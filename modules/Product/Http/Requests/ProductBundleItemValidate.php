<?php

namespace Modules\Product\Http\Requests;

use Modules\Support\Http\Requests\Request;

class ProductBundleItemValidate extends Request
{
    public function rules(): array
    {
        return [
            'child_product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'is_optional' => 'nullable|boolean',
            'price_override' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
