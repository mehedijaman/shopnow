<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function index(): View
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 12));

        return view('product::product-index', compact('products'));
    }

    public function show(int $productId)
    {
        $product = Product::find($productId);

        return view('product::product-show', compact('product'));
    }

    public function search(Request $request): View
    {
        $searchText = $request->input('searchText');

        $products = Product::with(['category', 'tags'])
            ->orderBy('name', 'asc')
            ->where('name', 'like', '%' . $searchText . '%')
            ->paginate(request('rowsPerPage', 10));

        return view('product::product-search-result', compact('products', 'searchText'));
    }
}
