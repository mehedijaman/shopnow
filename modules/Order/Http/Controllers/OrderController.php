<?php

namespace Modules\Order\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Modules\Customer\Models\Customer;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\PaymentMethod;
use Modules\Order\Enums\PaymentStatus;
use Modules\Order\Enums\TransactionStatus;
use Modules\Order\Http\Requests\OrderValidate;
use Modules\Order\Http\Requests\UpdateOrderValidate;
use Modules\Order\Models\Order;
use Modules\Order\Services\CreateOrderService;
use Modules\Order\Services\RecordOrderPayment;
use Modules\Order\Services\UpdateOrderService;
use Modules\Product\Models\Product;
use Modules\PromoCode\Services\CalculatePromoDiscount;
use Modules\PromoCode\Services\ValidatePromoCode;
use Modules\Support\Http\Controllers\BackendController;

class OrderController extends BackendController
{
    public function index(Request $request): Response
    {
        $statusFilter = $request->input('status');
        $paymentFilter = $request->input('payment_status');
        $paymentMethodFilter = $request->input('payment_method');

        $orders = Order::orderBy('id', 'desc')
            ->search($request->input('searchContext'), $request->input('searchTerm'))
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($paymentFilter, fn ($q) => $q->where('payment_status', $paymentFilter))
            ->when($paymentMethodFilter, fn ($q) => $q->where('payment_method', $paymentMethodFilter))
            ->paginate($request->input('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($order) => [
                'id' => $order->id,
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'status' => $order->status->value,
                'payment_status' => $order->payment_status->value,
                'payment_method' => $order->payment_method,
                'total' => $order->total,
                'created_at' => $order->created_at->format('d M Y'),
            ]);

        // Single grouped query for all status counts
        $statusCounts = Order::selectRaw('status, count(*) as count')
            ->whereIn('status', OrderStatus::values())
            ->groupBy('status')
            ->pluck('count', 'status');

        return inertia('Order/OrderIndex', [
            'orders' => $orders,
            'statuses' => OrderStatus::values(),
            'statusCounts' => $statusCounts,
            'paymentMethods' => Order::whereNotNull('payment_method')
                ->distinct()
                ->pluck('payment_method')
                ->sort()
                ->values(),
            'filters' => [
                'status' => $statusFilter,
                'payment_status' => $paymentFilter,
                'payment_method' => $paymentMethodFilter,
                'searchTerm' => $request->input('searchTerm'),
            ],
        ]);
    }

