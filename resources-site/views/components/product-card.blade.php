<div class="group w-full max-w-sm overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg">
    {{-- Image --}}
    <a href="{{ route('shop.product', [$product->id, $product->slug]) }}" class="relative block aspect-[3/4] w-full overflow-hidden bg-gray-100">
        <img
            src="{{ $product->image_url }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105"
        >

        {{-- Badges --}}
        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->featured)
                <span class="rounded-full bg-amber-400 px-2.5 py-0.5 text-xs font-semibold uppercase  text-white shadow">
                    Featured
                </span>
            @endif
            @if ($product->sale_price)
                <span class="rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-semibold uppercase  text-white shadow">
                    Sale
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
            <p class="text-xs font-medium uppercase st text-gray-400">
                {{ $product->category->name }}
            </p>
        @endif

        <h2 class="truncate font-semibold text-gray-800 transition-colors group-hover:text-blue-600">
            <a href="{{ route('shop.product', [$product->id, $product->slug]) }}">
                {{ $product->name }}
            </a>
        </h2>

        <div class="mt-1 flex items-baseline justify-center gap-2">
            <span class="text-base font-bold text-gray-900">
                @if ($product->sale_price)
                    {{ $product->sale_price }}
                @else
                    {{ $product->price }}
                @endif
                BDT
            </span>
            @if ($product->sale_price)
                <span class="text-sm text-gray-400 line-through">
                    {{ $product->price }} BDT
                </span>
            @endif
        </div>

        <div class="mt-3">
            <add-to-cart-button :product="{{ json_encode($product) }}"></add-to-cart-button>
        </div>
    </div>
</div>
