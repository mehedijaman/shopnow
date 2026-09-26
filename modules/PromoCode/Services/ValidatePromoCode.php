<?php

namespace Modules\PromoCode\Services;

use Illuminate\Validation\ValidationException;
use Modules\PromoCode\Models\PromoCode;

class ValidatePromoCode
{
    /**
     * Resolve a promo code and ensure it may be applied to the given cart.
     *
     * @param  bool  $lock  lock the promo row and usage rows while inside a transaction (order placement)
     *
     * @throws ValidationException
     */
    public function run(string $code, float $subtotal, ?int $customerId = null, bool $lock = false): PromoCode
    {
        $query = PromoCode::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        $promoCode = $query->where('code', PromoCode::normalize($code))->first();

        if (! $promoCode) {
            $this->fail('Promo code not found.');
        }

        $error = $promoCode->validationError($subtotal, $customerId, $lock);

        if ($error !== null) {
            $this->fail($error);
        }

        return $promoCode;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['coupon_code' => $message]);
    }
}
