<div class="group w-full max-w-sm overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg">
    {{-- Image --}}
    <a href="{{ route('shop.product', [$product->id, $product->slug]) }}" class="relative block aspect-[3/4] w-full overflow-hidden bg-gray-100">
        <img
            src="{{ $product->image_url ?? 'https://placehold.co/600x800/f3f4f6/9ca3af?text=No+Image' }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105"
        >

        {{-- Badges --}}
        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->featured)
                <span class="rounded-full bg-amber-400 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                    Featured
                </span>
            @endif
            @if ($product->sale_price)
                <span class="rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                    Sale
                </span>
            @endif
            @if ($product->type?->value === 'variable')
                <span class="rounded-full bg-purple-500 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                    Variable
                </span>
            @elseif ($product->type?->value === 'bundle')
                <span class="rounded-full bg-indigo-500 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                    Bundle
                </span>
            @endif
            @if ($product->type?->value === 'variable')
                @php
                    $hasVariationStock = $product->variations->contains(fn($v) => $v->active && $v->quantity > 0);
                @endphp
                @if (! $hasVariationStock)
                    <span class="rounded-full bg-gray-700 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                        Out of Stock
                    </span>
                @endif
            @elseif ($product->quantity <= 0)
                <span class="rounded-full bg-gray-700 px-2.5 py-0.5 text-xs font-semibold uppercase text-white shadow">
                    Out of Stock
                </span>
            @endif
        </div>

        {{-- Wishlist button --}}
        @if (auth('customer')->check())
            <button
                class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-gray-500 shadow backdrop-blur-sm transition hover:bg-white hover:text-red-500"
                aria-label="Add to wishlist"
            >
                <i class="ri-heart-line text-lg"></i>
            </button>
        @endif
    </a>

    {{-- Body --}}
    <div class="flex flex-col items-center gap-1 p-4 text-center">
        @if ($product->category?->name)
            <p class="text-xs font-medium uppercase text-gray-400">
                {{ $product->category->name }}
            </p>
        @endif

        <h2 class="line-clamp-2 font-semibold text-gray-800 transition-colors group-hover:text-blue-600">
            <a href="{{ route('shop.product', [$product->id, $product->slug]) }}">
                {{ $product->name }}
            </a>
        </h2>

        {{-- Price display --}}
        <div class="mt-1 flex items-baseline justify-center gap-2">
            @if ($product->type?->value === 'variable')
                {{-- Variable: show "From ৳X" price range --}}
                @php
                    $activeVariations = $product->variations->where('active', true)->where('price', '>', 0);
                    $minPrice = $activeVariations->min(fn($v) => $v->sale_price ?: $v->price);
                    $maxPrice = $activeVariations->max(fn($v) => $v->sale_price ?: $v->price);
                @endphp
                @if ($minPrice)
                    <span class="text-base font-bold text-gray-900">
                        ৳{{ number_format($minPrice, 2) }}
                        @if ($maxPrice && $maxPrice > $minPrice) – ৳{{ number_format($maxPrice, 2) }} @endif
                    </span>
                    <span class="text-xs text-gray-400">from</span>
                @else
                    <span class="text-base font-bold text-gray-400">Price varies</span>
                @endif
            @elseif ($product->type?->value === 'bundle')
                {{-- Bundle: show calculated price from bundle config --}}
                <span class="text-base font-bold text-gray-900">
                    ৳{{ number_format($product->sale_price ?: $product->price, 2) }}
                </span>
            @else
                {{-- Simple: show price / sale price --}}
                <span class="text-base font-bold text-gray-900">
                    ৳{{ number_format($product->sale_price ?: $product->price, 2) }}
                </span>
                @if ($product->sale_price && $product->price && $product->sale_price < $product->price)
                    <span class="text-sm text-gray-400 line-through">
                        ৳{{ number_format($product->price, 2) }}
                    </span>
                @endif
            @endif
        </div>

        {{-- Add to Cart / View Options --}}
        <div class="mt-3">
            @if ($product->type?->value === 'variable')
                <a
                    href="{{ route('shop.product', [$product->id, $product->slug]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition-colors hover:bg-blue-100"
                >
                    <i class="ri-eye-line"></i>
                    Select Options
                </a>
            @else
                <add-to-cart-button :product="{{ json_encode($product) }}"></add-to-cart-button>
            @endif
        </div>
    </div>
</div>
