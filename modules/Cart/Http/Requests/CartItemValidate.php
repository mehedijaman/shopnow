<?php

namespace Modules\Cart\Http\Requests;

use Modules\Support\Http\Requests\JsonRequest;

class CartItemValidate extends JsonRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'product_variation_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1',
            'bundle_selection' => 'nullable|array',
        ];
    }
}
