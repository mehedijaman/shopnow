<?php

namespace Modules\Order\Http\Controllers;

use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Http\Requests\SiteOrderValidate;
use Modules\Order\Models\Order;
use Modules\Support\Http\Controllers\SiteController;

class SiteOrderController extends SiteController
{
    public function store(SiteOrderValidate $request)
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

            // Send admin notification email
            $adminEmail = setting('general.admin_email');
            if ($adminEmail) {
                $order->load('orderProducts.product');
                Mail::to($adminEmail)->send(new OrderPlacedMail($order));
            }

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
}
