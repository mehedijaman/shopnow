<?php

namespace Modules\PromoCode\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\PromoCode\Enums\DiscountType;
use Modules\Support\Http\Requests\Request;

class PromoCodeValidate extends Request
{
    protected function prepareForValidation(): void
    {
        $code = $this->input('code');

        if (is_string($code)) {
            $this->merge(['code' => strtoupper(trim($code))]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('promo_codes', 'code')->ignore($this->route('id')),
            ],
            'discount_type' => ['required', Rule::enum(DiscountType::class)],
            'discount_value' => [
                'required_unless:discount_type,'.DiscountType::FreeShipping->value,
                'numeric',
                'min:0',
                $this->discount_type === DiscountType::Percentage->value ? 'max:100' : 'max:10000000',
            ],
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'The promo code may only contain letters, numbers, dashes and underscores.',
            'discount_value.max' => 'Percentage discounts cannot exceed 100.',
        ];
    }
}
