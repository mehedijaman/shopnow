<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;

class CalculateVariationPriceRange
{
    public function run(Product $product): array
    {
        $prices = $product->variations()
            ->where('active', true)
            ->get()
            ->map(fn ($v) => $v->sale_price ? min($v->price, $v->sale_price) : $v->price)
            ->filter()
            ->values();

        if ($prices->isEmpty()) {
            return [
                'min' => (float) $product->price,
                'max' => (float) $product->price,
                'has_range' => false,
            ];
        }

        return [
            'min' => (float) $prices->min(),
            'max' => (float) $prices->max(),
            'has_range' => $prices->min() !== $prices->max(),
        ];
    }
}
