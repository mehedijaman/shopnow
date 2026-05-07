<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Modules\Order\Http\Requests\OrderValidate;
use Modules\Order\Models\Order;
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

        // Status counts for tab badges
        $statusCounts = collect(self::STATUSES)->mapWithKeys(
            fn ($s) => [$s => Order::where('status', $s)->count()]
        );

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
        $order = Order::with(['orderProducts.product'])->findOrFail($id);

        return inertia('Order/OrderShow', [
            'order' => [
                'id' => $order->id,
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'country' => $order->country,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
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
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'total_price' => $item->total_price,
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

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'payment_status' => ['nullable', Rule::in(['paid', 'unpaid'])],
        ]);

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
