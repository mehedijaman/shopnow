<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('order::enums.order_status.pending'),
            self::Processing => __('order::enums.order_status.processing'),
            self::Shipped => __('order::enums.order_status.shipped'),
            self::Delivered => __('order::enums.order_status.delivered'),
            self::Completed => __('order::enums.order_status.completed'),
            self::Cancelled => __('order::enums.order_status.cancelled'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isComplete(): bool
    {
        return $this === self::Completed || $this === self::Delivered;
    }
}
