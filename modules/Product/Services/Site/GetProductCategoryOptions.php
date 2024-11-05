<?php

namespace Modules\Product\Services\Site;

use Illuminate\Support\Str;
use Modules\Product\Models\ProductCategory;

class GetProductCategoryOptions
{
    public static function get()
    {
        return ProductCategory::orderBy('name')
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'name' => Str::limit($category->name, 25),
            ])
            ->all();
    }
}
