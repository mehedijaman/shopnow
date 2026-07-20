<div class="group relative flex w-full max-w-sm flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:bg-gray-900 dark:ring-gray-800">
    {{-- Image --}}
    <a href="{{ route('shop.product', [$product->id, $product->slug]) }}" class="relative block aspect-[3/4] w-full overflow-hidden bg-gray-50 dark:bg-gray-800">
        <img
            src="{{ $product->image_url ?? 'https://placehold.co/600x800/f3f4f6/9ca3af?text=No+Image' }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="h-full w-full object-contain transition-transform duration-700 ease-out group-hover:scale-105"
        >

        {{-- Badges --}}
        <div class="absolute left-3 top-3 flex flex-col gap-2">
            @if ($product->featured)
                <span class="rounded bg-amber-500 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm">
                    Featured
                </span>
            @endif
            @if ($product->sale_price)
                <span class="rounded bg-red-600 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm">
                    Sale
                </span>
            @endif
            @if ($product->type?->value === 'variable')
                <span class="rounded bg-purple-600 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm">
                    Variable
                </span>
            @elseif ($product->type?->value === 'bundle')
                <span class="rounded bg-indigo-600 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm">
                    Bundle
                </span>
            @endif
            @if ($product->type?->value === 'variable')
                @php
                    $hasVariationStock = $product->variations->contains(fn($v) => $v->active && $v->quantity > 0);
                @endphp
                @if (! $hasVariationStock)
                    <span class="rounded bg-gray-900 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm dark:bg-gray-700">
                        Out of Stock
                    </span>
                @endif
            @elseif ($product->quantity <= 0)
                <span class="rounded bg-gray-900 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm dark:bg-gray-700">
                    Out of Stock
                </span>
            @endif
        </div>

        {{-- Wishlist button --}}
        @if (auth('customer')->check())
            <button
                class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white text-gray-400 shadow-md transition-all duration-300 hover:scale-110 hover:text-red-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-red-400"
                aria-label="Add to wishlist"
            >
                <i class="ri-heart-line text-lg leading-none"></i>
            </button>
        @endif
    </a>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-5">
        @if ($product->category?->name)
            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">
                {{ $product->category->name }}
            </p>
        @endif

        <h3 class="mb-3 line-clamp-2 text-base font-bold leading-tight text-gray-900 transition-colors group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
            <a href="{{ route('shop.product', [$product->id, $product->slug]) }}">
                <span class="absolute inset-0"></span>
                {{ $product->name }}
            </a>
        </h3>

        {{-- Price & Action area --}}
        <div class="relative z-10 mt-auto pt-2 flex flex-col justify-end">
            <div class="mb-4 flex flex-col">
                @if ($product->type?->value === 'variable')
                    {{-- Variable: show "From ৳X" price range --}}
                    @php
                        $activeVariations = $product->variations->where('active', true)->where('price', '>', 0);
                        $minPrice = $activeVariations->min(fn($v) => $v->sale_price ?: $v->price);
                        $maxPrice = $activeVariations->max(fn($v) => $v->sale_price ?: $v->price);
                    @endphp
                    @if ($minPrice)
                        @if ($maxPrice && $maxPrice > $minPrice)
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">From</span>
                        @endif
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">
                            ৳{{ number_format($minPrice, 2) }}
                        </span>
                    @else
                        <span class="text-sm font-bold text-gray-400">Price varies</span>
                    @endif
                @elseif ($product->type?->value === 'bundle')
                    {{-- Bundle: show calculated price from bundle config --}}
                    <span class="text-xl font-extrabold text-gray-900 dark:text-white">
                        ৳{{ number_format($product->sale_price ?: $product->price, 2) }}
                    </span>
                @else
                    {{-- Simple: show price / sale price --}}
                    @if ($product->sale_price && $product->price && $product->sale_price < $product->price)
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold text-red-600 dark:text-red-400">
                                ৳{{ number_format($product->sale_price, 2) }}
                            </span>
                            <span class="text-sm font-medium text-gray-400 line-through dark:text-gray-500">
                                ৳{{ number_format($product->price, 2) }}
                            </span>
                        </div>
                    @else
                        <span class="text-xl font-extrabold text-gray-900 dark:text-white">
                            ৳{{ number_format($product->price, 2) }}
                        </span>
                    @endif
                @endif
            </div>

            {{-- Add to Cart / View Options --}}
            <div class="w-full">
                @if ($product->type?->value === 'variable')
                    <a
                        href="{{ route('shop.product', [$product->id, $product->slug]) }}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white transition-all hover:bg-primary-600 dark:bg-white dark:text-gray-900 dark:hover:bg-primary-500"
                    >
                        <i class="ri-eye-line text-lg leading-none"></i>
                        Select Options
                    </a>
                @else
                    <add-to-cart-button :product="{{ json_encode($product) }}"></add-to-cart-button>
                @endif
            </div>
        </div>
    </div>
</div>
