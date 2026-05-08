@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
    <script>
        function setMainImage(btn, src) {
            document.getElementById('mainImage').src = src
            document.querySelectorAll('.gallery-thumb').forEach(el => el.classList.remove('border-blue-500'))
            btn.classList.add('border-blue-500')
        }
    </script>
@endsection

@section('content')
    @php
        $allImages = collect([$product->image_url])->merge($gallery)->filter()->values();
    @endphp

    <!-- Breadcrumb -->
    <div class="border-b border-gray-100 bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('site.index') }}" class="hover:text-gray-700">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="{{ route('shop.index') }}" class="hover:text-gray-700">Shop</a>
                @if ($product->category)
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                    <a href="{{ route('shop.category', [$product->category->id, $product->category->slug]) }}" class="hover:text-gray-700">
                        {{ $product->category->name }}
                    </a>
                @endif
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="truncate font-medium text-gray-800">{{ Str::limit($product->name, 40) }}</span>
            </nav>
        </div>
    </div>

    <!-- Product Section -->
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">

            <!-- Left: Image Gallery -->
            <div>
                <!-- Main Image -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <img
                        id="mainImage"
                        src="{{ $allImages->first() ?? 'https://placehold.co/800x800/f3f4f6/9ca3af?text=No+Image' }}"
                        alt="{{ $product->name }}"
                        class="h-auto max-h-[480px] w-full object-contain"
                    />
                </div>

                <!-- Thumbnails -->
                @if ($allImages->count() > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($allImages as $index => $imgUrl)
                            <button
                                type="button"
                                onclick="setMainImage(this, '{{ $imgUrl }}')"
                                class="gallery-thumb shrink-0 overflow-hidden rounded-lg border-2 border-transparent transition hover:border-blue-500 focus:outline-none {{ $index === 0 ? 'border-blue-500' : '' }}"
                            >
                                <img
                                    src="{{ $imgUrl }}"
                                    alt="{{ $product->name }} image {{ $index + 1 }}"
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
                            class="rounded-full bg-blue-50 px-3 py-0.5 text-xs font-medium text-blue-600 hover:bg-blue-100"
                        >
                            {{ $product->category->name }}
                        </a>
                    @endif
                    @if ($product->featured)
                        <span class="rounded-full bg-amber-50 px-3 py-0.5 text-xs font-medium text-amber-600">
                            <i class="ri-star-fill mr-0.5"></i> Featured
                        </span>
                    @endif
                    @if ($product->quantity <= 0)
                        <span class="rounded-full bg-red-50 px-3 py-0.5 text-xs font-medium text-red-600">
                            Out of Stock
                        </span>
                    @elseif ($product->quantity < 10)
                        <span class="rounded-full bg-orange-50 px-3 py-0.5 text-xs font-medium text-orange-600">
                            Only {{ $product->quantity }} left
                        </span>
                    @else
                        <span class="rounded-full bg-green-50 px-3 py-0.5 text-xs font-medium text-green-600">
                            <i class="ri-checkbox-circle-line mr-0.5"></i> In Stock
                        </span>
                    @endif
                </div>

                <!-- Name -->
                <h1 class="text-2xl font-bold leading-snug text-gray-900 sm:text-3xl">
                    {{ $product->name }}
                </h1>

                <!-- Price -->
                <div class="mt-4 flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-gray-900">
                        ৳{{ number_format($product->sale_price ?? $product->price, 2) }}
                    </span>
                    @if ($product->sale_price && $product->price && $product->sale_price < $product->price)
                        <span class="text-lg text-gray-400 line-through">
                            ৳{{ number_format($product->price, 2) }}
                        </span>
                        @php
                            $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                        @endphp
                        <span class="rounded-md bg-red-100 px-2 py-0.5 text-sm font-semibold text-red-600">
                            -{{ $discount }}%
                        </span>
                    @endif
                </div>

                <!-- Summary -->
                @if ($product->summary)
                    <p class="mt-4 leading-relaxed text-gray-600">{{ $product->summary }}</p>
                @endif

                <hr class="my-5 border-gray-100" />

                <!-- Product Meta -->
                <dl class="space-y-2 text-sm">
                    @if ($product->brand)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-gray-500">Brand</dt>
                            <dd class="text-gray-800">{{ $product->brand->name }}</dd>
                        </div>
                    @endif
                    @if ($product->unit)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-gray-500">Unit</dt>
                            <dd class="text-gray-800">{{ $product->unit }}</dd>
                        </div>
                    @endif
                    @if ($product->min_order)
                        <div class="flex gap-2">
                            <dt class="w-24 shrink-0 font-medium text-gray-500">Min Order</dt>
                            <dd class="text-gray-800">{{ $product->min_order }} {{ $product->unit }}</dd>
                        </div>
                    @endif
                </dl>

                <hr class="my-5 border-gray-100" />

                <!-- Add to Cart -->
                <div class="flex flex-wrap items-center gap-3">
                    <add-to-cart-button :product="{{ json_encode($product) }}"></add-to-cart-button>
                </div>

                <!-- Tags -->
                @if ($product->tags?->count())
                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">Tags:</span>
                        @foreach ($product->tags as $tag)
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-600">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        @if ($product->description)
            <div class="mt-12 border-t border-gray-100 pt-10">
                <h2 class="mb-6 text-xl font-bold text-gray-900">Product Description</h2>
                <div class="prose prose-gray max-w-none leading-relaxed text-gray-700">
                    {!! $product->description !!}
                </div>
            </div>
        @endif
    </div>
@endsection
