<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        {{-- Primary SEO --}}
        @php
            $siteName = setting('branding.site_name', config('app.name'));
            $siteSlogan = setting('branding.site_slogan');
            $defaultTitle = $siteSlogan ? $siteName.' — '.$siteSlogan : $siteName;
        @endphp
        <title>@yield('seo_title', $seo['title'] ?? $defaultTitle)</title>
        @if (!empty($seo['description']))
        <meta name="description" content="{{ $seo['description'] }}" />
        @endif
        @if (!empty($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}" />
        @endif
        <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}" />
        @if (!empty($seo['author']))
        <meta name="author" content="{{ $seo['author'] }}" />
        @endif
        <meta name="language" content="{{ app()->getLocale() }}" />

        {{-- Canonical URL --}}
        <link rel="canonical" href="@yield('canonical', $seo['canonical'] ?? url()->current())" />

        {{-- Open Graph --}}
        <meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}" />
        <meta property="og:title" content="{{ $seo['og_title'] ?? ($seo['title'] ?? config('app.name')) }}" />
        @if (!empty($seo['og_description']))
        <meta property="og:description" content="{{ $seo['og_description'] }}" />
        @endif
        <meta property="og:url" content="{{ $seo['og_url'] ?? url()->current() }}" />
        <meta property="og:site_name" content="{{ $seo['site_name'] ?? config('app.name') }}" />
        @if (!empty($seo['og_image']))
        <meta property="og:image" content="{{ $seo['og_image'] }}" />
        <meta property="og:image:alt" content="{{ $seo['og_title'] ?? ($seo['title'] ?? '') }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        @endif
        @if (!empty($seo['published_time']))
        <meta property="article:published_time" content="{{ $seo['published_time'] }}" />
        @endif
        @if (!empty($seo['modified_time']))
        <meta property="article:modified_time" content="{{ $seo['modified_time'] }}" />
        @endif

        {{-- Twitter / X Cards --}}
        <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary' }}" />
        <meta name="twitter:title" content="{{ $seo['twitter_title'] ?? ($seo['title'] ?? config('app.name')) }}" />
        @if (!empty($seo['twitter_description']))
        <meta name="twitter:description" content="{{ $seo['twitter_description'] }}" />
        @endif
        @if (!empty($seo['twitter_image']))
        <meta name="twitter:image" content="{{ $seo['twitter_image'] }}" />
        @endif
        @if (!empty($seo['twitter_handle']))
        <meta name="twitter:site" content="{{ $seo['twitter_handle'] }}" />
        @endif

        {{-- Favicon --}}
        @php $faviconUrl = setting('branding.favicon_url'); @endphp
        @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}" />
        @else
        <link rel="icon" href="/favicon.svg" />
        @endif

        {{-- Preconnect for performance --}}
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin />

        {{-- Fonts --}}
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" />

        {{-- CSRF --}}
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        {{-- JSON-LD Structured Data --}}
        @if (!empty($seo['schema']))
            @foreach ((array) $seo['schema'] as $schemaItem)
            <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
            @endforeach
        @endif

        {{-- Page-level overrides from child views --}}
        @stack('head')

        @vite(['resources-site/css/site.css'])
        @yield('headEndScripts')

        {{-- Google Analytics (only when ID is configured) --}}
        @if (!empty($seo['google_analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $seo['google_analytics_id'] }}', { anonymize_ip: true });
        </script>
        @endif
    </head>

    <body>
        <div id="app">
            <x-header></x-header>

            @yield('content')

            <x-footer></x-footer>
        </div>

        @yield('bodyEndScripts')
        @stack('scripts')
    </body>
</html>
