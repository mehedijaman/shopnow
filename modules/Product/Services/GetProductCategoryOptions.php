<?php

namespace Modules\Product\Services;

use Illuminate\Support\Str;
use Modules\Blog\Models\Category;
use Modules\Product\Models\ProductCategory;

class GetProductCategoryOptions
{
    public function get(): array
    {
        return ProductCategory::orderBy('name')
            ->get()
            ->map(fn($category) => [
                'value' => $category->id,
                'label' => Str::limit($category->name, 25),
            ])
            ->all();
    }
}
