@extends('site-layout')

@section('seo_title', __('order::site.confirm.seo_title') . ' — #' . $order->id)

@section('robots', 'noindex, follow')

@php
    $purchaseItems = $order->orderProducts->map(function ($item) {
        $data = [
            'item_id' => (string) $item->product_id,
            'item_name' => $item->product?->name ?? __('order::site.confirm.product_fallback', ['id' => $item->product_id]),
            'price' => (float) $item->unit_price,
            'quantity' => (int) $item->quantity,
        ];
        if (! empty($item->variation_label)) {
            $data['item_variant'] = $item->variation_label;
        }

        return $data;
    })->values()->all();
@endphp

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
    <script>
        window.dataLayer = window.dataLayer || []
        var purchaseTransactionId = @json((string) $order->id);
        var purchaseStorageKey = 'purchase_' + purchaseTransactionId
        var purchaseAlreadyFired = false
        try { purchaseAlreadyFired = sessionStorage.getItem(purchaseStorageKey) === '1' } catch (e) {}
        if (!purchaseAlreadyFired) {
            if (window.ShopNowTracking && window.ShopNowTracking.gtmEnabled) {
                var purchasePayload = {
                    event: 'purchase',
                    ecommerce: {
                        transaction_id: purchaseTransactionId,
                        value: Number(@json((float) $order->total)),
                        tax: Number(@json((float) $order->tax)),
                        shipping: Number(@json((float) $order->shipping)),
                        currency: 'BDT',
                        items: @json($purchaseItems)
                    }
                }
                console.log('[GTM] purchase', purchasePayload)
                window.dataLayer.push(purchasePayload)
            }

            if (window.ShopNowTracking && window.ShopNowTracking.pixelEnabled && !window.ShopNowTracking.gtmEnabled) {
                window.ShopNowTracking.track('Purchase', {
                    value: Number(@json((float) $order->total)),
                    currency: 'BDT',
                    content_ids: @json(collect($purchaseItems)->pluck('item_id')),
                    content_type: 'product',
                }, {
                    eventID: 'purchase_' + purchaseTransactionId,
                })
            }

            try { sessionStorage.setItem(purchaseStorageKey, '1') } catch (e) {}
        }
    </script>
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        <section class="bg-site-surface dark:bg-site-surface-muted py-8 antialiased md:py-16">
            <div class="mx-auto max-w-2xl px-4 2xl:px-0">

                {{-- Success Icon --}}
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-site-text dark:text-site-text sm:text-2xl">
                            {{ __('order::site.confirm.thank_you', ['name' => $order->name]) }}
                        </h2>
                        <p class="text-sm text-site-muted dark:text-site-muted">{{ __('order::site.confirm.success') }}</p>
                    </div>
                </div>

                <p class="mb-6 text-site-muted dark:text-site-muted md:mb-8">
                    {!! __('order::site.confirm.intro', ['id' => $order->id]) !!}
                    @if ($order->email)
                        {!! __('order::site.confirm.intro_email', ['email' => '<strong>' . $order->email . '</strong>']) !!}
                    @endif
                </p>

                {{-- Order Details --}}
                <div class="mb-6 space-y-3 rounded-lg border border-site-divider dark:border-site-divider bg-site-surface-muted dark:bg-site-surface-muted p-6 md:mb-8">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('order::site.confirm.order_details') }}</h3>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.order_number') }}</dt>
                        <dd class="font-semibold text-site-text dark:text-site-text sm:text-end">#{{ $order->id }}</dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.date') }}</dt>
                        <dd class="font-medium text-site-text dark:text-site-text sm:text-end">{{ $order->created_at->format('d M Y, h:i A') }}</dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.payment_method') }}</dt>
                        <dd class="font-medium text-site-text dark:text-site-text sm:text-end">
                            {{ $order->payment_method === 'cod' ? __('order::site.confirm.cash_on_delivery') : ($order->payment_method ? ucfirst($order->payment_method) : '—') }}
                        </dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.payment_status') }}</dt>
                        <dd class="sm:text-end">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $order->payment_status === \Modules\Order\Enums\PaymentStatus::Paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.phone') }}</dt>
                        <dd class="font-medium text-site-text dark:text-site-text sm:text-end">{{ $order->phone }}</dd>
                    </dl>

                    @if ($order->district || $order->upazila)
                        <dl class="items-center justify-between gap-4 sm:flex">
                            <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.area') }}</dt>
                            <dd class="font-medium text-site-text dark:text-site-text sm:text-end">
                                {{ collect([$order->upazila, $order->district])->filter()->implode(', ') }}
                            </dd>
                        </dl>
                    @endif

                    @if ($order->address)
                        <dl class="items-center justify-between gap-4 sm:flex">
                            <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.delivery_address') }}</dt>
                            <dd class="font-medium text-site-text dark:text-site-text sm:text-end">{{ $order->address }}</dd>
                        </dl>
                    @endif

                    @if ($order->requires_shipping)
                        <dl class="items-center justify-between gap-4 sm:flex">
                            <dt class="mb-1 font-normal text-site-muted dark:text-site-muted sm:mb-0">{{ __('order::site.confirm.track_parcel') }}</dt>
                            <dd class="sm:text-end">
                                <a href="{{ route('site.track', ['tracking' => $order->id]) }}" class="font-semibold text-site-link hover:underline">
                                    {{ __('order::site.confirm.track_this_order') }} <i class="ri-arrow-right-line"></i>
                                </a>
                                <p class="mt-0.5 text-xs font-normal text-site-muted">{{ __('order::site.confirm.track_hint') }}</p>
                            </dd>
                        </dl>
                    @endif
                </div>

                {{-- Items Table --}}
                <div class="mb-6 md:mb-8">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('order::site.confirm.items_ordered') }}</h3>
                    <div class="overflow-hidden rounded-lg border border-site-divider dark:border-site-divider">
                        <table class="w-full text-sm">
                            <thead class="bg-site-surface-muted dark:bg-site-surface-muted">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-site-muted dark:text-site-muted">{{ __('order::site.confirm.col_product') }}</th>
                                    <th class="px-4 py-3 text-center font-medium text-site-muted dark:text-site-muted">{{ __('order::site.confirm.col_qty') }}</th>
                                    <th class="px-4 py-3 text-right font-medium text-site-muted dark:text-site-muted">{{ __('order::site.confirm.col_price') }}</th>
                                    <th class="px-4 py-3 text-right font-medium text-site-muted dark:text-site-muted">{{ __('order::site.confirm.col_total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-site-divider dark:divide-site-divider">
                                @foreach ($order->orderProducts as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-site-text dark:text-site-text">
                                            <div>
                                                <span class="font-medium">{{ $item->product?->name ?? __('order::site.confirm.product_fallback', ['id' => $item->product_id]) }}</span>
                                                @if ($item->variation_label)
                                                    <p class="mt-0.5 text-xs text-site-link">{{ $item->variation_label }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-site-muted dark:text-site-text">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-site-muted dark:text-site-text">{{ number_format($item->unit_price, 2) }} Tk</td>
                                        <td class="px-4 py-3 text-right font-medium text-site-text dark:text-site-text">{{ number_format($item->total_price, 2) }} Tk</td>
                                    </tr>

                                    {{-- Bundle child items snapshot --}}
                                    @if ($item->bundleItems && $item->bundleItems->count())
                                        @foreach ($item->bundleItems as $bi)
                                            <tr class="bg-site-surface-muted/50 dark:bg-site-surface-muted/50">
                                                <td class="px-4 py-2 pl-8">
                                                    <span class="text-xs text-site-muted dark:text-site-muted">└ {{ $bi->name ?? __('order::site.confirm.bundle_item') }}</span>
                                                    @if ($bi->sku)
                                                        <span class="text-xs text-site-muted">({{ $bi->sku }})</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-center text-xs text-site-muted dark:text-site-muted">{{ $bi->quantity }}</td>
                                                <td class="px-4 py-2 text-right text-xs text-site-muted dark:text-site-muted">{{ number_format($bi->unit_price, 2) }} Tk</td>
                                                <td class="px-4 py-2 text-right text-xs font-medium text-site-muted dark:text-site-text">{{ number_format($bi->total_price, 2) }} Tk</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-site-surface-muted dark:bg-site-surface-muted">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-site-muted dark:text-site-muted">{{ __('cart::site.cart.subtotal') }}</td>
                                    <td class="px-4 py-2 text-right text-sm text-site-text dark:text-site-text">{{ number_format($order->subtotal, 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-site-muted dark:text-site-muted">{{ __('cart::site.cart.shipping') }}</td>
                                    <td class="px-4 py-2 text-right text-sm text-site-text dark:text-site-text">
                                        @if ($order->shipping == 0)
                                            <span class="text-green-600">{{ __('cart::site.cart.free') }}</span>
                                        @else
                                            {{ number_format($order->shipping, 2) }} Tk
                                        @endif
                                    </td>
                                </tr>
                                @if ($order->tax > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm text-site-muted dark:text-site-muted">{{ __('cart::site.cart.tax') }}</td>
                                        <td class="px-4 py-2 text-right text-sm text-site-text dark:text-site-text">{{ number_format($order->tax, 2) }} Tk</td>
                                    </tr>
                                @endif
                                @if ($order->discount > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm text-site-muted dark:text-site-muted">{{ __('cart::site.cart.discount') }}
                                            @if ($order->coupon_code)
                                                <span class="text-xs text-emerald-600">({{ $order->coupon_code }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm font-semibold text-emerald-600">-{{ number_format($order->discount, 2) }} Tk</td>
                                    </tr>
                                @endif
                                <tr class="border-t border-site-divider dark:border-site-divider">
                                    <td colspan="3" class="px-4 py-3 text-right font-semibold text-site-text dark:text-site-text">{{ __('cart::site.cart.total') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-site-text dark:text-site-text">{{ number_format($order->total, 2) }} Tk</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('site.index') }}"
                        class="rounded-lg bg-site-primary px-5 py-2.5 text-sm font-medium text-site-on-primary hover:bg-site-primary-hover focus:outline-hidden focus:ring-4 focus:ring-site-focus"
                    >
                        {{ __('order::site.confirm.continue_shopping') }}
                    </a>
                    <a
                        href="{{ route('shop.index') }}"
                        class="rounded-lg border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-5 py-2.5 text-sm font-medium text-site-text dark:text-site-text hover:bg-site-surface-muted dark:hover:bg-site-surface-muted focus:z-10 focus:outline-hidden focus:ring-4 focus:ring-site-border dark:focus:ring-site-border"
                    >
                        {{ __('order::site.confirm.back_to_shop') }}
                    </a>
                </div>

            </div>
        </section>
    </div>
@endsection
