<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($pages as $page)
    <url>
        <loc>{{ $page['loc'] }}</loc>
        @if (!empty($page['lastmod']))
        <lastmod>{{ $page['lastmod'] }}</lastmod>
        @endif
        @if (!empty($page['changefreq']))
        <changefreq>{{ $page['changefreq'] }}</changefreq>
        @endif
        @if (!empty($page['priority']))
        <priority>{{ $page['priority'] }}</priority>
        @endif
    </url>
    @endforeach
</urlset>
