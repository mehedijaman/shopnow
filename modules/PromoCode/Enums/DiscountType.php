<?php

namespace Modules\PromoCode\Enums;

enum DiscountType: string
{
    case Percentage = 'percentage';
    case FixedAmount = 'fixed_amount';
    case FreeShipping = 'free_shipping';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Percentage',
            self::FixedAmount => 'Fixed Amount',
            self::FreeShipping => 'Free Shipping',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isFreeShipping(): bool
    {
        return $this === self::FreeShipping;
    }
}
