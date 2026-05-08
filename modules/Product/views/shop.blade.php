@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        @if (isset($searchText))
            <li class="flex shrink-0 items-center gap-1">
                <a href="{{ route('shop.index') }}" class="hover:text-primary-600 hover:underline">Shop</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
            </li>
            <li class="min-w-0">
                <span class="block truncate font-semibold text-gray-800">Search: &ldquo;{{ $searchText }}&rdquo;</span>
            </li>
        @elseif (isset($category))
            <li class="flex shrink-0 items-center gap-1">
                <a href="{{ route('shop.index') }}" class="hover:text-primary-600 hover:underline">Shop</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
            </li>
            <li class="min-w-0">
                <span class="block truncate font-semibold text-gray-800">{{ $category->name }}</span>
            </li>
        @else
            <li class="min-w-0">
                <span class="font-semibold text-gray-800">Shop</span>
            </li>
        @endif
    </x-breadcrumb>

    <div class="mx-auto min-h-screen max-w-7xl px-4 py-8 sm:px-6 lg:px-6">

        {{-- Page header --}}
        @if (isset($searchText))
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Search Results for &ldquo;{{ $searchText }}&rdquo;</h1>
            </div>
        @elseif (isset($category))
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
            </div>
        @else
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">All Products</h1>
            </div>
        @endif

        {{-- Mobile filter toggle --}}
        <div class="mb-4 lg:hidden">
            <button
                id="filterToggle"
                type="button"
                class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                Filter by Category
            </button>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row">

            {{-- Sidebar filter --}}
            <aside
                id="filterSidebar"
                class="hidden w-full shrink-0 lg:block lg:w-56 xl:w-64"
            >
                <div class="sticky top-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between border-b border-gray-200 pb-3">
                        <p class="text-sm font-semibold uppercase tracking-wide text-gray-700">Categories</p>
                        @if (isset($category))
                            <a
                                href="{{ route('shop.index') }}"
                                class="text-xs font-medium text-gray-400 hover:text-primary-600"
                            >
                                Clear
                            </a>
                        @endif
                    </div>

                    <nav class="flex flex-col gap-1">
                        <a
                            href="{{ route('shop.index') }}"
                            class="flex items-center rounded-md px-3 py-2 text-sm transition-colors
                                {{ ! isset($category) ? 'bg-primary-50 font-semibold text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        >
                            All Products
                        </a>

                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('shop.category', [$cat->id, $cat->slug]) }}"
                                class="flex items-center justify-between rounded-md px-3 py-2 text-sm transition-colors
                                    {{ isset($category) && $category->id === $cat->id ? 'bg-primary-50 font-semibold text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                            >
                                <span>{{ $cat->name }}</span>
                                @if (isset($category) && $category->id === $cat->id)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- Product grid --}}
            <div class="min-w-0 flex-1">
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
                        <p class="text-lg font-medium text-gray-500">No products found.</p>
                        <a href="{{ route('shop.index') }}" class="mt-4 text-sm text-primary-600 hover:underline">Browse all products</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Use event delegation so the listener survives Vue's deferred DOM replacement
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#filterToggle')) { return }
        var sidebar = document.getElementById('filterSidebar')
        if (!sidebar) { return }
        sidebar.style.display = sidebar.style.display === 'block' ? '' : 'block'
    })
</script>
@endpush
