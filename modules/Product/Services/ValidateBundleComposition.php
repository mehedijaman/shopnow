<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;

class ValidateBundleComposition
{
    public function run(Product $bundle, array $childProductIds): void
    {
        if (in_array($bundle->id, $childProductIds)) {
            abort(422, 'A bundle cannot include itself as a child.');
        }

        $nestedBundles = Product::whereIn('id', $childProductIds)
            ->where('type', 'bundle')
            ->exists();

        if ($nestedBundles) {
            abort(422, 'A bundle cannot include another bundle as a child.');
        }
    }
}
