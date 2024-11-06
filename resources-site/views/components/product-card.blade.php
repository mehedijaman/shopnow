<div class="w-80 rounded border border-gray-300 bg-white shadow">
    <div
        class="flex h-48 w-full flex-col justify-between bg-gray-200 bg-cover bg-center p-4"
        style="background-image: url('{{ $product->image_url }}')"
    >
        <div class="flex justify-between">
            <span></span>
            <button class="text-white hover:text-blue-500">
                <i class="ri-heart-line text-2xl"></i>
            </button>
        </div>
        <div class="flex justify-between">
            <span
                class="select-none rounded border border-green-500 bg-green-50 p-0.5 text-xs font-medium uppercase text-green-700"
            >
                @if ($product->active)
                    Available
                @else
                        Not Available
                @endif
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
            <a href="{{ route('site.product.show', $product) }}">
                {{ $product->name }}
            </a>
        </h1>
        {{-- <p class="mt-1 text-center text-gray-800">{{ $product->price }} BDT</p> --}}
        <div class="mt-1 text-center text-gray-800">
            <span class="mr-2 text-lg">
                @if ($product->sale_price)
                    {{ $product->sale_price }}
                @else
                    {{ $product->price }}
                @endif
                BDT
            </span>
            @if ($product->sale_price)
                <span class="text-sm text-gray-500 line-through">
                    {{ $product->price }} BDT
                </span>
            @endif
        </div>
        <!-- Quantity Counter -->

        <div class="mt-2 inline-flex items-center">
            <button
                class="inline-flex items-center rounded-l border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
            >
                <i class="ri-subtract-line"></i>
            </button>
            <div
                class="inline-flex select-none items-center border-b border-t border-gray-100 bg-gray-100 px-4 py-1 text-gray-600 hover:bg-gray-100"
            >
                1
            </div>
            <button
                class="inline-flex items-center rounded-r border border-r border-gray-200 bg-white px-2 py-1 text-gray-600 hover:bg-gray-100 active:bg-gray-200 disabled:opacity-50"
            >
                <i class="ri-add-line"></i>
            </button>
        </div>

        <button
            class="mt-4 flex w-full items-center justify-center rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 active:bg-blue-700 disabled:opacity-50"
        >
            Add to Cart
            <i class="ri-shopping-cart-fill ml-2 text-xl"></i>
        </button>
    </div>
</div>
