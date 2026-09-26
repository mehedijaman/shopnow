@php
    $variationsData = [];
    $variationAttributesData = (object) [];

    if ($product->type?->value === 'variable') {
        $activeVariations = $product->variations->where('active', true);

        $variationsData = $activeVariations->map(function ($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'price' => (float) $v->price,
                'sale_price' => $v->sale_price ? (float) $v->sale_price : null,
                'quantity' => (int) $v->quantity,
                'active' => (bool) $v->active,
                'image_url' => $v->image_url,
                'attribute_value_ids' => $v->attributeValues->pluck('id')->toArray(),
            ];
        })->values()->toArray();

        $attrMap = [];
        $activeValueIds = $activeVariations->flatMap(fn($v) => $v->attributeValues->pluck('id'))->unique()->toArray();

        // 1. Extract attribute values directly linked to product that belong to active variations
        foreach ($product->attributeValues as $value) {
            if (in_array($value->id, $activeValueIds)) {
                $attr = $value->attribute;
                if ($attr) {
                    $attrMap[$attr->id] ??= [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'input_type' => $attr->input_type,
                        'values' => [],
                    ];
                    $existingValIds = array_column($attrMap[$attr->id]['values'], 'id');
                    if (! in_array($value->id, $existingValIds)) {
                        $attrMap[$attr->id]['values'][] = [
                            'id' => $value->id,
                            'value' => $value->value,
                            'swatch' => $value->swatch,
                        ];
                    }
                }
            }
        }

        // 2. Extract attribute values linked to active variations
        foreach ($activeVariations as $v) {
            foreach ($v->attributeValues as $value) {
                $attr = $value->attribute;
                if ($attr) {
                    $attrMap[$attr->id] ??= [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'input_type' => $attr->input_type,
                        'values' => [],
                    ];
                    $existingValIds = array_column($attrMap[$attr->id]['values'], 'id');
                    if (! in_array($value->id, $existingValIds)) {
                        $attrMap[$attr->id]['values'][] = [
                            'id' => $value->id,
                            'value' => $value->value,
                            'swatch' => $value->swatch,
                        ];
                    }
                }
            }
        }

        $variationAttributesData = $attrMap;
    }
@endphp

<div class="group relative  flex w-full flex-col overflow-hidden rounded-xl sm:rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/80 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:bg-gray-900 dark:ring-gray-800">
    {{-- Image --}}
    <a href="{{ route('shop.product', [$product->id, $product->slug]) }}" class="relative block aspect-[4/5] w-full overflow-hidden {{ !$product->image_url ? 'bg-gray-100 dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-800/50' }}">
        @php
            $defaultLogo = setting('branding.logo_url') ?: asset('logo.png');
        @endphp
        <img
            src="{{ $product->image_url ?: $defaultLogo }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="h-full w-full {{ !$product->image_url ? 'object-contain p-4' : 'object-cover' }} transition-transform duration-500 ease-out group-hover:scale-105"
            onerror="this.src='{{ $defaultLogo }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4');"
        >

        {{-- Badges --}}
        <div class="absolute left-2 top-1 z-10 flex flex-col gap-1 sm:left-3 sm:top-1.5">
            @if ($product->featured)
                <span class="rounded-md bg-amber-500/90 backdrop-blur-sm px-1.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-white shadow-sm">
                    Featured
                </span>
            @endif
            @if ($product->type?->value === 'bundle')
                <span class="rounded-md bg-indigo-600/90 backdrop-blur-sm px-1.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-white shadow-sm">
                    Bundle
                </span>
            @endif
            @if ($product->type?->value === 'variable')
                @php
                    $hasVariationStock = $product->variations->contains(fn($v) => $v->active && $v->quantity > 0);
                @endphp
                @if (! $hasVariationStock)
                    <span class="rounded-md bg-gray-900/90 backdrop-blur-sm px-1.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-white shadow-sm dark:bg-gray-700">
                        Out of Stock
                    </span>
                @endif
            @elseif ($product->quantity <= 0)
                <span class="rounded-md bg-gray-900/90 backdrop-blur-sm px-1.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-white shadow-sm dark:bg-gray-700">
                    Out of Stock
                </span>
            @endif
        </div>

        @if ($product->sale_price)
            <div class="absolute right-2 top-1 z-10 sm:right-3 sm:top-1.5">
                <span class="rounded-md bg-red-600/90 backdrop-blur-sm px-1.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-white shadow-sm">
                    Sale
                </span>
            </div>
        @endif
    </a>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        @if ($product->category?->name)
            <p class="mb-0.5 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400 truncate">
                {{ $product->category->name }}
            </p>
        @endif

        <h3 class="mb-1.5 line-clamp-2 text-xs sm:text-sm font-semibold leading-tight sm:leading-snug text-gray-900 transition-colors group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
            <a href="{{ route('shop.product', [$product->id, $product->slug]) }}">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Price & Action area --}}
        <div class="mt-auto pt-1 flex flex-col justify-end">
            <div class="mb-2 flex items-baseline gap-1.5">
                @if ($product->type?->value === 'variable')
                    @php
                        $activeVariations = $product->variations->where('active', true)->where('price', '>', 0);
                        $minPrice = $activeVariations->min(fn($v) => $v->sale_price ?: $v->price);
                        $maxPrice = $activeVariations->max(fn($v) => $v->sale_price ?: $v->price);
                    @endphp
                    @if ($minPrice)
                        <div class="flex items-baseline gap-1">
                            @if ($maxPrice && $maxPrice > $minPrice)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">From</span>
                            @endif
                            <span class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">
                                ৳{{ number_format($minPrice, 2) }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs font-bold text-gray-400">Price varies</span>
                    @endif
                @elseif ($product->type?->value === 'bundle')
                    <span class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">
                        ৳{{ number_format($product->sale_price ?: $product->price, 2) }}
                    </span>
                @else
                    @if ($product->sale_price && $product->price && $product->sale_price < $product->price)
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-base sm:text-lg font-extrabold text-red-600 dark:text-red-400">
                                ৳{{ number_format($product->sale_price, 2) }}
                            </span>
                            <span class="text-xs font-medium text-gray-400 line-through dark:text-gray-500">
                                ৳{{ number_format($product->price, 2) }}
                            </span>
                        </div>
                    @else
                        <span class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">
                            ৳{{ number_format($product->price, 2) }}
                        </span>
                    @endif
                @endif
            </div>

            {{-- Add to Cart with Variation Selector component --}}
            <div class="relative z-10 w-full">
                <add-to-cart-button
                    :product="{{ json_encode($product) }}"
                    :variations="{{ json_encode($variationsData) }}"
                    :variation-attributes="{{ json_encode($variationAttributesData) }}"
                    :show-wishlist="{{ auth('customer')->check() ? 'true' : 'false' }}"
                ></add-to-cart-button>
            </div>
        </div>
    </div>
</div>
