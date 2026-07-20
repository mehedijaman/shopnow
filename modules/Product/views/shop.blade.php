@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="bg-gray-50/50 dark:bg-gray-900/50">
        {{-- Elegant Header Section --}}
        <div class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <x-breadcrumb class="mb-4">
                    @if (isset($searchText))
                        <li class="flex shrink-0 items-center gap-1">
                            <a href="{{ route('shop.index') }}" class="hover:text-primary-600 hover:underline">Shop</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                        </li>
                        <li class="min-w-0">
                            <span class="block truncate font-medium text-gray-800 dark:text-gray-200">Search: &ldquo;{{ $searchText }}&rdquo;</span>
                        </li>
                    @elseif (isset($category))
                        <li class="flex shrink-0 items-center gap-1">
                            <a href="{{ route('shop.index') }}" class="hover:text-primary-600 hover:underline">Shop</a>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                        </li>
                        <li class="min-w-0">
                            <span class="block truncate font-medium text-gray-800 dark:text-gray-200">{{ $category->name }}</span>
                        </li>
                    @else
                        <li class="min-w-0">
                            <span class="font-medium text-gray-800 dark:text-gray-200">Shop</span>
                        </li>
                    @endif
                </x-breadcrumb>

                @if (isset($searchText))
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Search Results for &ldquo;{{ $searchText }}&rdquo;</h1>
                @elseif (isset($category))
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $category->name }}</h1>
                @else
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">All Products</h1>
                @endif
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{-- Mobile filter toggle --}}
            <div class="mb-6 lg:hidden">
                <button
                    id="filterToggle"
                    type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-[15px] font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    <i class="ri-filter-3-line text-lg"></i>
                    Filter by Category
                </button>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">

                {{-- Sidebar filter --}}
                <aside
                    id="filterSidebar"
                    class="hidden w-full shrink-0 lg:block lg:w-64"
                >
                    <div class="sticky top-24 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
                            <h3 class="text-[13px] font-bold uppercase tracking-wider text-gray-900 dark:text-white">Categories</h3>
                            @if (isset($category))
                                <a
                                    href="{{ route('shop.index') }}"
                                    class="text-xs font-semibold text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400"
                                >
                                    Clear All
                                </a>
                            @endif
                        </div>

                        <nav class="flex flex-col p-3">
                            <a
                                href="{{ route('shop.index') }}"
                                class="group flex items-center rounded-xl px-3 py-2.5 text-[15px] transition-colors
                                    {{ ! isset($category) ? 'bg-primary-50 font-semibold text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }}"
                            >
                                <span class="flex-1">All Products</span>
                                @if (!isset($category))
                                    <i class="ri-check-line text-lg text-primary-600 dark:text-primary-400"></i>
                                @endif
                            </a>

                            @foreach ($categories as $cat)
                                <a
                                    href="{{ route('shop.category', [$cat->id, $cat->slug]) }}"
                                    class="group flex items-center rounded-xl px-3 py-2.5 text-[15px] transition-colors
                                        {{ isset($category) && $category->id === $cat->id ? 'bg-primary-50 font-semibold text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }}"
                                >
                                    <span class="flex-1">{{ $cat->name }}</span>
                                    @if (isset($category) && $category->id === $cat->id)
                                        <i class="ri-check-line text-lg text-primary-600 dark:text-primary-400"></i>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </aside>

                {{-- Product grid --}}
                <div class="min-w-0 flex-1">
                    @if ($products->count())
                        <div class="mb-6 flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Showing <span class="font-bold text-gray-900 dark:text-white">{{ $products->count() }}</span> of {{ $products->total() }} products
                            </p>
                        </div>

                        <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </section>

                        <div class="mt-10">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-white py-24 text-center dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-800">
                                <i class="ri-search-line text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="mb-1 text-lg font-bold text-gray-900 dark:text-white">No products found</h3>
                            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">We couldn't find anything matching your current filters.</p>
                            <a href="{{ route('shop.index') }}" class="rounded-full bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-700 hover:shadow-md">
                                Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
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
        // Smooth slide-down style for mobile toggle
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden')
            sidebar.classList.add('block')
        } else {
            sidebar.classList.remove('block')
            sidebar.classList.add('hidden')
        }
    })

    @if (isset($searchText))
    if (window.ShopNowTracking) {
        window.ShopNowTracking.track('Search', {
            search_string: @json($searchText),
            content_category: 'shop',
        })
    }
    @endif
</script>
@endpush
