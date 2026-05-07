<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($categories as $category)
    <url>
        <loc>{{ url('/shop/category/'.$category->id.'/'.$category->slug) }}</loc>
        @if ($category->updated_at)
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        @endif
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach ($products as $product)
    <url>
        <loc>{{ url('/shop/product/'.$product->id.'/'.$product->slug) }}</loc>
        @if ($product->updated_at)
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        @endif
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if ($product->image_url)
        <image:image>
            <image:loc>{{ $product->image_url }}</image:loc>
            <image:title>{{ $product->name ?? '' }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
