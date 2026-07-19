<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;

class CalculateBundlePrice
{
    /**
     * @param  array<int, array{product_id: int, quantity: int, price_override?: float|null, is_optional: bool}>  $selectedItems
     */
    public function run(Product $product, array $selectedItems = []): float
    {
        $config = $product->bundleConfig;

        if (! $config) {
            return (float) $product->price;
        }

        if ($config->pricing_type === 'fixed') {
            return (float) ($config->fixed_price ?? $product->price);
        }

        $allItems = $product->bundleItems()
            ->with('childProduct')
            ->orderBy('sort_order')
            ->get();

        $total = 0;

        foreach ($allItems as $item) {
            $isSelected = in_array($item->id, array_column($selectedItems, 'id') ?: []) ||
                in_array($item->child_product_id, array_column($selectedItems, 'product_id') ?: []);

            if ($item->is_optional && ! $isSelected) {
                continue;
            }

            $unitPrice = (float) ($item->price_override ?? $item->childProduct?->sale_price ?? $item->childProduct?->price ?? 0);
            $total += $unitPrice * $item->quantity;
        }

        if ($config->discount_type === 'percentage' && $config->discount_value) {
            $total -= $total * ((float) $config->discount_value / 100);
        } elseif ($config->discount_type === 'fixed_amount' && $config->discount_value) {
            $total -= (float) $config->discount_value;
        }

        return round(max($total, 0), 2);
    }
}
