@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
    <script>
        function setMainImage(btn, src) {
            document.getElementById('mainImage').src = src
            document.querySelectorAll('.gallery-thumb').forEach(el => el.classList.remove('border-site-focus'))
            btn.classList.add('border-site-focus')
        }

        if (window.ShopNowTracking && window.ShopNowTracking.pixelEnabled && !window.ShopNowTracking.gtmEnabled) {
            window.ShopNowTracking.track('ViewContent', {
                content_ids: [String(@json($product->id))],
                content_type: 'product',
                content_name: @json($product->name),
                value: Number(@json($product->sale_price ?? $product->price ?? 0)),
                currency: 'BDT',
                category: @json($product->category?->name),
            })
        }

        if (window.ShopNowTracking && window.ShopNowTracking.gtmEnabled) {
            window.dataLayer = window.dataLayer || []
            var viewItemPayload = {
                event: 'view_item',
                ecommerce: {
                    currency: 'BDT',
                    value: Number(@json($product->sale_price ?? $product->price ?? 0)),
                    items: [{
                        item_id: String(@json($product->id)),
                        item_name: @json($product->name),
                        price: Number(@json($product->sale_price ?? $product->price ?? 0)),
                        item_category: @json($product->category?->name),
                        item_brand: @json($product->brand?->name),
                        quantity: 1,
                    }]
                }
            }
            console.log('[GTM] view_item', viewItemPayload)
            window.dataLayer.push(viewItemPayload)
        }
    </script>
@endsection

