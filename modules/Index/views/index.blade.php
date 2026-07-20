@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    {{-- Slider Carousel --}}
    @if ($sliders->isNotEmpty())
        <slider-carousel :sliders="{{ json_encode($sliders) }}"></slider-carousel>
    @endif

    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">

        {{-- Featured Category Sections --}}
        <x-featured-categories :categories="$featuredCategories" />

        {{-- Our Brands Section --}}
        @if ($brands->isNotEmpty())
            <section class="mt-8">
                <brands-carousel :brands="{{ json_encode($brands) }}"></brands-carousel>
            </section>
        @endif

        {{-- Blog Section --}}
        <x-latest-blog :latestPosts="$latestPosts" />

    </div>
@endsection
