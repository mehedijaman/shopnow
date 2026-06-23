<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Services\SeoService;
use Modules\Support\Http\Controllers\SiteController;

class SiteProductController extends SiteController
{
    public function index(SeoService $seoService): View
    {
        $categories = ProductCategory::where('active', true)->orderBy('sort_order')->orderBy('name')->get();

        $products = Product::with(['category', 'tags'])
            ->where('active', true)
            ->whereHas('category', fn ($query) => $query->where('active', true))
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 45));

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

        return view('product::shop', compact('products', 'categories', 'seo'));
    }

    public function search(?string $searchText, SeoService $seoService): View
    {
        $categories = ProductCategory::where('active', true)->orderBy('sort_order')->orderBy('name')->get();

        $products = Product::with(['category', 'tags'])
            ->where('active', true)
            ->whereHas('category', fn ($query) => $query->where('active', true))
            ->orderBy('name', 'asc')
            ->where('name', 'like', '%'.$searchText.'%')
            ->paginate(request('rowsPerPage', 45));

        $seo = $seoService->build([
            'title' => 'Search: '.($searchText ?? ''),
            'robots' => 'noindex, follow',
        ]);

        return view('product::shop', compact('products', 'categories', 'searchText', 'seo'));
    }

    public function category(int $categoryId, SeoService $seoService): View
    {
        $categories = ProductCategory::where('active', true)->orderBy('sort_order')->orderBy('name')->get();
        $category = ProductCategory::findOrFail($categoryId);

        $products = $category->products()
            ->with('category')
            ->where('active', true)
            ->whereHas('category', fn ($query) => $query->where('active', true))
            ->paginate(45);

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

        return view('product::shop', compact('products', 'categories', 'category', 'seo'));
    }

    public function brand(int $brandId, SeoService $seoService): View
    {
        $brand = ProductBrand::findOrFail($brandId);

        $products = $brand->products()
            ->with('category')
            ->where('active', true)
            ->whereHas('category', fn ($query) => $query->where('active', true))
            ->paginate(45);

        $description = strip_tags($brand->description ?? "Browse all products from {$brand->name}.");

        $seo = $seoService->build([
            'title' => $brand->name,
            'description' => $description,
            'canonical_full' => url('/brand/'.$brand->id.'/'.$brand->slug),
            'schema' => [
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Shop', 'url' => url('/shop')],
                    ['name' => $brand->name, 'url' => url('/brand/'.$brand->id.'/'.$brand->slug)],
                ]),
            ],
        ]);

        return view('product::brand', compact('brand', 'products', 'seo'));
    }

    public function brands(SeoService $seoService): View
    {
        $brands = ProductBrand::where('active', true)->orderBy('name')->get();

        $seo = $seoService->build([
            'title' => 'Our Brands',
            'description' => 'Browse all our brands and discover products from your favorite manufacturers.',
            'canonical_full' => url('/brands'),
            'schema' => [
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Brands', 'url' => url('/brands')],
                ]),
            ],
        ]);

        return view('product::brands', compact('brands', 'seo'));
    }

    public function show(int $productId, SeoService $seoService): View
    {
        $product = Product::with(['category', 'brand', 'tags'])->findOrFail($productId);

        $gallery = $product->getMedia('gallery')->map(fn ($m) => $m->getUrl())->values()->toArray();

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

        return view('product::product-show', compact('product', 'gallery', 'seo'));
    }
}
