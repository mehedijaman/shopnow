<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\View\View;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductCategoryController extends SiteController
{
    public function index(): View
    {
        $categories = ProductCategory::orderBy('name')->get();

        return view('product::product-category-index', compact('categories'));
    }

    public function show(int $categoryId): View
    {
        $category = ProductCategory::find($categoryId);

        $products = $category->products()->paginate(12);

        return view('product::product-category-show', compact('category', 'products'));
    }
}
