<?php

namespace Modules\Order\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('order::enums.shipment_status.pending'),
            self::Processing => __('order::enums.shipment_status.processing'),
            self::Shipped => __('order::enums.shipment_status.shipped'),
            self::Delivered => __('order::enums.shipment_status.delivered'),
            self::Cancelled => __('order::enums.shipment_status.cancelled'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
