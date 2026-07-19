<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class ProductVariationValidate extends Request
{
    public function rules(): array
    {
        return [
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('product_variations', 'sku')->ignore($this->route('variation')),
            ],
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:5120',
            'remove_image' => 'nullable|boolean',
        ];
    }
}
