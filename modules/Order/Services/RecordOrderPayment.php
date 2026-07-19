<?php

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Models\OrderPayment;
use Modules\Support\Events\OrderPaymentConfirmed;

class RecordOrderPayment
{
    public function run(Order $order, array $data): ?OrderPayment
    {
        if ($order->orderPayments()->where('payment_status', 'success')->exists()) {
            return null;
        }

        $payment = $order->orderPayments()->create([
            'payment_method' => $data['payment_method'] ?? $order->payment_method ?? 'cod',
            'payment_status' => $data['payment_status'] ?? 'success',
            'amount_paid' => $data['amount_paid'] ?? $order->total,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'transaction_id' => $data['transaction_id'] ?? null,
        ]);

        if ($payment->payment_status === 'success') {
            $items = $order->orderProducts()
                ->with('product')
                ->get()
                ->map(fn ($op) => [
                    'product_id' => $op->product_id,
                    'order_product_id' => $op->id,
                    'quantity' => $op->quantity,
                    'is_downloadable' => $op->product?->is_downloadable ?? false,
                ])
                ->toArray();

            event(new OrderPaymentConfirmed(
                orderId: $order->id,
                customerId: $order->customer_id,
                customerEmail: $order->email ?? '',
                items: $items,
            ));

            if (! $order->requires_shipping) {
                $order->updateQuietly(['status' => 'completed']);
            }
        }

        return $payment;
    }
}
