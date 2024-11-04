<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\Models\Product;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function show(int $productId)
    {
        $product = Product::find($productId);

        return view('index::product-details', compact('product'));
    }
}
