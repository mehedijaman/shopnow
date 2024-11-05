<?php

namespace Modules\Product\Services\Site;

use Illuminate\Support\Str;
use Modules\Product\Models\ProductBrand;

class GetProductBrandOptions
{
    public function get(): array
    {
        return ProductBrand::orderBy('name')
            ->get()
            ->map(fn ($brand) => [
                'id' => $brand->id,
                'name' => Str::limit($brand->name, 25),
            ])
            ->all();
    }
}
