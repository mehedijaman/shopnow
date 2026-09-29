<?php

namespace Modules\Index\Http\Controllers;

use Modules\Blog\Models\Post;
use Modules\Page\Models\Page;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Services\SeoService;
use Modules\Slider\Models\Slider;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index(SeoService $seoService)
    {
        $showSlider = setting('homepage.show_slider', true) !== false;
        $showFeaturedProducts = setting('homepage.show_featured_products', true) !== false;
        $showFeaturedCategories = setting('homepage.show_featured_categories', true) !== false;
        $showFeaturedCategoryProducts = setting('homepage.show_featured_category_products', true) !== false;
        $showBlog = setting('homepage.show_blog', true) !== false;
        $showBrands = setting('homepage.show_brands', true) !== false;

        $sliders = $showSlider
            ? Slider::where('active', true)
                ->orderBy('order')
                ->get(['id', 'title', 'description', 'bg_color', 'url', 'button_text'])
                ->map(fn ($slider) => [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'description' => $slider->description,
                    'image_url' => $slider->image_url,
                    'bg_color' => $slider->bg_color,
                    'url' => $slider->url,
                    'button_text' => $slider->button_text,
                ])
            : collect();

        $featuredProducts = $showFeaturedProducts
            ? Product::where('featured', true)
                ->where('active', true)
                ->with('category')
                ->latest()
                ->limit(8)
                ->get()
            : collect();

        $featuredCategories = $showFeaturedCategories
            ? ProductCategory::where('featured', true)
                ->where('active', true)
                ->whereHas('products', fn ($query) => $query->where('active', true))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
            : collect();

        $featuredCategoryProducts = $showFeaturedCategoryProducts
            ? ProductCategory::where('featured', true)
                ->where('active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->with(['products' => function ($query) {
                    $query->where('active', true)
                        ->with('category')
                        ->latest()
                        ->limit(8);
                }])
                ->get()
            : collect();

        $latestPosts = $showBlog
            ? Post::whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->limit(4)
                ->get()
            : collect();

        $brands = $showBrands
            ? ProductBrand::where('active', true)->orderBy('name')->get(['id', 'name', 'slug'])
            : collect();

        $seo = $seoService->build([
            'canonical_full' => url('/'),
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->websiteSchema(),
            ],
        ]);

        return view('index::index', compact('sliders', 'featuredProducts', 'featuredCategories', 'featuredCategoryProducts', 'latestPosts', 'brands', 'seo'));
    }

    public function about(SeoService $seoService)
    {
        $page = Page::where('slug', 'about')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? __('index::site.seo.about_description'),
            'canonical_full' => url('/about'),
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->breadcrumbSchema([
                    ['name' => __('site.nav.home'), 'url' => url('/')],
                    ['name' => $page->title, 'url' => url('/about')],
                ]),
            ],
        ]);

        return view('page::index', compact('page', 'seo'));
    }

    public function privacyPolicy(SeoService $seoService)
    {
        $page = Page::where('slug', 'privacy-policy')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? __('index::site.seo.privacy_description'),
            'canonical_full' => url('/privacy-policy'),
            'robots' => 'noindex, follow',
        ]);

        return view('page::index', compact('page', 'seo'));
    }

    public function termsOfService(SeoService $seoService)
    {
        $page = Page::where('slug', 'terms-of-service')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? __('index::site.seo.terms_description'),
            'canonical_full' => url('/terms-of-service'),
            'robots' => 'noindex, follow',
        ]);

        return view('page::index', compact('page', 'seo'));
    }

    public function refundPolicy(SeoService $seoService)
    {
        $page = Page::where('slug', 'refund-policy')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? __('index::site.seo.refund_description'),
            'canonical_full' => url('/refund-policy'),
            'robots' => 'noindex, follow',
        ]);

        return view('page::index', compact('page', 'seo'));
    }
}
