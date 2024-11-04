<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Order\Http\Requests\OrderValidate;
use Modules\Order\Models\Order;
use Modules\Support\Http\Controllers\BackendController;

class OrderController extends BackendController
{
    public function index(): Response
    {
        $orders = Order::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($order) => [
                'id' => $order->id,
                'name' => $order->name,
                'created_at' => $order->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Order/OrderIndex', [
            'orders' => $orders,
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

    public function destroy(int $id): RedirectResponse
    {
        Order::findOrFail($id)->delete();

        return redirect()->route('order.index')
            ->with('success', 'Order deleted.');
    }
}
