<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

class CreateOrderService
{
    public function __construct(
        private UpdateOrderService $updateOrderService,
    ) {}

    /**
     * Create an admin-placed order. The order row is created as a pending
     * draft, then UpdateOrderService syncs line items (stock for variations
     * and bundle children, mirroring edit/checkout), customer snapshot,
     * coupon/discount, totals and the pending shipment. The requested status
     * is applied last so a shipped/completed status never trips the edit lock.
     *
     * @param  array<string, mixed>  $data
     */
    public function run(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $paid = round((float) ($data['paid'] ?? 0), 2);

            $order = Order::create([
                'customer_id' => $data['customer_id'] ?? null,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'paid' => $paid,
                'status' => OrderStatus::Pending,
            ]);

            $this->updateOrderService->run($order, [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'shipping' => $data['shipping'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'coupon_code' => $data['coupon_code'] ?? '',
                'items' => $data['items'],
            ]);

            $status = OrderStatus::tryFrom((string) ($data['status'] ?? '')) ?? OrderStatus::Pending;

            if ($status !== OrderStatus::Pending) {
                $order->update(['status' => $status]);
            }

            return $order->refresh();
        });
    }
}
