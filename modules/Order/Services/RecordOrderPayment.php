<?php

namespace Modules\Order\Services;

use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\PaymentMethod;
use Modules\Order\Enums\TransactionStatus;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderPayment;
use Modules\Support\Events\OrderPaymentConfirmed;

class RecordOrderPayment
{
    public function run(Order $order, array $data): ?OrderPayment
    {
        if ($order->orderPayments()->where('payment_status', TransactionStatus::Success)->exists()) {
            return null;
        }

        $requestedMethod = $data['payment_method'] ?? $order->payment_method ?? null;
        if ($requestedMethod instanceof PaymentMethod) {
            $paymentMethod = $requestedMethod;
        } else {
            $paymentMethod = PaymentMethod::tryFrom((string) $requestedMethod) ?? PaymentMethod::Cod;
        }

        $payment = $order->orderPayments()->create([
            'payment_method' => $paymentMethod,
            'payment_status' => $data['payment_status'] ?? TransactionStatus::Success,
            'amount_paid' => $data['amount_paid'] ?? $order->total,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'transaction_id' => $data['transaction_id'] ?? null,
        ]);

        if ($payment->payment_status === TransactionStatus::Success) {
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
                $order->updateQuietly(['status' => OrderStatus::Completed]);
            }
        }

        return $payment;
    }
}
