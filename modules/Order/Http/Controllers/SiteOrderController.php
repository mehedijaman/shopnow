<?php

namespace Modules\Order\Http\Controllers;

use App\Jobs\SendCustomerOrderConfirmationMail;
use App\Jobs\SendOrderPlacedMail;
use App\Models\User;
use Devfaysal\BangladeshGeocode\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Courier\Jobs\CheckOrderFraud;
use Modules\Order\Enums\PaymentMethod;
use Modules\Order\Enums\ShipmentStatus;
use Modules\Order\Http\Requests\SiteOrderValidate;
use Modules\Order\Models\Order;
use Modules\Order\Services\DetermineShippingRequirement;
use Modules\Product\Enums\ProductType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\PromoCode\Services\CalculatePromoDiscount;
use Modules\PromoCode\Services\ValidatePromoCode;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Support\Http\Controllers\SiteController;

class SiteOrderController extends SiteController
{
    public function store(
        SiteOrderValidate $request,
        DetermineShippingRequirement $determineShippingRequirement,
        ValidatePromoCode $validatePromoCode,
        CalculatePromoDiscount $calculatePromoDiscount,
        MetaConversionApiService $metaConversionApiService,
    ) {
        DB::beginTransaction();

        try {
            $orderData = $request->validated();
            $items = $orderData['items'];
            unset($orderData['items']);

            $divisionId = $orderData['division_id'] ?? null;
            $districtId = $orderData['district_id'] ?? null;
            $upazilaId = $orderData['upazila_id'] ?? null;
            $unionId = $orderData['union_id'] ?? null;
            $selectedAddressId = $orderData['selected_address_id'] ?? null;

            unset($orderData['division_id'], $orderData['district_id'], $orderData['upazila_id'], $orderData['union_id'], $orderData['selected_address_id']);

            $requiresShipping = $determineShippingRequirement->run($items);

            // Resolve unit prices server-side and recompute the trusted subtotal
            $lines = collect($items)->map(function ($item) {
                $productId = $item['item']['id'];
                $productVariationId = $item['item']['product_variation_id'] ?? null;

                $product = Product::find($productId);
                $isBundle = $product && $product->type === ProductType::Bundle;
                $variation = null;

                if ($isBundle) {
                    $unitPrice = (float) ($item['item']['price'] ?? $product?->sale_price ?? $product?->price ?? 0);
                } elseif ($productVariationId) {
                    $variation = ProductVariation::where('id', $productVariationId)
                        ->where('product_id', $productId)
                        ->where('active', true)
                        ->first();

                    $unitPrice = $variation
                        ? ($variation->sale_price ? (float) $variation->sale_price : (float) $variation->price)
                        : (float) ($product?->sale_price ?? $product?->price ?? 0);
                } else {
                    $unitPrice = (float) ($product?->sale_price ?? $product?->price ?? 0);
                }

                return [
                    'item' => $item,
                    'product' => $product,
                    'is_bundle' => $isBundle,
                    'variation' => $variation,
                    'unit_price' => $unitPrice,
                ];
            });

            $subtotal = round($lines->sum(fn ($line) => $line['unit_price'] * (int) $line['item']['quantity']), 2);

            // Promo code — locked for the duration of the order transaction
            $promoCode = null;
            $discount = 0;
            if ($orderData['coupon_code'] ?? null) {
                $promoCode = $validatePromoCode->run(
                    $orderData['coupon_code'],
                    $subtotal,
                    Auth::guard('customer')->id(),
                    lock: true,
                );
                $discount = $calculatePromoDiscount->run($promoCode, $subtotal);
            }

            // Resolve shipping cost server-side from settings
            $rawOptions = setting('shipping.options', '[]');
            $shippingOptions = collect(is_array($rawOptions) ? $rawOptions : (json_decode($rawOptions, true) ?? []));
            $selectedOption = ($orderData['shipping_method'] ?? null)
                ? $shippingOptions->firstWhere('name', $orderData['shipping_method'])
                : null;
            $freeShippingThreshold = (float) setting('shipping.free_shipping_threshold', 1000);

            $shippingCost = 0;
            if ($selectedOption && $requiresShipping) {
                $waivesShipping = $promoCode && $promoCode->discount_type->isFreeShipping();
                $shippingCost = ($waivesShipping || ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold))
                    ? 0
                    : (float) $selectedOption['price'];
            }

            $tax = (float) ($orderData['tax'] ?? 0);
            $total = round($subtotal + $tax + $shippingCost - $discount, 2);
            $paid = (float) ($orderData['paid'] ?? 0);
            $due = max(0, round($total - $paid, 2));

            $order = Order::create(array_merge($orderData, [
                'requires_shipping' => $requiresShipping,
                'shipping' => $shippingCost,
                'shipping_method' => $selectedOption ? $selectedOption['name'] : null,
                'customer_id' => Auth::guard('customer')->id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $promoCode?->code,
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
            ]));

            $createdOrderProducts = $lines->map(function ($line) use ($order) {
                $item = $line['item'];
                $quantity = $item['quantity'];
                $itemDiscount = 0;
                $variationLabel = null;

                $productId = $item['item']['id'];
                $unitPrice = $line['unit_price'];
                $product = $line['product'];
                $isBundle = $line['is_bundle'];
                $variation = $line['variation'];
                $variationId = $variation?->id;

                if ($variation) {
                    $variationLabel = $item['item']['variation_label'] ?? null;

                    if ($variation->quantity >= $quantity) {
                        $variation->decrement('quantity', $quantity);
                    }
                }

                $totalPrice = ($unitPrice * $quantity) - $itemDiscount;

                $orderProduct = $order->orderProducts()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'variation_label' => $variationLabel,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $itemDiscount,
                    'total_price' => $totalPrice,
                ]);

                if ($isBundle) {
                    $bundleChildren = $product->bundleItems()->with('childProduct')->get();

                    foreach ($bundleChildren as $bi) {
                        $childUnitPrice = (float) ($bi->price_override ?? $bi->childProduct?->sale_price ?? $bi->childProduct?->price ?? 0);

                        $orderProduct->bundleItems()->create([
                            'product_id' => $bi->child_product_id,
                            'product_variation_id' => $bi->child_product_variation_id,
                            'name' => $bi->childProduct?->name ?? "Product #{$bi->child_product_id}",
                            'sku' => $bi->childProduct?->sku,
                            'quantity' => $bi->quantity * $quantity,
                            'unit_price' => $childUnitPrice,
                            'total_price' => $childUnitPrice * $bi->quantity * $quantity,
                        ]);

                        if ($bi->childProduct && $bi->childProduct->quantity >= $bi->quantity * $quantity) {
                            $bi->childProduct->decrement('quantity', $bi->quantity * $quantity);
                        }
                    }
                }

                return $orderProduct;
            });

            if ($requiresShipping) {
                $order->orderShipments()->create([
                    'shopment_status' => ShipmentStatus::Pending,
                ]);
            }

            $customer = Auth::guard('customer')->user();
            if ($customer && $requiresShipping) {
                $isNewAddress = ($selectedAddressId === 'new') || ($customer->addresses()->count() === 0);

                if ($isNewAddress && $districtId && $orderData['address']) {
                    if (! $divisionId) {
                        $district = District::find($districtId);
                        $divisionId = $district?->division_id;
                    }

                    $isFirstAddress = $customer->addresses()->count() === 0;

                    $customer->addresses()->create([
                        'name' => $orderData['name'],
                        'phone' => $orderData['phone'],
                        'division_id' => $divisionId,
                        'district_id' => $districtId,
                        'upazilla_id' => $upazilaId,
                        'union_id' => $unionId,
                        'address' => $orderData['address'],
                        'country' => 'Bangladesh',
                        'default' => $isFirstAddress,
                    ]);
                }
            }

            DB::commit();

            $normalizedPhone = preg_replace('/[^0-9]/', '', (string) ($order->phone ?? ''));
            if ($order->payment_method === PaymentMethod::Cod->value
                && $order->requires_shipping
                && setting('courier.fraud_enabled')
                && preg_match('/^01[3-9][0-9]{8}$/', $normalizedPhone)) {
                CheckOrderFraud::dispatch($order->id);
            }

            // Dispatch admin notification email (async via queue)
            $adminEmail = setting('general.admin_email');
            if (! $this->isValidRealEmail($adminEmail)) {
                $adminEmail = config('mail.from.address');
            }
            if (! $this->isValidRealEmail($adminEmail)) {
                $adminEmail = env('ADMIN_EMAIL');
            }
            if (! $this->isValidRealEmail($adminEmail)) {
                $userEmail = User::first()?->email;
                $adminEmail = $this->isValidRealEmail($userEmail) ? $userEmail : null;
            }

            if ($adminEmail && $this->isValidRealEmail($adminEmail)) {
                SendOrderPlacedMail::dispatch($order->id, $adminEmail);
            }

            // Dispatch customer order confirmation email (async via queue)
            if ($order->email) {
                SendCustomerOrderConfirmationMail::dispatch($order->id);
            }

            $orderProductsData = $createdOrderProducts->map(fn ($op) => [
                'product_id' => $op->product_id,
                'quantity' => $op->quantity,
                'unit_price' => $op->unit_price,
            ])->toArray();

            $this->trackPurchaseEvent($request, $order, $orderProductsData, $metaConversionApiService);

            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed: '.$e->getMessage(), [
                'order_data' => $orderData ?? [],
                'items' => $items ?? [],
                'error' => $e,
            ]);

            return response()->json([
                'message' => 'Failed to place the order. Please try again later.',
            ], 500);
        }
    }

    public function confirm(int $id)
    {
        $order = Order::with(['orderProducts.product', 'orderProducts.bundleItems'])->findOrFail($id);

        return view('order::order-confirm', compact('order'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $orderProducts
     */
    private function trackPurchaseEvent(
        Request $request,
        Order $order,
        array $orderProducts,
        MetaConversionApiService $metaConversionApiService
    ): void {
        $names = preg_split('/\s+/', trim((string) $order->name)) ?: [];
        $firstName = $names[0] ?? null;
        $lastName = count($names) > 1 ? end($names) : null;

        $metaConversionApiService->track('Purchase', [
            'currency' => 'BDT',
            'value' => (float) $order->total,
            'order_id' => (string) $order->id,
            'content_type' => 'product',
            'content_ids' => array_values(array_map(static fn ($item) => (string) ($item['product_id'] ?? ''), $orderProducts)),
            'contents' => array_values(array_map(static fn ($item) => [
                'id' => (string) ($item['product_id'] ?? ''),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'item_price' => (float) ($item['unit_price'] ?? 0),
            ], $orderProducts)),
            'num_items' => array_sum(array_map(static fn ($item) => (int) ($item['quantity'] ?? 0), $orderProducts)),
        ], [
            'event_id' => 'purchase_'.$order->id,
            'event_source_url' => url('/order-confirm/'.$order->id),
            'consent_granted' => true,
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
            'email' => $order->email,
            'phone' => $order->phone,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'external_id' => (string) $order->id,
        ]);
    }

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $orders = Order::where('customer_id', $customer->id)
            ->with('orderShipments')
            ->orderByDesc('id')
            ->paginate(10);

        return view('order::my-orders', compact('orders', 'customer'));
    }

    private function isValidRealEmail(?string $email): bool
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return true;
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));
        $dummyDomains = ['example.com', 'example.org', 'example.net', 'localhost', 'test.com'];

        return ! in_array($domain, $dummyDomains, true);
    }
}
