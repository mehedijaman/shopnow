<?php

namespace Modules\Order\Http\Controllers;

use Inertia\Response;
use Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Modules\Order\Http\Requests\OrderValidate;
use Modules\Order\Http\Requests\SiteOrderValidate;
use Modules\Support\Http\Controllers\SiteController;

class SiteOrderController extends SiteController
{
    // public function index(): Response
    // {
    //     $orders = Order::orderBy('name')
    //         ->search(request('searchContext'), request('searchTerm'))
    //         ->paginate(request('rowsPerPage', 10))
    //         ->withQueryString()
    //         ->through(fn($order) => [
    //             'id' => $order->id,
    //             'name' => $order->name,
    //             'created_at' => $order->created_at->format('d/m/Y H:i') . 'h',
    //         ]);

    //     return inertia('Order/OrderIndex', [
    //         'orders' => $orders,
    //     ]);
    // }

    public function store(SiteOrderValidate $request)
    {
        DB::beginTransaction();

        try {
            // Extract and clean the request data
            $orderData = $request->validated();
            $items = $orderData['items']; // Extract items
            unset($orderData['items']);  // Remove items before creating the order

            // Create the order
            $order = Order::create($orderData);

            // Prepare order_products data and associate them with the order
            $orderProducts = collect($items)->map(function ($item) use ($order) {
                $unitPrice = $item['item']['price'];
                $quantity = $item['quantity'];
                $discount = 0; // Default discount
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

            // Save related products using the correct relationship
            $order->orderProducts()->createMany($orderProducts);

            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $order->id
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error to check details in case of failure
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'order_data' => $orderData,
                'items' => $items,
                'error' => $e
            ]);

            return response()->json([
                'message' => 'Failed to place the order. Please try again later.',
            ], 500);
        }
    }





    // public function edit(int $id): Response
    // {
    //     $order = Order::find($id);

    //     return inertia('Order/OrderForm', [
    //         'order' => $order,
    //     ]);
    // }

    // public function update(OrderValidate $request, int $id): RedirectResponse
    // {
    //     $order = Order::findOrFail($id);

    //     $order->update($request->validated());

    //     return redirect()->route('order.index')
    //         ->with('success', 'Order updated.');
    // }

    // public function destroy(int $id): RedirectResponse
    // {
    //     Order::findOrFail($id)->delete();

    //     return redirect()->route('order.index')
    //         ->with('success', 'Order deleted.');
    // }
}
