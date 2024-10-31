<?php

namespace Modules\Product\Services;

use Illuminate\Support\Str;
use Modules\Product\Models\ProductBrand;

class GetProductBrandOptions
{
    public function get(): array
    {
        return ProductBrand::orderBy('name')
            ->get()
            ->map(fn ($brand) => [
                'value' => $brand->id,
                'label' => Str::limit($brand->name, 25),
            ])
            ->all();
    }
}
