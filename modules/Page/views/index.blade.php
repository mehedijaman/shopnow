@extends('site-layout')

@section('seo_title', ($page->meta_tag_title ?? $page->title) . ' — ' . setting('branding.site_name', config('app.name')))

@if (!empty($seo['description']))
    @section('seo_description', $seo['description'])
@endif

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="min-w-0">
            <span class="block truncate font-semibold text-gray-800" title="{{ $page->title }}">{{ $page->title }}</span>
        </li>
    </x-breadcrumb>

    <div class="bg-gray-50 py-12 sm:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Page heading --}}
            <div class="mb-10 text-center">
                <div class="mb-3 flex items-center justify-center gap-3">
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">{{ $page->title }}</h1>
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                </div>
            </div>

            {{-- Content card --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                @if ($page->image_url)
                    <div class="w-full overflow-hidden bg-gray-100">
                        <img
                            src="{{ $page->image_url }}"
                            alt="{{ $page->title }}"
                            class="w-full object-contain"
                        />
                    </div>
                @endif

                <div class="p-6 sm:p-10">
                    <div class="prose prose-gray max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline prose-img:rounded-lg">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
