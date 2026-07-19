<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Modules\Order\Http\Requests\OrderValidate;
use Modules\Order\Models\Order;
use Modules\Order\Services\RecordOrderPayment;
use Modules\Support\Http\Controllers\BackendController;

class OrderController extends BackendController
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];

    public function index(Request $request): Response
    {
        $statusFilter = $request->input('status');
        $paymentFilter = $request->input('payment_status');

        $orders = Order::orderBy('id', 'desc')
            ->search($request->input('searchContext'), $request->input('searchTerm'))
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($paymentFilter, fn ($q) => $q->where('payment_status', $paymentFilter))
            ->paginate($request->input('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($order) => [
                'id' => $order->id,
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total' => $order->total,
                'created_at' => $order->created_at->format('d M Y'),
            ]);

        // Single grouped query for all status counts
        $statusCounts = Order::selectRaw('status, count(*) as count')
            ->whereIn('status', self::STATUSES)
            ->groupBy('status')
            ->pluck('count', 'status');

        return inertia('Order/OrderIndex', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $statusFilter,
                'payment_status' => $paymentFilter,
                'searchTerm' => $request->input('searchTerm'),
            ],
        ]);
    }

    public function create(): Response
    {
        return inertia('Order/OrderForm');
    }

    public function store(OrderValidate $request): RedirectResponse
    {
        Order::create($request->validated());

        return redirect()->route('order.index')
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
            'order' => [
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
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'requires_shipping' => $order->requires_shipping,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'shipping' => $order->shipping,
                'total' => $order->total,
                'paid' => $order->paid,
                'due' => $order->due,
                'notes' => $order->notes,
                'created_at' => $order->created_at->format('d M Y, h:i A'),
                'orderProducts' => $order->orderProducts->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name ?? 'Product #'.$item->product_id,
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
                    'payment_method' => $p->payment_method,
                    'payment_status' => $p->payment_status,
                    'amount_paid' => $p->amount_paid,
                    'payment_date' => $p->payment_date ? date('d M Y, h:i A', strtotime($p->payment_date)) : null,
                    'transaction_id' => $p->transaction_id,
                ]),
                'orderShipments' => $order->orderShipments->map(fn ($shipment) => [
                    'id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'tracking_url' => $shipment->tracking_url,
                    'carrier' => $shipment->carrier,
                    'shopment_status' => $shipment->shopment_status,
                    'shipment_date' => $shipment->shipment_date,
                    'estimated_delivery' => $shipment->estimated_delivery,
                    'actual_delivery' => $shipment->actual_delivery,
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
            ],
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(int $id): Response
    {
        $order = Order::find($id);

        return inertia('Order/OrderForm', [
            'order' => $order,
        ]);
    }

    public function update(OrderValidate $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $order->update($request->validated());

        return redirect()->route('order.index')
            ->with('success', 'Order updated.');
    }

    public function updateStatus(
        Request $request,
        RecordOrderPayment $recordOrderPayment,
        int $id,
    ): RedirectResponse {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'payment_status' => ['nullable', Rule::in(['paid', 'unpaid'])],
        ]);

        if (($validated['payment_status'] ?? null) === 'paid') {
            $recordOrderPayment->run($order, [
                'payment_status' => 'success',
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
}
