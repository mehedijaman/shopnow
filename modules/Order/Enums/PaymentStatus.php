<?php

namespace Modules\Order\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Paid',
            self::Unpaid => 'Unpaid',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isPaid(): bool
    {
        return $this === self::Paid;
    }
}
