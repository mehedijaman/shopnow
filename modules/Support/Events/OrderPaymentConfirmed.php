<?php

namespace Modules\Support\Events;

final class OrderPaymentConfirmed
{
    /**
     * @param  array<int, array{product_id:int, order_product_id:int, quantity:int, is_downloadable:bool}>  $items
     */
    public function __construct(
        public readonly int $orderId,
        public readonly ?int $customerId,
        public readonly string $customerEmail,
        public readonly array $items,
    ) {}
}
