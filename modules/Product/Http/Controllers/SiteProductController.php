<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Services\SeoService;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function index(SeoService $seoService): View
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 12));

        $seo = $seoService->build([
            'title' => 'Shop',
            'description' => 'Browse our full collection of products.',
            'canonical_full' => url('/shop'),
            'schema' => [
                $seoService->websiteSchema(),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Shop', 'url' => url('/shop')],
                ]),
            ],
        ]);

        return view('product::shop', compact('products', 'seo'));
    }

    public function search(?string $searchText, SeoService $seoService): View
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('name', 'asc')
            ->where('name', 'like', '%'.$searchText.'%')
            ->paginate(request('rowsPerPage', 12));

        $seo = $seoService->build([
            'title' => 'Search: '.($searchText ?? ''),
            'robots' => 'noindex, follow',
        ]);

        return view('product::shop', compact('products', 'searchText', 'seo'));
    }

    public function category(int $categoryId, SeoService $seoService): View
    {
        $category = ProductCategory::findOrFail($categoryId);

        $products = $category->products()->paginate(12);

        $description = strip_tags($category->description ?? "Browse all products in {$category->name}.");

        $seo = $seoService->build([
            'title' => $category->name,
            'description' => $description,
            'canonical_full' => url('/shop/category/'.$category->id.'/'.$category->slug),
            'schema' => [
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Shop', 'url' => url('/shop')],
                    ['name' => $category->name, 'url' => url('/shop/category/'.$category->id.'/'.$category->slug)],
                ]),
            ],
        ]);

        return view('product::shop', compact('products', 'category', 'seo'));
    }

    public function show(int $productId, SeoService $seoService): View
    {
        $product = Product::with(['category', 'brand'])->findOrFail($productId);

        $description = strip_tags($product->description ?? "Buy {$product->name} at the best price.");

        $seo = $seoService->build([
            'title' => $product->name,
            'description' => $description,
            'og_type' => 'product',
            'og_image' => $product->image_url,
            'twitter_image' => $product->image_url,
            'canonical_full' => url('/shop/product/'.$product->id.'/'.$product->slug),
            'schema' => [
                $seoService->productSchema([
                    'name' => $product->name,
                    'description' => $description,
                    'url' => url('/shop/product/'.$product->id.'/'.$product->slug),
                    'image' => $product->image_url,
                    'brand' => $product->brand?->name,
                    'price' => $product->price ?? null,
                    'currency' => 'USD',
                ]),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Shop', 'url' => url('/shop')],
                    ['name' => $product->category?->name ?? 'Products', 'url' => url('/shop/category/'.($product->category?->id ?? 0).'/'.($product->category?->slug ?? ''))],
                    ['name' => $product->name, 'url' => url('/shop/product/'.$product->id.'/'.$product->slug)],
                ]),
            ],
        ]);

        return view('product::product-show', compact('product', 'seo'));
    }
}
