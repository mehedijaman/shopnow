@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        @if (isset($searchText))
            <li class="flex shrink-0 items-center gap-1">
                <a href="{{ route('shop.index') }}" class="hover:text-site-link hover:underline">{{ __('site.nav.shop') }}</a>
                <i class="ri-arrow-right-s-line text-site-muted"></i>
            </li>
            <li class="min-w-0">
                <span class="block truncate font-semibold text-site-text dark:text-site-text">{{ __('product::site.shop.search_prefix') }} &ldquo;{{ $searchText }}&rdquo;</span>
            </li>
        @elseif (isset($category))
            <li class="flex shrink-0 items-center gap-1">
                <a href="{{ route('shop.index') }}" class="hover:text-site-link hover:underline">{{ __('site.nav.shop') }}</a>
                <i class="ri-arrow-right-s-line text-site-muted"></i>
            </li>
            <li class="min-w-0">
                <span class="block truncate font-semibold text-site-text dark:text-site-text">{{ $category->name }}</span>
            </li>
        @else
            <li class="min-w-0">
                <span class="font-semibold text-site-text dark:text-site-text">{{ __('site.nav.shop') }}</span>
            </li>
        @endif
    </x-breadcrumb>

    <div class="bg-site-surface-muted/50 dark:bg-site-surface-muted/50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8">
                @if (isset($searchText))
                    <h1 class="text-3xl font-bold tracking-tight text-site-text dark:text-site-text">{{ __('product::site.shop.search_results_for') }} &ldquo;{{ $searchText }}&rdquo;</h1>
                @elseif (isset($category))
                    <h1 class="text-3xl font-bold tracking-tight text-site-text dark:text-site-text">{{ $category->name }}</h1>
                @else
                    <h1 class="text-3xl font-bold tracking-tight text-site-text dark:text-site-text">{{ __('product::site.shop.all_products') }}</h1>
                @endif
            </div>

            {{-- Mobile filter toggle --}}
            <div class="mb-6 lg:hidden">
                <button
                    id="filterToggle"
                    type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-site-divider bg-site-surface px-4 py-3.5 text-[15px] font-semibold text-site-text shadow-sm transition-all hover:bg-site-surface-muted focus:outline-none focus:ring-2 focus:ring-site-focus/20 dark:border-site-divider dark:bg-site-surface-muted dark:text-site-text dark:hover:bg-site-surface-muted"
                >
                    <i class="ri-filter-3-line text-lg"></i>
                    {{ __('product::site.shop.filter_by_category') }}
                </button>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">

                {{-- Sidebar filter --}}
                <aside
                    id="filterSidebar"
                    class="hidden w-full shrink-0 lg:block lg:w-64"
                >
                    <div class="sticky top-24 overflow-hidden rounded-2xl border border-site-divider bg-site-surface shadow-sm dark:border-site-divider dark:bg-site-surface-muted">
                        <div class="flex items-center justify-between border-b border-site-divider bg-site-surface-muted/50 px-5 py-4 dark:border-site-divider dark:bg-site-surface-muted/50">
                            <h3 class="text-[13px] font-bold uppercase tracking-wider text-site-text dark:text-site-text">{{ __('product::site.shop.categories') }}</h3>
                            @if (isset($category))
                                <a
                                    href="{{ route('shop.index') }}"
                                    class="text-xs font-semibold text-site-link transition-colors hover:text-site-link dark:text-site-link"
                                >
                                    {{ __('product::site.shop.clear_all') }}
                                </a>
                            @endif
                        </div>

                        <nav class="flex flex-col p-3">
                            <a
                                href="{{ route('shop.index') }}"
                                class="group flex items-center rounded-xl px-3 py-2.5 text-[15px] transition-colors
                                    {{ ! isset($category) ? 'bg-site-primary-soft font-semibold text-site-link dark:bg-site-primary/20 dark:text-site-link' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-muted dark:hover:bg-site-surface-muted dark:hover:text-site-on-strong' }}"
                            >
                                <span class="flex-1">{{ __('product::site.shop.all_products') }}</span>
                                @if (!isset($category))
                                    <i class="ri-check-line text-lg text-site-link dark:text-site-link"></i>
                                @endif
                            </a>

                            @foreach ($categories as $cat)
                                <a
                                    href="{{ route('shop.category', [$cat->id, $cat->slug]) }}"
                                    class="group flex items-center rounded-xl px-3 py-2.5 text-[15px] transition-colors
                                        {{ isset($category) && $category->id === $cat->id ? 'bg-site-primary-soft font-semibold text-site-link dark:bg-site-primary/20 dark:text-site-link' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-muted dark:hover:bg-site-surface-muted dark:hover:text-site-on-strong' }}"
                                >
                                    <span class="flex-1">{{ $cat->name }}</span>
                                    @if (isset($category) && $category->id === $cat->id)
                                        <i class="ri-check-line text-lg text-site-link dark:text-site-link"></i>
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
                            <p class="text-sm font-medium text-site-muted dark:text-site-muted">
                                {!! __('product::site.shop.showing_products', [
                                    'shown' => '<span class="font-bold text-site-text dark:text-site-text">' . $products->count() . '</span>',
                                    'total' => $products->total(),
                                ]) !!}
                            </p>
                        </div>

                        <section class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 xl:grid-cols-3">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </section>

                        <div class="mt-10">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-site-divider bg-site-surface py-24 text-center dark:border-site-divider dark:bg-site-surface-muted">
                            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-site-surface-muted dark:bg-site-surface-muted">
                                <i class="ri-search-line text-4xl text-site-muted"></i>
                            </div>
                            <h3 class="mb-1 text-lg font-bold text-site-text dark:text-site-text">{{ __('product::site.shop.no_products_found') }}</h3>
                            <p class="mb-6 text-sm text-site-muted dark:text-site-muted">{{ __('product::site.shop.no_products_hint') }}</p>
                            <a href="{{ route('shop.index') }}" class="rounded-full bg-site-primary px-6 py-2.5 text-sm font-semibold text-site-on-primary shadow-sm transition-all hover:bg-site-primary-hover hover:shadow-md">
                                {{ __('product::site.shop.clear_all_filters') }}
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
    if (window.ShopNowTracking && window.ShopNowTracking.pixelEnabled && !window.ShopNowTracking.gtmEnabled) {
        window.ShopNowTracking.track('Search', {
            search_string: @json($searchText),
            content_category: 'shop',
        })
    }
    @endif

    window.dataLayer = window.dataLayer || []
    @php
        $listName = isset($category) ? $category->name : (isset($searchText) ? 'Search: '.$searchText : 'All Products');
        $viewItemListProducts = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) ($p->sale_price ?? $p->price),
            'category' => $p->category?->name,
            'brand' => $p->brand?->name,
        ])->values()->all();
    @endphp
    @if ($products->count())
    ;(function () {
        var listName = {!! json_encode($listName) !!}
        var products = {!! json_encode($viewItemListProducts) !!}
        if (products.length > 0 && window.ShopNowTracking && window.ShopNowTracking.gtmEnabled) {
            var viewItemListPayload = {
                event: 'view_item_list',
                ecommerce: {
                    currency: 'BDT',
                    value: products.reduce(function (s, p) { return s + (p.price || 0) }, 0),
                    item_list_name: listName,
                    items: products.map(function (p, i) {
                        return {
                            item_id: String(p.id),
                            item_name: p.name,
                            price: Number(p.price || 0),
                            item_category: p.category || undefined,
                            item_brand: p.brand || undefined,
                            item_list_id: listName,
                            index: i,
                            quantity: 1,
                        }
                    })
                }
            }
            console.log('[GTM] view_item_list', viewItemListPayload)
            window.dataLayer.push(viewItemListPayload)
        }
    })()
    @endif
</script>
@endpush
