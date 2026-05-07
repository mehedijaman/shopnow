<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Str;

class SeoService
{
    /** @var array<string, mixed> */
    private array $seoSettings;

    /** @var array<string, mixed> */
    private array $generalSettings;

    public function __construct()
    {
        $this->seoSettings = settings_group('seo');
        $this->generalSettings = settings_group('general');
    }

    /**
     * Build a complete SEO meta array for the given page.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function build(array $overrides = []): array
    {
        $siteName = setting('branding.site_name') ?: setting('general.site_name') ?: config('app.name', 'ShopNow');
        $defaultTitle = $this->seoSettings['meta_title'] ?? $siteName;
        $defaultDescription = $this->seoSettings['meta_description'] ?? $this->generalSettings['site_description'] ?? null;
        $defaultOgImage = $this->seoSettings['og_image_url'] ?? null;
        $canonicalDomain = rtrim($this->seoSettings['canonical_domain'] ?? config('app.url'), '/');

        $title = $overrides['title'] ?? $defaultTitle;
        $description = Str::limit($overrides['description'] ?? $defaultDescription ?? '', 160);
        $canonicalPath = $overrides['canonical'] ?? request()->path();
        $canonical = $overrides['canonical_full'] ?? ($canonicalDomain.'/'.ltrim($canonicalPath, '/'));
        $ogImage = $overrides['og_image'] ?? $defaultOgImage ?? null;
        $ogType = $overrides['og_type'] ?? 'website';
        $robots = $overrides['robots'] ?? $this->seoSettings['robots_default'] ?? 'index, follow';
        $keywords = $overrides['keywords'] ?? $this->seoSettings['meta_keywords'] ?? null;
        $twitterHandle = $this->seoSettings['twitter_handle'] ?? null;

        $pageTitle = $title !== $siteName
            ? "{$title} — {$siteName}"
            : $title;

        return [
            'site_name' => $siteName,
            'title' => $pageTitle,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => $canonical,
            'robots' => $robots,
            'og_title' => $overrides['og_title'] ?? $pageTitle,
            'og_description' => $overrides['og_description'] ?? $description,
            'og_image' => $ogImage,
            'og_type' => $ogType,
            'og_url' => $canonical,
            'twitter_card' => $overrides['twitter_card'] ?? ($ogImage ? 'summary_large_image' : 'summary'),
            'twitter_handle' => $twitterHandle,
            'twitter_title' => $overrides['twitter_title'] ?? $pageTitle,
            'twitter_description' => $overrides['twitter_description'] ?? $description,
            'twitter_image' => $overrides['twitter_image'] ?? $ogImage,
            'schema' => $overrides['schema'] ?? [],
            'published_time' => $overrides['published_time'] ?? null,
            'modified_time' => $overrides['modified_time'] ?? null,
            'author' => $overrides['author'] ?? null,
            'google_analytics_id' => $this->seoSettings['google_analytics_id'] ?? null,
        ];
    }

    /**
     * Build Organization JSON-LD schema.
     *
     * @return array<string, mixed>
     */
    public function organizationSchema(): array
    {
        $siteName = setting('branding.site_name') ?: config('app.name');
        $url = config('app.url');
        $logoUrl = setting('branding.logo_url');
        $social = settings_group('social');
        $contact = settings_group('contact');

        $sameAs = array_values(array_filter([
            $social['facebook'] ?? null,
            $social['instagram'] ?? null,
            $social['youtube'] ?? null,
            $social['linkedin'] ?? null,
            $social['x'] ?? null,
            $social['tiktok'] ?? null,
            $social['github'] ?? null,
        ]));

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $url,
        ];

        if ($logoUrl) {
            $schema['logo'] = $logoUrl;
        }

        if (! empty($contact['email']) && is_array($contact['email'])) {
            $schema['email'] = $contact['email'][0] ?? null;
        }

        if (! empty($contact['phone']) && is_array($contact['phone'])) {
            $schema['telephone'] = $contact['phone'][0] ?? null;
        }

        if (! empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        return $schema;
    }

    /**
     * Build WebSite JSON-LD schema with SearchAction.
     *
     * @return array<string, mixed>
     */
    public function websiteSchema(): array
    {
        $siteName = setting('branding.site_name') ?: config('app.name');
        $url = config('app.url');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $url,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $url.'/shop/search/{search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Build Article JSON-LD schema for a blog post.
     *
     * @param  array<string, mixed>  $post
     * @return array<string, mixed>
     */
    public function articleSchema(array $post): array
    {
        $siteName = setting('branding.site_name') ?: config('app.name');
        $logoUrl = setting('branding.logo_url');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post['title'],
            'description' => $post['description'] ?? null,
            'url' => $post['url'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
            ],
        ];

        if ($logoUrl) {
            $schema['publisher']['logo'] = [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ];
        }

        if (! empty($post['image'])) {
            $schema['image'] = $post['image'];
        }

        if (! empty($post['published_at'])) {
            $schema['datePublished'] = $post['published_at'];
        }

        if (! empty($post['updated_at'])) {
            $schema['dateModified'] = $post['updated_at'];
        }

        if (! empty($post['author'])) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $post['author'],
            ];
        }

        return $schema;
    }

    /**
     * Build Product JSON-LD schema.
     *
     * @param  array<string, mixed>  $product
     * @return array<string, mixed>
     */
    public function productSchema(array $product): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'url' => $product['url'],
        ];

        if (! empty($product['description'])) {
            $schema['description'] = $product['description'];
        }

        if (! empty($product['image'])) {
            $schema['image'] = $product['image'];
        }

        if (! empty($product['brand'])) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product['brand'],
            ];
        }

        if (isset($product['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => $product['currency'] ?? 'USD',
                'price' => $product['price'],
                'availability' => 'https://schema.org/InStock',
                'url' => $product['url'],
            ];
        }

        return $schema;
    }

    /**
     * Build BreadcrumbList JSON-LD schema.
     *
     * @param  array<int, array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public function breadcrumbSchema(array $items): array
    {
        $listItems = [];
        foreach ($items as $position => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
