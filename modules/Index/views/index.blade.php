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

        {{-- Featured Categories --}}
        <x-featured-categories :categories="$featuredCategories" />

        {{-- Featured Products --}}
        @if ($featuredProducts->isNotEmpty())
            <section class="mb-12 lg:mb-16">
                <div class="mb-6 flex items-end justify-between">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('index::site.featured_products') }}</h2>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Featured Category Product Sections --}}
        <x-featured-category-products :categories="$featuredCategoryProducts" />

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
