<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;

class GenerateProductVariations
{
    /**
     * @param  array<int, array<int>>  $groupedValueIds  attribute_id => [value_id, ...]
     */
    public function run(Product $product, array $groupedValueIds): void
    {
        $combinations = $this->cartesianProduct(array_values($groupedValueIds));

        $existingKeys = $product->variations()
            ->withTrashed()
            ->get()
            ->keyBy('variation_key');

        $generatedKeys = [];

        foreach ($combinations as $combination) {
            $sorted = $combination;
            sort($sorted);
            $key = implode(',', $sorted);

            $generatedKeys[] = $key;

            if ($existingKeys->has($key)) {
                $variation = $existingKeys->get($key);

                if (! $variation->active && $variation->trashed()) {
                    $variation->restore();
                }

                $variation->attributeValues()->sync($combination);

                continue;
            }

            $variation = $product->variations()->create([
                'price' => $product->price ?? 0,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity ?? 0,
                'active' => true,
                'variation_key' => $key,
            ]);

            $variation->attributeValues()->sync($combination);
        }

        $product->variations()
            ->whereNotIn('variation_key', $generatedKeys)
            ->where('active', true)
            ->each(fn (ProductVariation $v) => $v->update(['active' => false]));
    }

    private function cartesianProduct(array $arrays): array
    {
        if (empty($arrays)) {
            return [];
        }

        $result = [[]];

        foreach ($arrays as $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[] = $item;
                    $append[] = $product;
                    array_pop($product);
                }
            }

            $result = $append;
        }

        return $result;
    }
}
