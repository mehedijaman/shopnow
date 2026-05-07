<?php

namespace Modules\Index\Http\Controllers;

use Modules\Blog\Models\Post;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Services\SeoService;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index(SeoService $seoService)
    {
        $featuredCategories = ProductCategory::where('featured', true)
            ->where('active', true)
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
        $seo = $seoService->build([
            'title' => 'About Us',
            'description' => 'Learn more about who we are, our mission, and what drives us.',
            'canonical_full' => url('/about'),
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'About Us', 'url' => url('/about')],
                ]),
            ],
        ]);

        return view('about', compact('seo'));
    }

    public function privacyPolicy(SeoService $seoService)
    {
        $seo = $seoService->build([
            'title' => 'Privacy Policy',
            'description' => 'Read our privacy policy to understand how we handle your data.',
            'canonical_full' => url('/privacy-policy'),
            'robots' => 'noindex, follow',
        ]);

        return view('privacy-policy', compact('seo'));
    }

    public function termsOfService(SeoService $seoService)
    {
        $seo = $seoService->build([
            'title' => 'Terms of Service',
            'description' => 'Review the terms and conditions for using our platform.',
            'canonical_full' => url('/terms-of-service'),
            'robots' => 'noindex, follow',
        ]);

        return view('terms-of-service', compact('seo'));
    }
}
