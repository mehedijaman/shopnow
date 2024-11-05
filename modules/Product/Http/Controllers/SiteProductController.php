<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function index(): View
    {
        $products = Product::orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'image_url' => $product->image_url,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity,
                'unit' => $product->unit,
                'min_order' => $product->min_order,
                'active' => $product->active,
                'featured' => $product->featured,
            ]);

        return view('product::product-index', compact('products'));
    }

    public function show(int $productId): View
    {
        $product = Product::find($productId);

        return view('product::product-details', compact('product'));
    }
}
