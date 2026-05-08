@extends('site-layout')

@section('seo_title', ($page->meta_tag_title ?? $page->title) . ' — ' . setting('branding.site_name', config('app.name')))

@if (!empty($seo['description']))
    @section('seo_description', $seo['description'])
@endif

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    {{-- Hero Banner --}}
    <div class="bg-linear-to-r from-blue-700 to-[#B06AB3] px-6 py-12 font-sans">
        <div class="container mx-auto flex flex-col items-center justify-center text-center">
            <h1 class="mb-4 text-3xl font-bold text-white sm:text-4xl">
                {{ $page->title }}
            </h1>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="mx-auto max-w-4xl px-6 py-12 lg:px-6">
        @if ($page->image_url)
            <img
                src="{{ $page->image_url }}"
                alt="{{ $page->title }}"
                class="mb-8 w-full rounded-xl object-cover"
            />
        @endif

        <div class="prose prose-lg max-w-none text-gray-700 dark:prose-invert">
            {!! $page->content !!}
        </div>
    </div>
@endsection
