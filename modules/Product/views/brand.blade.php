@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="flex shrink-0 items-center gap-1">
            <a href="{{ route('shop.index') }}" class="hover:text-primary-600 hover:underline">Shop</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
        </li>
        <li class="min-w-0">
            <span class="block truncate font-semibold text-gray-800">{{ $brand->name }}</span>
        </li>
    </x-breadcrumb>

    <div class="mx-auto min-h-screen max-w-7xl px-4 py-8 sm:px-6 lg:px-6">
        {{-- Brand header --}}
        <div class="mb-8 flex flex-col items-center gap-6 sm:flex-row">
            @if ($brand->image_url)
                <div class="flex h-32 w-32 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <img
                        src="{{ $brand->image_url }}"
                        alt="{{ $brand->name }}"
                        class="max-h-full max-w-full object-contain"
                    />
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $brand->name }}</h1>
                @if ($brand->description)
                    <div class="mt-2 text-gray-600 dark:text-gray-400">{!! $brand->description !!}</div>
                @endif
            </div>
        </div>

        {{-- Products --}}
        @if ($products->count())
            <div class="mb-4 text-sm text-gray-500">
                {{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }} found
            </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </section>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-lg font-medium text-gray-500">No products found for {{ $brand->name }}.</p>
                <a href="{{ route('shop.index') }}" class="mt-4 text-sm text-primary-600 hover:underline">Browse all products</a>
            </div>
        @endif
    </div>
@endsection
