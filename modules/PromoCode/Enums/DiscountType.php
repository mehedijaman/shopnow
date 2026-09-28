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
            self::Percentage => __('promo-code::admin.percentage'),
            self::FixedAmount => __('promo-code::admin.fixed_amount'),
            self::FreeShipping => __('promo-code::admin.free_shipping'),
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
