<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;

class GetVariationBySelection
{
    /**
     * @param  array<int>  $attributeValueIds
     */
    public function run(Product $product, array $attributeValueIds): ?ProductVariation
    {
        $sorted = $attributeValueIds;
        sort($sorted);
        $key = implode(',', $sorted);

        return $product->variations()
            ->where('variation_key', $key)
            ->where('active', true)
            ->first();
    }
}
