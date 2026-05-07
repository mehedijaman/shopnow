<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Admin panel is excluded from search indexing --}}
    <meta name="robots" content="noindex, nofollow">

    {{-- If developing using SSL/HTTPS (uncomment the line below): Enforces loading all resources over HTTPS, upgrading requests from HTTP to HTTPS for enhanced security --}}
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    {{-- used by Tiptap Editor --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Canonical self-reference for admin --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    @php $faviconUrl = setting('branding.favicon_url'); @endphp
    @if ($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}" />
    @else
    <link rel="icon" href="/favicon.svg" />
    @endif

    {{-- Preconnect for external fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Scripts -->
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="font-sans antialiased h-full">
    @inertia

    <x-modular-translations></x-modular-translations>
</body>

</html>