@section('content')
    @php
        $defaultLogo = setting('branding.logo_url') ?: asset('logo.png');
        $featuredImage = $product->image_url ?: $defaultLogo;
        $allImages = collect([$featuredImage])->merge($gallery)->filter()->values();
    @endphp

    <!-- Breadcrumb -->
    <div class="border-b border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted">
        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6">
            <nav aria-label="{{ __('common.breadcrumb') }}">
                <ol class="flex min-w-0 flex-wrap items-center gap-x-1 gap-y-1 text-sm text-site-muted dark:text-site-muted">
                    <li class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('site.index') }}" class="hover:text-site-link hover:underline">{{ __('site.nav.home') }}</a>
                        <i class="ri-arrow-right-s-line text-site-muted"></i>
                    </li>
                    <li class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('shop.index') }}" class="hover:text-site-link hover:underline">{{ __('site.nav.shop') }}</a>
                        @if ($product->category)
                            <i class="ri-arrow-right-s-line text-site-muted"></i>
                        @endif
                    </li>
                    @if ($product->category)
                        <li class="flex shrink-0 items-center gap-1">
                            <a href="{{ route('shop.category', [$product->category->id, $product->category->slug]) }}" class="hover:text-site-link hover:underline">
                                {{ $product->category->name }}
                            </a>
                            <i class="ri-arrow-right-s-line text-site-muted"></i>
                        </li>
                    @endif
                    <li class="min-w-0">
                        <span class="block truncate font-semibold text-site-text dark:text-site-text" title="{{ $product->name }}">{{ $product->name }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Section -->
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">

            <!-- Left: Image Gallery -->
            <div>
                <!-- Main Image -->
                <div class="flex items-center justify-center overflow-hidden rounded-xl bg-site-surface-muted/50 dark:bg-site-surface-muted/50">
                    <img
                        id="mainImage"
                        data-product-id="{{ $product->id }}"
                        src="{{ $allImages->first() ?? $defaultLogo }}"
                        alt="{{ $product->name }}"
                        class="h-auto max-h-[520px] w-full rounded-lg object-contain mx-auto transition-all duration-300"
                        onerror="this.src='{{ $defaultLogo }}';"
                    />
                </div>

                <!-- Thumbnails -->
                @if ($allImages->count() > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($allImages as $index => $imgUrl)
                            <button
                                type="button"
                                onclick="setMainImage(this, '{{ $imgUrl }}')"
                                class="gallery-thumb shrink-0 overflow-hidden rounded-lg border-2 border-transparent transition hover:border-site-focus focus:outline-none {{ $index === 0 ? 'border-site-focus' : '' }}"
                            >
                                <img
                                    src="{{ $imgUrl }}"
                                    alt="{{ __('site.product.image_alt', ['name' => $product->name, 'index' => $index + 1]) }}"
                                    class="h-16 w-16 object-cover sm:h-20 sm:w-20"
                                />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Product Details -->
            <div class="flex flex-col">

                <!-- Category + Featured badge -->
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    @if ($product->category)
                        <a
                            href="{{ route('shop.category', [$product->category->id, $product->category->slug]) }}"
                            class="rounded-full bg-site-primary-soft px-3 py-0.5 text-xs font-medium text-site-link hover:bg-site-primary-soft"
                        >
                            {{ $product->category->name }}
                        </a>
                    @endif
                    @if ($product->featured)
                        <span class="rounded-full bg-site-accent px-3 py-0.5 text-xs font-medium text-site-on-accent">
                            <i class="ri-star-fill mr-0.5"></i> {{ __('site.product.featured') }}
                        </span>
                    @endif
                    @if ($product->type?->value === 'variable')
                        <span class="rounded-full bg-purple-50 px-3 py-0.5 text-xs font-medium text-purple-600">
                            {{ __('site.product.variable') }}
                        </span>
                    @elseif ($product->type?->value === 'bundle')
                        <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-medium text-indigo-600">
                            {{ __('site.product.bundle') }}
                        </span>
                    @endif
                    @if ($product->is_virtual)
                        <span class="rounded-full bg-teal-50 px-3 py-0.5 text-xs font-medium text-teal-600">
                            <i class="ri-wifi-line mr-0.5"></i> {{ __('site.product.virtual') }}
                        </span>
                    @endif
                    @if ($product->is_downloadable)
                        <span class="rounded-full bg-cyan-50 px-3 py-0.5 text-xs font-medium text-cyan-600">
                            <i class="ri-download-line mr-0.5"></i> {{ __('site.product.downloadable') }}
                        </span>
                    @endif
                    @if ($product->type?->value === 'variable')
                        @php
                            $hasStock = $variations->contains(fn($v) => $v['active'] && $v['quantity'] > 0);
                        @endphp
                        @if ($hasStock)
                            <span class="rounded-full bg-green-50 px-3 py-0.5 text-xs font-medium text-green-600">
                                <i class="ri-checkbox-circle-line mr-0.5"></i> {{ __('site.product.available_in_variations') }}
                            </span>
                        @else
                            <span class="rounded-full bg-red-50 px-3 py-0.5 text-xs font-medium text-red-600">
                                {{ __('site.product.out_of_stock') }}
                            </span>
                        @endif
                    @elseif ($product->quantity <= 0)
                        <span class="rounded-full bg-red-50 px-3 py-0.5 text-xs font-medium text-red-600">
                            {{ __('site.product.out_of_stock') }}
                        </span>
                    @elseif ($product->quantity < 10)
                        <span class="rounded-full bg-orange-50 px-3 py-0.5 text-xs font-medium text-orange-600">
                            {{ __('site.product.only_left', ['count' => $product->quantity]) }}
                        </span>
                    @else
                        <span class="rounded-full bg-green-50 px-3 py-0.5 text-xs font-medium text-green-600">
                            <i class="ri-checkbox-circle-line mr-0.5"></i> {{ __('site.product.in_stock') }}
                        </span>
                    @endif
                </div>

                <!-- Name -->
                <h1 class="text-2xl font-bold leading-snug text-site-text dark:text-site-text sm:text-3xl">
                    {{ $product->name }}
                </h1>

                <!-- Price -->
                <div class="mt-4 flex items-baseline gap-3">
                    @if ($product->type?->value === 'variable' && count($variations) > 0)
                        {{-- Variable product: show price range from active variations --}}
                        @php
                            $activeVariations = collect($variations)->filter(fn($v) => $v['active'] && ($v['price'] ?? 0) > 0);
                            $minPrice = $activeVariations->min(fn($v) => $v['sale_price'] ?: $v['price']);
                            $maxPrice = $activeVariations->max(fn($v) => $v['sale_price'] ?: $v['price']);
                        @endphp
                        @if ($minPrice)
                            <span class="text-3xl font-bold text-site-text dark:text-site-text">
                                ৳{{ rtrim(rtrim(number_format($minPrice, 2), '0'), '.') }}
                                @if ($maxPrice && $maxPrice > $minPrice) – ৳{{ rtrim(rtrim(number_format($maxPrice, 2), '0'), '.') }} @endif
                            </span>
                            <span class="text-sm text-site-muted">{{ __('site.product.price_from') }}</span>
                        @else
                            <span class="text-3xl font-bold text-site-muted">{{ __('site.product.select_options_for_price') }}</span>
                        @endif
                    @elseif ($product->type?->value === 'bundle')
                        {{-- Bundle product: show bundle price --}}
                        <span class="text-3xl font-bold text-site-text dark:text-site-text">
                            ৳{{ rtrim(rtrim(number_format($product->sale_price ?? $product->price, 2), '0'), '.') }}
                        </span>
                    @else
                        {{-- Simple product --}}
                        <span class="text-3xl font-bold text-site-text dark:text-site-text">
                            ৳{{ rtrim(rtrim(number_format($product->sale_price ?? $product->price, 2), '0'), '.') }}
                        </span>
                        @if ($product->sale_price && $product->price && $product->sale_price < $product->price)
                            <span class="text-lg text-site-muted line-through">
                                ৳{{ rtrim(rtrim(number_format($product->price, 2), '0'), '.') }}
                            </span>
                            @php
                                $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                            @endphp
                            <span class="rounded-md bg-red-100 px-2 py-0.5 text-sm font-semibold text-red-600">
                                -{{ $discount }}%
                            </span>
                        @endif
                    @endif
                </div>

                <!-- Summary -->
                @if ($product->summary)
                    <p class="mt-4 leading-relaxed text-site-muted dark:text-site-text">{{ $product->summary }}</p>
                @endif

                <hr class="my-5 border-site-divider dark:border-site-divider" />

                <!-- Product Meta -->
                <dl class="space-y-2 text-sm">
                    @if ($product->brand)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-site-muted dark:text-site-muted">{{ __('site.product.brand') }}</dt>
                            <dd class="text-site-text dark:text-site-text">{{ $product->brand->name }}</dd>
                        </div>
                    @endif
                    @if ($product->unit)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-site-muted dark:text-site-muted">{{ __('site.product.unit') }}</dt>
                            <dd class="text-site-text dark:text-site-text">{{ $product->unit }}</dd>
                        </div>
                    @endif
                    @if ($product->min_order)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-site-muted dark:text-site-muted">{{ __('site.product.min_order') }}</dt>
                            <dd class="text-site-text dark:text-site-text">{{ $product->min_order }} {{ $product->unit }}</dd>
                        </div>
                    @endif
                </dl>

                <hr class="my-5 border-site-divider dark:border-site-divider" />

                <!-- Bundle items (show individual items for bundle products) -->
                @if ($product->type?->value === 'bundle')
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-site-text dark:text-site-text">{{ __('site.product.bundle_includes') }}</h3>
                        <ul class="space-y-1">
                            @foreach ($bundleItems as $bi)
                                <li class="flex items-center gap-2 text-sm text-site-text dark:text-site-text">
                                    <i class="ri-checkbox-circle-fill text-green-500 text-xs"></i>
                                    @if ($bi['is_optional'])
                                        <span class="text-xs text-site-muted italic">{{ __('site.product.optional') }}</span>
                                    @endif
                                    {{ $bi['child_product_name'] }}
                                    @if ($bi['quantity'] > 1) &times; {{ $bi['quantity'] }} @endif
                                    @if ($bi['price_override'])
                                        <span class="text-xs text-site-muted">(৳{{ rtrim(rtrim(number_format($bi['price_override'], 2), '0'), '.') }} {{ __('site.product.each') }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <hr class="my-5 border-site-divider dark:border-site-divider" />
                @endif

                <!-- Add to Cart -->
                <div class="flex flex-wrap items-center gap-3">
                    <add-to-cart-button
                        :product="{{ json_encode($product) }}"
                        :variations="{{ json_encode($variations ?? []) }}"
                        :variation-attributes="{{ json_encode($variationAttributes ?? (object)[]) }}"
                        :bundle-items="{{ json_encode($bundleItems ?? []) }}"
                    ></add-to-cart-button>
                </div>

                <!-- Tags -->
                @if ($product->tags?->count())
                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-site-muted dark:text-site-muted">{{ __('site.product.tags') }}</span>
                        @foreach ($product->tags as $tag)
                            <span class="rounded-full bg-site-surface-muted dark:bg-site-surface-muted px-2.5 py-0.5 text-xs text-site-muted dark:text-site-text">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif

                <!-- Downloadable Files -->
                @if ($product->is_downloadable && $productFiles->count())
                    <div class="mt-5 rounded-lg border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted p-4">
                        <h3 class="mb-2 text-sm font-semibold text-site-text dark:text-site-text">
                            <i class="ri-download-cloud-line mr-1 text-cyan-600"></i>
                            {{ __('site.product.downloadable_files') }}
                        </h3>
                        <p class="mb-3 text-xs text-site-muted dark:text-site-muted">{{ __('site.product.available_after_purchase') }}</p>
                        <ul class="space-y-1.5">
                            @foreach ($productFiles as $pf)
                                <li class="flex items-center gap-2 text-sm text-site-text dark:text-site-text">
                                    <i class="ri-file-line text-site-muted"></i>
                                    <span class="font-medium">{{ $pf['name'] }}</span>
                                    @if ($pf['file_size'])
                                        <span class="text-xs text-site-muted">({{ $pf['file_size'] }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        @if ($product->description)
            <div class="mt-12 border-t border-site-divider dark:border-site-divider pt-10">
                <h2 class="mb-6 text-xl font-bold text-site-text dark:text-site-text">{{ __('site.product.description') }}</h2>
                <div class="prose prose-gray max-w-none leading-relaxed text-site-text dark:text-site-text">
                    {!! $product->description !!}
                </div>
            </div>
        @endif
    </div>
@endsection
