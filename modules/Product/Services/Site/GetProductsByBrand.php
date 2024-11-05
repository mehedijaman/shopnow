<?php

namespace Modules\Product\Services\Site;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;

class GetProductsByBrand
{
    public function get(int $brandId): LengthAwarePaginator
    {
        return Product::orderBy('id', 'desc')
            ->where('brand_id', $brandId)
            ->latest()
            ->paginate(12);
    }
}
