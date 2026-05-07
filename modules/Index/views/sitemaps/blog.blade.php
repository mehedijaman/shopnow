<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($posts as $post)
    <url>
        <loc>{{ url('/blog/'.$post->slug) }}</loc>
        @if ($post->updated_at)
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
        @if ($post->image_url)
        <image:image>
            <image:loc>{{ $post->image_url }}</image:loc>
            <image:title>{{ $post->title ?? '' }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
