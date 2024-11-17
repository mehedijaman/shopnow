<div class="w-full max-w-sm rounded border border-gray-300 bg-white shadow">
    <div
        class="flex h-48 w-full flex-col justify-between bg-gray-200 bg-cover bg-center p-4"
        style="background-image: url({{ $product->image_url }})"
    >
        <div class="flex justify-between">
            <span></span>
            @if (auth('customer')->check())
                <button class="text-white hover:text-blue-500">
                    <i class="ri-heart-line text-2xl"></i>
                </button>
            @endif
        </div>
        <div class="flex justify-between">
            <span
                class="select-none rounded border border-green-500 bg-green-50 p-0.5 text-xs font-medium uppercase text-green-700"
            >
                <span>
                    @if ($product->active)
                        Available
                    @else
                            Not Available
                    @endif
                </span>
            </span>

            @if ($product->featured)
                <span
                    class="select-none rounded border border-green-500 bg-green-50 p-0.5 text-xs font-medium uppercase text-green-700"
                >
                    Featured
                </span>
            @endif
        </div>
    </div>
    <div class="flex flex-col items-center p-4">
        <p class="text-center text-xs font-light text-gray-400">
            {{ $product->category?->name }}
        </p>
        <h1
            class="mt-1 text-center text-gray-800 hover:text-blue-500 hover:underline"
        >
            <a
                href="{{ route('shop.product', [$product->id, $product->slug]) }}"
            >
                {{ $product->name }}
            </a>
        </h1>

        <div class="mt-1 text-center text-gray-800">
            <span class="mr-2 text-lg">
                @if ($product->sale_price)
                    <span>
                        {{ $product->sale_price }}
                    </span>
                @else
                    <span>{{ $product->price }}</span>
                @endif
                BDT
            </span>
            @if ($product->sale_price)
                <span class="text-sm text-gray-500 line-through">
                    {{ $product->price }} BDT
                </span>
            @endif
        </div>

        <add-to-cart-button
            :product="{{ json_encode($product) }}"
        ></add-to-cart-button>
    </div>
</div>
