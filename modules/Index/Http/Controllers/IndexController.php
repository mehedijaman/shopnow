<?php

namespace Modules\Index\Http\Controllers;

use Modules\Blog\Models\Post;
use Modules\Page\Models\Page;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Services\SeoService;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index(SeoService $seoService)
    {
        $featuredCategories = ProductCategory::where('featured', true)
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['products' => function ($query) {
                $query->where('active', true)
                    ->with('category')
                    ->latest()
                    ->limit(8);
            }])
            ->get();

        $latestPosts = Post::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit(4)
            ->get();

        $seo = $seoService->build([
            'canonical_full' => url('/'),
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->websiteSchema(),
            ],
        ]);

        return view('index::index', compact('featuredCategories', 'latestPosts', 'seo'));
    }

    public function about(SeoService $seoService)
    {
        $page = Page::where('slug', 'about')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? 'Learn more about who we are, our mission, and what drives us.',
            'canonical_full' => url('/about'),
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => $page->title, 'url' => url('/about')],
                ]),
            ],
        ]);

        return view('page-show', compact('page', 'seo'));
    }

    public function privacyPolicy(SeoService $seoService)
    {
        $page = Page::where('slug', 'privacy-policy')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? 'Read our privacy policy to understand how we handle your data.',
            'canonical_full' => url('/privacy-policy'),
            'robots' => 'noindex, follow',
        ]);

        return view('page-show', compact('page', 'seo'));
    }

    public function termsOfService(SeoService $seoService)
    {
        $page = Page::where('slug', 'terms-of-service')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? 'Review the terms and conditions for using our platform.',
            'canonical_full' => url('/terms-of-service'),
            'robots' => 'noindex, follow',
        ]);

        return view('page-show', compact('page', 'seo'));
    }

    public function refundPolicy(SeoService $seoService)
    {
        $page = Page::where('slug', 'refund-policy')->where('active', true)->firstOrFail();

        $seo = $seoService->build([
            'title' => $page->meta_tag_title ?? $page->title,
            'description' => $page->meta_tag_description ?? 'Read our refund and return policy.',
            'canonical_full' => url('/refund-policy'),
            'robots' => 'noindex, follow',
        ]);

        return view('page-show', compact('page', 'seo'));
    }
}
