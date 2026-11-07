@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="min-w-0">
            <span class="font-semibold text-site-text dark:text-site-text">{{ __('product::site.brands.heading') }}</span>
        </li>
    </x-breadcrumb>

    <div class="mx-auto min-h-screen max-w-7xl px-4 py-8 sm:px-6 lg:px-6">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-site-text dark:text-site-text">{{ __('product::site.brands.heading') }}</h1>
            <p class="mt-2 text-site-muted dark:text-site-muted">{{ __('product::site.brands.subtitle') }}</p>
        </div>

        @if ($brands->count())
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($brands as $brand)
                    <a
                        href="{{ url('/brand/'.$brand->id.'/'.$brand->slug) }}"
                        class="group flex flex-col items-center rounded-xl border border-site-divider bg-site-surface p-6 transition-shadow hover:shadow-lg dark:border-site-divider dark:bg-site-surface-muted"
                    >
                        <div class="flex h-24 w-full items-center justify-center">
                            @if ($brand->image_url)
                                <img
                                    src="{{ $brand->image_url }}"
                                    alt="{{ $brand->name }}"
                                    class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                />
                            @else
                                <span class="text-lg font-semibold text-site-muted dark:text-site-muted">{{ $brand->name }}</span>
                            @endif
                        </div>
                        <span class="mt-3 text-sm font-medium text-site-text dark:text-site-text">{{ $brand->name }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-16 w-16 text-site-text" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <p class="text-lg font-medium text-site-muted dark:text-site-muted">{{ __('product::site.brands.none') }}</p>
            </div>
        @endif
    </div>
@endsection
