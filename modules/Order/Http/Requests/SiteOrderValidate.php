<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class SiteOrderValidate extends Request
{
    public function rules(): array
    {
        return [
            // Basic customer info
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'required|string|max:255',

            // Geographic info
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'union' => 'nullable|string|max:255',
            'division_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'upazila_id' => 'nullable|integer',
            'union_id' => 'nullable|integer',

            // Address and payment
            'selected_address_id' => 'nullable|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'payment_method' => [
                'nullable',
                Rule::in(['cod', 'sslcommerz']),
            ],

            // Optional financial info (nullable but numeric if present)
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'paid' => 'nullable|numeric',
            'due' => 'nullable|numeric',

            // Items (product array)
            'items' => 'required|array',

        ];
    }

    /**
     * Custom messages for validation rules.
     */
    public function messages()
    {
        return [
            'items.*.item.id.exists' => 'The selected product does not exist.',
            'items.*.quantity.min' => 'Each item must have a quantity of at least 1.',
            'subtotal.numeric' => 'Subtotal must be a valid number.',
            'tax.numeric' => 'Tax must be a valid number.',
            'shipping.numeric' => 'Shipping must be a valid number.',
            'total.numeric' => 'Total must be a valid number.',
            'paid.numeric' => 'Paid amount must be a valid number.',
            'due.numeric' => 'Due amount must be a valid number.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('SiteOrderValidate failed validation', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all(),
        ]);

        parent::failedValidation($validator);
    }
}
