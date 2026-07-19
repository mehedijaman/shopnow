<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;

class CheckBundleStock
{
    /**
     * @param  array<int, int>|null  $excludedItemIds  IDs of optional items the customer is not taking
     */
    public function run(Product $product, ?array $excludedItemIds = []): array
    {
        $items = $product->bundleItems()
            ->with('childProduct')
            ->get()
            ->filter(fn ($item) => ! $item->is_optional || ! in_array($item->id, $excludedItemIds ?? []));

        $maxBundles = PHP_INT_MAX;

        foreach ($items as $item) {
            $available = $item->childProduct?->quantity ?? 0;
            $needed = $item->quantity > 0 ? $item->quantity : 1;
            $possible = (int) floor($available / $needed);

            if ($possible < $maxBundles) {
                $maxBundles = $possible;
            }
        }

        $inStock = $maxBundles > 0;
        $stockLevel = $maxBundles === PHP_INT_MAX ? 0 : $maxBundles;

        return [
            'in_stock' => $inStock,
            'available_bundles' => $stockLevel,
        ];
    }
}