    public function create(): Response
    {
        return inertia('Order/OrderForm', [
            'products' => $this->productsForPicker(),
            'statuses' => OrderStatus::values(),
            'paymentMethods' => collect(PaymentMethod::cases())
                ->map(fn (PaymentMethod $method) => [
                    'value' => $method->value,
                    'label' => $method->label(),
                ])
                ->values()
                ->all(),
            'customers' => Customer::query()
                ->orderBy('name')
                ->limit(200)
                ->get(['id', 'name', 'phone'])
                ->map(fn (Customer $customer) => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                ])
                ->values()
                ->all(),
        ]);
    }

    public function store(OrderValidate $request, CreateOrderService $createOrderService): RedirectResponse
    {
        $order = $createOrderService->run($request->validated());

        return redirect()->route('order.show', $order->id)
            ->with('success', 'Order created.');
    }

    public function show(int $id): Response
    {
        $order = Order::with([
            'orderProducts.product',
            'orderProducts.bundleItems',
            'orderShipments',
            'orderPayments',
        ])->findOrFail($id);

        return inertia('Order/OrderShow', [
            'order' => $this->transformOrder($order),
            'statuses' => OrderStatus::values(),
            'defaultCourier' => (string) (setting('courier.default_courier') ?: 'steadfast'),
        ]);
    }

    public function downloadInvoice(int $id)
    {
        $order = Order::with([
            'orderProducts.product',
            'orderProducts.bundleItems',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('order::invoice-pdf', [
            'order' => $order,
        ]);

        return $pdf->download('invoice-'.$order->id.'.pdf');
    }

    public function edit(int $id, UpdateOrderService $updateOrderService): Response
    {
        $order = Order::with([
            'orderProducts.product',
            'orderProducts.productVariation',
            'orderProducts.bundleItems',
            'orderShipments',
            'orderPayments',
        ])->findOrFail($id);

        return inertia('Order/OrderEdit', [
            'order' => $this->transformOrder($order),
            'products' => $this->productsForPicker(),
            'lockedItems' => $updateOrderService->itemsLocked($order),
        ]);
    }

    public function update(UpdateOrderValidate $request, UpdateOrderService $updateOrderService, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $updateOrderService->run($order, $request->validated());

        return redirect()->route('order.show', $order->id)
            ->with('success', 'Order updated.');
    }

    /**
     * Validate a coupon against the edited totals and return the discount
     * preview. Nothing is persisted; saving the order revalidates server-side.
     */
    public function applyCoupon(
        Request $request,
        ValidatePromoCode $validatePromoCode,
        CalculatePromoDiscount $calculatePromoDiscount,
        int $id,
    ): JsonResponse {
        return $this->couponResponse(
            $request,
            $validatePromoCode,
            $calculatePromoDiscount,
            Order::findOrFail($id),
        );
    }

    /**
     * Order-less coupon preview for the Create Order form: validates against
     * the posted subtotal (and optional linked customer) without an order.
     */
    public function couponPreview(
        Request $request,
        ValidatePromoCode $validatePromoCode,
        CalculatePromoDiscount $calculatePromoDiscount,
    ): JsonResponse {
        return $this->couponResponse($request, $validatePromoCode, $calculatePromoDiscount, null);
    }

    private function couponResponse(
        Request $request,
        ValidatePromoCode $validatePromoCode,
        CalculatePromoDiscount $calculatePromoDiscount,
        ?Order $order,
    ): JsonResponse {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'integer'],
        ]);

        $subtotal = round((float) ($validated['subtotal'] ?? $order?->subtotal ?? 0), 2);
        $customerId = $order ? $order->customer_id : ($validated['customer_id'] ?? null);

        $promoCode = $validatePromoCode->run(
            $validated['coupon_code'],
            $subtotal,
            $customerId,
            excludeOrderId: $order?->id,
        );

        return response()->json([
            'coupon_code' => $promoCode->code,
            'discount' => $calculatePromoDiscount->run($promoCode, $subtotal),
            'waives_shipping' => $promoCode->discount_type->isFreeShipping(),
        ]);
    }

    public function updateStatus(
        Request $request,
        RecordOrderPayment $recordOrderPayment,
        int $id,
    ): RedirectResponse {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'payment_status' => ['nullable', Rule::enum(PaymentStatus::class)],
        ]);

        if (PaymentStatus::tryFrom($validated['payment_status'] ?? '') === PaymentStatus::Paid) {
            $recordOrderPayment->run($order, [
                'payment_status' => TransactionStatus::Success->value,
            ]);
        }

        $order->update(array_filter($validated, fn ($v) => $v !== null));

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Order::findOrFail($id)->delete();

        return redirect()->route('order.index')
            ->with('success', 'Order deleted.');
    }

    /**
     * Shared order payload for OrderShow and OrderEdit.
     *
     * @return array<string, mixed>
     */
    private function transformOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'name' => $order->name,
            'email' => $order->email,
            'phone' => $order->phone,
            'address' => $order->address,
            'division' => $order->division,
            'district' => $order->district,
            'upazila' => $order->upazila,
            'union' => $order->union,
            'country' => $order->country,
            'status' => $order->status->value,
            'payment_status' => $order->payment_status->value,
            'payment_method' => $order->payment_method,
            'requires_shipping' => $order->requires_shipping,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'shipping' => $order->shipping,
            'shipping_method' => $order->shipping_method,
            'discount' => $order->discount,
            'coupon_code' => $order->coupon_code,
            'total' => $order->total,
            'paid' => $order->paid,
            'due' => $order->due,
            'notes' => $order->notes,
            'fraud_risk' => $order->fraud_risk,
            'fraud_checked_at' => $order->fraud_checked_at?->format('d M Y, h:i A'),
            'fraud_details' => $order->fraud_details,
            'created_at' => $order->created_at->format('d M Y, h:i A'),
            'orderProducts' => $order->orderProducts->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name ?? 'Product #'.$item->product_id,
                'product_type' => $item->product?->type?->value,
                'product_variation_id' => $item->product_variation_id,
                'variation_label' => $item->variation_label,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'total_price' => $item->total_price,
                'bundle_items' => $item->bundleItems->map(fn ($bi) => [
                    'id' => $bi->id,
                    'name' => $bi->name,
                    'sku' => $bi->sku,
                    'quantity' => $bi->quantity,
                    'unit_price' => $bi->unit_price,
                    'total_price' => $bi->total_price,
                ]),
            ]),
            'orderPayments' => $order->orderPayments->map(fn ($p) => [
                'id' => $p->id,
                'payment_method' => $p->payment_method?->value,
                'payment_status' => $p->payment_status->value,
                'amount_paid' => $p->amount_paid,
                'payment_date' => $p->payment_date ? date('d M Y, h:i A', strtotime($p->payment_date)) : null,
                'transaction_id' => $p->transaction_id,
            ]),
            'orderShipments' => $order->orderShipments->map(fn ($shipment) => [
                'id' => $shipment->id,
                'tracking_number' => $shipment->tracking_number,
                'tracking_url' => $shipment->tracking_url,
                'carrier' => $shipment->carrier,
                'shopment_status' => $shipment->shopment_status->value,
                'shipment_date' => $shipment->shipment_date,
                'estimated_delivery' => $shipment->estimated_delivery,
                'actual_delivery' => $shipment->actual_delivery,
                'consignment_id' => $shipment->consignment_id,
                'courier_status' => $shipment->courier_status,
                'booked_at' => $shipment->booked_at?->format('d M Y, h:i A'),
                'last_synced_at' => $shipment->last_synced_at?->format('d M Y, h:i A'),
                'booking_error' => $shipment->booking_error,
            ]),
            'downloadPermissions' => DB::table('download_permissions')
                ->leftJoin('product_files', 'download_permissions.product_file_id', '=', 'product_files.id')
                ->leftJoin('products', 'download_permissions.product_id', '=', 'products.id')
                ->where('download_permissions.order_id', $order->id)
                ->select([
                    'download_permissions.id',
                    'product_files.name as product_file_name',
                    'products.name as product_name',
                    'download_permissions.product_id',
                    'download_permissions.download_count',
                    'download_permissions.download_limit',
                    'download_permissions.expires_at',
                    'download_permissions.active',
                    'download_permissions.created_at',
                ])
                ->get()
                ->map(fn ($dp) => [
                    'id' => $dp->id,
                    'product_file_name' => $dp->product_file_name ?? 'File #'.$dp->product_file_id,
                    'product_name' => $dp->product_name,
                    'product_id' => $dp->product_id,
                    'download_count' => $dp->download_count,
                    'download_limit' => $dp->download_limit,
                    'expires_at' => $dp->expires_at ? date('d M Y', strtotime($dp->expires_at)) : null,
                    'active' => (bool) $dp->active,
                    'created_at' => date('d M Y', strtotime($dp->created_at)),
                ]),
        ];
    }

    /**
     * Catalog for the OrderEdit item picker: products with active variations
     * and bundle definitions so the form can price rows client-side.
     *
     * @return array<int, array<string, mixed>>
     */
    private function productsForPicker(): array
    {
        return Product::query()
            ->with([
                'variations' => fn ($q) => $q->where('active', true)->with('attributeValues.attribute'),
                'bundleItems.childProduct',
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'price', 'sale_price', 'quantity', 'is_virtual'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type->value,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity,
                'is_virtual' => (bool) $product->is_virtual,
                'variations' => $product->variations->map(fn ($variation) => [
                    'id' => $variation->id,
                    'label' => $variation->label(),
                    'price' => $variation->price,
                    'sale_price' => $variation->sale_price,
                    'quantity' => $variation->quantity,
                ]),
                'bundle_items' => $product->bundleItems->map(fn ($bundleItem) => [
                    'name' => $bundleItem->childProduct?->name ?? "Product #{$bundleItem->child_product_id}",
                    'quantity' => $bundleItem->quantity,
                ]),
            ])
            ->all();
    }
}
