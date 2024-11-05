<?php

namespace Modules\Product\Services\Site;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;

class GetProductsByCategory
{
    public function get(int $categoryId): LengthAwarePaginator
    {
        return Product::orderBy('id', 'desc')
            ->where('category_id', $categoryId)
            ->latest()
            ->paginate(12);
    }
}
