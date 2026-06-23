<?php

namespace Modules\Order\Http\Controllers;

use App\Jobs\SendOrderPlacedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\Http\Requests\SiteOrderValidate;
use Modules\Order\Models\Order;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Support\Http\Controllers\SiteController;

class SiteOrderController extends SiteController
{
    public function store(SiteOrderValidate $request, MetaConversionApiService $metaConversionApiService)
    {
        DB::beginTransaction();

        try {
            $orderData = $request->validated();
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = Order::create($orderData);

            $orderProducts = collect($items)->map(function ($item) use ($order) {
                $unitPrice = $item['item']['price'];
                $quantity = $item['quantity'];
                $discount = 0;
                $totalPrice = ($unitPrice * $quantity) - $discount;

                return [
                    'order_id' => $order->id,
                    'product_id' => $item['item']['id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'total_price' => $totalPrice,
                ];
            })->toArray();

            $order->orderProducts()->createMany($orderProducts);

            DB::commit();

            // Dispatch admin notification email (async via queue)
            $adminEmail = setting('general.admin_email');
            if ($adminEmail) {
                SendOrderPlacedMail::dispatch($order->id, $adminEmail);
            }

            $this->trackPurchaseEvent($request, $order, $orderProducts, $metaConversionApiService);

            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
            ], 201);
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
        $order = Order::with(['orderProducts.product'])->findOrFail($id);

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
            'consent_granted' => $request->cookie('tracking_consent') === 'granted',
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
}
