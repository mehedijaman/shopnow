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
        <meta name="robots" content="@yield('robots', $seo['robots'] ?? 'index, follow')" />
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

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4/fonts/remixicon.min.css">
        @vite(['resources-site/css/site.css'])
        @yield('headEndScripts')

        @php
            $pixelEnabled = (bool) setting('pixel.enabled', false);
            $pixelId = (string) setting('pixel.meta_pixel_id', '');
            $pixelEnableNonProduction = (bool) setting('pixel.enable_non_production', false);
            $pixelCanLoadInEnv = app()->environment('production') || $pixelEnableNonProduction;
            $pixelCanLoad = $pixelEnabled && $pixelCanLoadInEnv && $pixelId !== '';

            $gtmContainerId = (string) setting('analytics.gtm_container_id', '');
            $gtmCanLoad = $gtmContainerId !== '';
        @endphp

        @if ($pixelCanLoad || $gtmCanLoad)
        <script>
            (function () {
                var pixelConfig = {
                    enabled: @json($pixelCanLoad),
                    pixelId: @json($pixelId),
                }

                var gtmConfig = {
                    enabled: @json($gtmCanLoad),
                    containerId: @json($gtmContainerId),
                }

                function initPixel() {
                    if (!pixelConfig.enabled || !pixelConfig.pixelId) {
                        return
                    }

                    !function(f,b,e,v,n,t,s)
                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');

                    fbq('init', pixelConfig.pixelId)
                    fbq('track', 'PageView')
                }

                function initGtm() {
                    if (!gtmConfig.enabled || !gtmConfig.containerId) {
                        return
                    }

                    window.dataLayer = window.dataLayer || []
                    window.dataLayer.push({
                        'gtm.start': new Date().getTime(),
                        event: 'gtm.js',
                    })

                    var script = document.createElement('script')
                    script.async = true
                    script.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(gtmConfig.containerId)
                    document.head.appendChild(script)
                }

                window.ShopNowTracking = {
                    pixelEnabled: pixelConfig.enabled,
                    gtmEnabled: gtmConfig.enabled,
                    track: function (eventName, payload, options) {
                        if (typeof window.fbq !== 'function') {
                            return
                        }
                        window.fbq('track', eventName, payload || {}, options || {})
                    },
                    trackCustom: function (eventName, payload) {
                        if (typeof window.fbq !== 'function') {
                            return
                        }
                        window.fbq('trackCustom', eventName, payload || {})
                    },
                }

                initPixel()
                initGtm()
            })();
        </script>
        @endif
    </head>

    <body>
        @if ($gtmCanLoad)
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ urlencode($gtmContainerId) }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        @endif

        <div id="app">
            <x-header></x-header>

            @yield('content')

            <x-footer></x-footer>

            <mobile-bottom-nav :is-logged-in="{{ Auth::guard('customer')->check() ? 'true' : 'false' }}"></mobile-bottom-nav>
            <shop-search></shop-search>
        </div>

        @yield('bodyEndScripts')
        @stack('scripts')
    </body>
</html>
