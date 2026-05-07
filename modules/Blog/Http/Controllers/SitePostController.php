<?php

namespace Modules\Blog\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use Modules\Blog\Models\Post;
use Modules\Blog\Services\Site\GetArchiveOptions;
use Modules\Blog\Services\Site\GetTagOptions;
use Modules\Settings\Services\SeoService;
use Modules\Support\Http\Controllers\SiteController;

class SitePostController extends SiteController
{
    public function index(GetArchiveOptions $getArchiveOptions, GetTagOptions $getTagOptions, SeoService $seoService): View
    {
        $posts = Post::with('tags', 'author')
            ->where('published_at', '<=', Carbon::now())
            ->latest()
            ->paginate(6);

        $archiveOptions = $getArchiveOptions->get();
        $tags = $getTagOptions->get();

        $seo = $seoService->build([
            'title' => 'Blog',
            'description' => 'Read our latest articles, tips, and insights.',
            'og_type' => 'website',
            'schema' => [
                $seoService->organizationSchema(),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Blog', 'url' => url('/blog')],
                ]),
            ],
        ]);

        return view('blog::post-index', compact('posts', 'archiveOptions', 'tags', 'seo'));
    }

    public function show(string $slug, SeoService $seoService): View
    {
        $post = Post::with('author')->where('slug', $slug)->firstOrFail();

        $description = strip_tags($post->excerpt ?? substr(strip_tags($post->content ?? ''), 0, 160));

        $seo = $seoService->build([
            'title' => $post->title,
            'description' => $description,
            'og_type' => 'article',
            'og_image' => $post->image_url,
            'twitter_image' => $post->image_url,
            'published_time' => $post->published_at?->toIso8601String(),
            'modified_time' => $post->updated_at?->toIso8601String(),
            'author' => $post->author?->name,
            'canonical_full' => url('/blog/'.$post->slug),
            'schema' => [
                $seoService->articleSchema([
                    'title' => $post->title,
                    'description' => $description,
                    'url' => url('/blog/'.$post->slug),
                    'image' => $post->image_url,
                    'published_at' => $post->published_at?->toIso8601String(),
                    'updated_at' => $post->updated_at?->toIso8601String(),
                    'author' => $post->author?->name,
                ]),
                $seoService->breadcrumbSchema([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Blog', 'url' => url('/blog')],
                    ['name' => $post->title, 'url' => url('/blog/'.$post->slug)],
                ]),
            ],
        ]);

        return view('blog::post-show', compact('post', 'seo'));
    }
}
