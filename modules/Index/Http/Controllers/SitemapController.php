<?php

namespace Modules\Index\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Modules\Blog\Models\Post;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\SiteController;

class SitemapController extends SiteController
{
    private const CACHE_TTL_SECONDS = 3600; // 1 hour

    public function index(): Response
    {
        $xml = Cache::remember('sitemap.index', self::CACHE_TTL_SECONDS, function () {
            $sitemaps = [
                ['loc' => route('sitemap.static'), 'lastmod' => Carbon::now()->toAtomString()],
                ['loc' => route('sitemap.products'), 'lastmod' => Carbon::now()->toAtomString()],
                ['loc' => route('sitemap.blog'), 'lastmod' => Carbon::now()->toAtomString()],
            ];

            return view('index::sitemaps.index', compact('sitemaps'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function staticPages(): Response
    {
        $xml = Cache::remember('sitemap.static', self::CACHE_TTL_SECONDS, function () {
            $baseUrl = config('app.url');
            $pages = [
                ['loc' => $baseUrl, 'changefreq' => 'weekly', 'priority' => '1.0'],
                ['loc' => $baseUrl.'/about', 'changefreq' => 'monthly', 'priority' => '0.6'],
                ['loc' => $baseUrl.'/blog', 'changefreq' => 'daily', 'priority' => '0.8'],
                ['loc' => $baseUrl.'/shop', 'changefreq' => 'daily', 'priority' => '0.9'],
                ['loc' => $baseUrl.'/privacy-policy', 'changefreq' => 'yearly', 'priority' => '0.3'],
                ['loc' => $baseUrl.'/terms-of-service', 'changefreq' => 'yearly', 'priority' => '0.3'],
                ['loc' => $baseUrl.'/contact', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ];

            return view('index::sitemaps.urlset', compact('pages'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function products(): Response
    {
        $xml = Cache::remember('sitemap.products', self::CACHE_TTL_SECONDS, function () {
            $products = Product::query()
                ->where('active', true)
                ->select(['id', 'slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->get();

            $categories = ProductCategory::query()
                ->select(['id', 'slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->get();

            return view('index::sitemaps.products', compact('products', 'categories'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function blog(): Response
    {
        $xml = Cache::remember('sitemap.blog', self::CACHE_TTL_SECONDS, function () {
            $posts = Post::query()
                ->where('published_at', '<=', Carbon::now())
                ->select(['id', 'slug', 'title', 'published_at', 'updated_at'])
                ->orderByDesc('published_at')
                ->get();

            return view('index::sitemaps.blog', compact('posts'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
