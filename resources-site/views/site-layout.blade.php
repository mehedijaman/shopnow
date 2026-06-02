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

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4/fonts/remixicon.min.css">
        @vite(['resources-site/css/site.css'])
        @yield('headEndScripts')

        @php
            $pixelEnabled = (bool) setting('pixel.enabled', false);
            $pixelId = (string) setting('pixel.meta_pixel_id', '');
            $pixelRequireConsent = (bool) setting('pixel.require_consent', true);
            $pixelEnableNonProduction = (bool) setting('pixel.enable_non_production', false);
            $pixelCanLoadInEnv = app()->environment('production') || $pixelEnableNonProduction;
            $pixelCanLoad = $pixelEnabled && $pixelCanLoadInEnv && $pixelId !== '';
        @endphp

        @if ($pixelCanLoad)
        <script>
            (function () {
                var consentKey = 'tracking_consent'
                var bannerId = 'tracking-consent-banner'

                function getConsentValue() {
                    var fromStorage = null

                    try {
                        fromStorage = window.localStorage.getItem(consentKey)
                    } catch (e) {
                        fromStorage = null
                    }

                    if (fromStorage === 'granted' || fromStorage === 'denied') {
                        return fromStorage
                    }

                    var cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + consentKey + '=([^;]*)'))
                    return cookieMatch ? decodeURIComponent(cookieMatch[1]) : null
                }

                function persistConsent(value) {
                    var oneYear = 60 * 60 * 24 * 365

                    try {
                        window.localStorage.setItem(consentKey, value)
                    } catch (e) {
                        // Ignore storage failures; cookie still persists consent.
                    }

                    document.cookie = consentKey + '=' + encodeURIComponent(value)
                        + '; path=/'
                        + '; max-age=' + oneYear
                        + '; samesite=lax'
                }

                var pixelConfig = {
                    pixelId: @json($pixelId),
                    requireConsent: @json($pixelRequireConsent),
                }

                var initialized = false

                function initPixel() {
                    if (initialized || !pixelConfig.pixelId) {
                        return
                    }

                    initialized = true

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

                function hasConsent() {
                    if (!pixelConfig.requireConsent) {
                        return true
                    }

                    return getConsentValue() === 'granted'
                }

                window.ShopNowTracking = {
                    hasConsent: hasConsent,
                    setConsent: function (granted) {
                        var value = granted ? 'granted' : 'denied'
                        persistConsent(value)

                        if (granted) {
                            initPixel()
                        }

                        var banner = document.getElementById(bannerId)
                        if (banner) {
                            banner.classList.add('hidden')
                        }
                    },
                    track: function (eventName, payload, options) {
                        if (!hasConsent() || typeof window.fbq !== 'function') {
                            return
                        }

                        window.fbq('track', eventName, payload || {}, options || {})
                    },
                    trackCustom: function (eventName, payload) {
                        if (!hasConsent() || typeof window.fbq !== 'function') {
                            return
                        }

                        window.fbq('trackCustom', eventName, payload || {})
                    },
                }

                var consent = getConsentValue()
                if (!pixelConfig.requireConsent || consent === 'granted') {
                    initPixel()
                }

                document.addEventListener('DOMContentLoaded', function () {
                    var banner = document.getElementById(bannerId)
                    if (!banner) {
                        return
                    }

                    var shouldShowBanner = pixelConfig.requireConsent && consent !== 'granted' && consent !== 'denied'
                    if (shouldShowBanner) {
                        banner.classList.remove('hidden')
                    }

                    var acceptButton = document.getElementById('tracking-consent-accept')
                    var declineButton = document.getElementById('tracking-consent-decline')

                    if (acceptButton) {
                        acceptButton.addEventListener('click', function () {
                            window.ShopNowTracking.setConsent(true)
                        })
                    }

                    if (declineButton) {
                        declineButton.addEventListener('click', function () {
                            window.ShopNowTracking.setConsent(false)
                        })
                    }
                })
            })();
        </script>
        @endif

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

        @if ($pixelCanLoad && $pixelRequireConsent)
        <div
            id="tracking-consent-banner"
            class="fixed bottom-0 left-0 right-0 z-50 hidden border-t border-skin-neutral-4 bg-white/95 p-4 shadow-lg backdrop-blur"
        >
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-skin-neutral-11">
                    We use Meta Pixel to measure campaign performance and improve your shopping experience. You can accept or decline tracking.
                </p>
                <div class="flex shrink-0 items-center gap-2">
                    <button
                        id="tracking-consent-decline"
                        type="button"
                        class="rounded-md border border-skin-neutral-6 px-3 py-2 text-sm font-medium text-skin-neutral-11 hover:bg-skin-neutral-2"
                    >
                        Decline
                    </button>
                    <button
                        id="tracking-consent-accept"
                        type="button"
                        class="rounded-md bg-skin-primary-10 px-3 py-2 text-sm font-medium text-skin-neutral-1 hover:opacity-90"
                    >
                        Accept
                    </button>
                </div>
            </div>
        </div>
        @endif

        @yield('bodyEndScripts')
        @stack('scripts')
    </body>
</html>
