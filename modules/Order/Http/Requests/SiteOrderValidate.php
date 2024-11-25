<?php

namespace Modules\Order\Http\Requests;

use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Union;
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
            'division_id' => [
                'nullable',
                'integer',
                Rule::exists(Division::class, 'id'),
            ],
            'district_id' => [
                'nullable',
                'integer',
                Rule::exists(District::class, 'id'),
            ],
            'upazila_id' => [
                'nullable',
                'integer',
                Rule::exists(District::class, 'id'),
            ],
            'union_id' => [
                'nullable',
                'integer',
                Rule::exists(Union::class, 'id'),
            ],

            // Address and payment
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
}
