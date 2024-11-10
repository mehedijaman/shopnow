<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function index(): View
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 12));

        return view('product::shop', compact('products'));
    }

    public function search(Request $request): View
    {
        $searchText = $request->input('searchText');

        $products = Product::with(['category', 'tags'])
            ->orderBy('name', 'asc')
            ->where('name', 'like', '%' . $searchText . '%')
            ->paginate(request('rowsPerPage', 12));

        return view('product::shop', compact(['products', 'searchText']));
    }

    public function category(int $categoryId): View
    {
        $category = ProductCategory::find($categoryId);

        $products = $category->products()->paginate(12);

        return view('product::shop', compact('products', 'category'));
    }

    public function show(int $productId)
    {
        $product = Product::find($productId);

        return view('product::product-show', compact('product'));
    }
}
