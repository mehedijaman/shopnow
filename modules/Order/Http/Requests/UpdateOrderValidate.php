<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class UpdateOrderValidate extends Request
{
    public function rules(): array
    {
        return [
            // Customer (order is a snapshot — no customer_addresses sync)
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'union' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',

            // Totals
            'shipping' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',

            // Line items — absence means "customer-only" edit;
            // an empty array is rejected by the service when the order has items.
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.product_variation_id' => ['nullable', 'integer', Rule::exists('product_variations', 'id')->whereNull('deleted_at')],
            'items.*.quantity' => 'required|integer|min:1|max:100000',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ];
    }
}
