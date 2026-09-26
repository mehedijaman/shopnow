<?php

namespace Modules\PromoCode\Services;

use Modules\PromoCode\Enums\DiscountType;
use Modules\PromoCode\Models\PromoCode;

class CalculatePromoDiscount
{
    /**
     * Order-level discount for the given cart subtotal (shipping is waived separately).
     */
    public function run(PromoCode $promoCode, float $subtotal): float
    {
        $discount = match ($promoCode->discount_type) {
            DiscountType::Percentage => $subtotal * ((float) $promoCode->discount_value / 100),
            DiscountType::FixedAmount => (float) $promoCode->discount_value,
            DiscountType::FreeShipping => 0,
        };

        if ($promoCode->discount_type === DiscountType::Percentage && $promoCode->maximum_discount_amount !== null) {
            $discount = min($discount, (float) $promoCode->maximum_discount_amount);
        }

        return round(min($discount, $subtotal), 2);
    }
}
