@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('seo_title', __('courier::site.track.seo_title').' — '.setting('branding.site_name', config('app.name')))

{{-- Form page is an indexable utility; results are PII-gated behind the phone match. --}}
@section('robots', !empty($notFound) || !empty($awaitingShipment) || isset($shipment) ? 'noindex, follow' : 'index, follow')

@section('content')
    @php
        use Modules\Courier\Enums\CourierProvider;

        $statusStyles = [
            'pending' => 'bg-amber-100 text-amber-800',
            'processing' => 'bg-site-primary-soft text-site-link',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-site-primary-soft text-site-link">
                <i class="ri-truck-line text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-site-text dark:text-site-text sm:text-3xl">{{ __('courier::site.track.heading') }}</h1>
            <p class="mt-2 text-sm text-site-muted dark:text-site-muted">
                {{ __('courier::site.track.intro') }}
            </p>
        </div>

        <div class="rounded-2xl border border-site-divider/80 dark:border-site-divider/80 bg-site-surface dark:bg-site-surface-muted p-6 shadow-sm sm:p-8">
            <form action="{{ route('site.track.result') }}" method="GET" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="tracking" class="mb-2 block text-xs font-bold uppercase tracking-wider text-site-text dark:text-site-text">
                        {{ __('courier::site.track.tracking_label') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-site-muted">
                            <i class="ri-barcode-line text-base"></i>
                        </div>
                        <input
                            type="text"
                            id="tracking"
                            name="tracking"
                            value="{{ old('tracking', $tracking ?? '') }}"
                            maxlength="64"
                            required
                            placeholder="{{ __('courier::site.track.tracking_placeholder') }}"
                            class="block w-full rounded-xl border border-site-border dark:border-site-border bg-site-surface-muted/50 dark:bg-site-surface-muted/50 py-3 pl-10 pr-4 text-sm text-site-text dark:text-site-text transition-all placeholder:text-site-muted focus:border-site-focus focus:bg-site-surface dark:focus:bg-site-surface-muted focus:outline-none focus:ring-2 focus:ring-site-focus/20 @error('tracking') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                    </div>
                    @error('tracking') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-xs font-bold uppercase tracking-wider text-site-text dark:text-site-text">
                        {{ __('courier::site.track.phone_label') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-site-muted">
                            <i class="ri-phone-line text-base"></i>
                        </div>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $phone ?? $prefillPhone ?? '') }}"
                            maxlength="14"
                            required
                            placeholder="{{ __('courier::site.track.phone_placeholder') }}"
                            class="block w-full rounded-xl border border-site-border dark:border-site-border bg-site-surface-muted/50 dark:bg-site-surface-muted/50 py-3 pl-10 pr-4 text-sm text-site-text dark:text-site-text transition-all placeholder:text-site-muted focus:border-site-focus focus:bg-site-surface dark:focus:bg-site-surface-muted focus:outline-none focus:ring-2 focus:ring-site-focus/20 @error('phone') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                    </div>
                    @error('phone') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <button
                        type="submit"
                        class="group inline-flex w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-site-primary to-site-primary-hover px-6 py-3.5 text-sm font-bold text-site-on-primary shadow-lg shadow-primary-600/25 transition-all duration-200 hover:from-site-primary-hover hover:to-site-primary-hover hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-4 focus:ring-site-focus/20 active:scale-[0.99] sm:w-auto"
                    >
                        <span>{{ __('courier::site.track.submit') }}</span>
                        <i class="ri-search-line text-base transition-transform group-hover:translate-x-0.5"></i>
                    </button>
                </div>
            </form>

            @if (!empty($notFound))
                <div class="mt-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-900">
                    <i class="ri-error-warning-fill mt-0.5 shrink-0 text-xl text-red-600"></i>
                    <div>
                        <p class="font-semibold">{{ __('courier::site.track.not_found_heading') }}</p>
                        <p class="mt-0.5 text-xs text-red-700">
                            {{ __('courier::site.track.not_found_body') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        @if (!empty($awaitingShipment))
            <div class="mt-6 rounded-2xl border border-site-divider/80 dark:border-site-divider/80 bg-site-surface dark:bg-site-surface-muted p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-site-divider dark:border-site-divider pb-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.order') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-site-text dark:text-site-text">#{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.order_status') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-site-text dark:text-site-text">{{ $order->status->label() }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 pt-5">
                    <i class="ri-time-line mt-0.5 shrink-0 text-xl text-amber-500"></i>
                    <div>
                        <p class="text-sm font-bold text-site-text dark:text-site-text">
                            {{ $order->requires_shipping ? __('courier::site.track.awaiting_shipped') : __('courier::site.track.awaiting_no_parcel') }}
                        </p>
                        <p class="mt-0.5 text-xs text-site-muted dark:text-site-muted">
                            {{ $order->requires_shipping
                                ? __('courier::site.track.awaiting_shipped_body', ['date' => $order->created_at->format('d M Y, h:i A')])
                                : __('courier::site.track.awaiting_no_parcel_body', ['date' => $order->created_at->format('d M Y, h:i A')]) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @isset($shipment)
            @php
                $courierLabel = CourierProvider::tryFrom((string) $shipment->carrier)?->label()
                    ?? (string) $shipment->carrier;
                $statusStyle = $statusStyles[$shipment->shopment_status->value] ?? 'bg-site-surface-muted text-site-text';
            @endphp

            <div class="mt-6 rounded-2xl border border-site-divider/80 dark:border-site-divider/80 bg-site-surface dark:bg-site-surface-muted p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-site-divider dark:border-site-divider pb-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.order') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-site-text dark:text-site-text">#{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.carrier') }}</p>
                        <p class="mt-0.5 text-lg font-bold text-site-text dark:text-site-text">{{ $courierLabel }}</p>
                    </div>
                </div>

                @if (!empty($refreshing))
                    <div class="mt-4 flex items-center gap-2 rounded-xl border border-site-focus bg-site-primary-soft/70 px-3.5 py-2.5 text-xs font-semibold text-site-link">
                        <i class="ri-refresh-line animate-spin text-sm"></i>
                        <span>{{ __('courier::site.track.refreshing') }}</span>
                    </div>
                @endif

                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 pt-5 sm:grid-cols-2">
                    <div class="flex items-center justify-between sm:block">
                        <dt class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.shipment_status') }}</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusStyle }}">
                                {{ $shipment->shopment_status->label() }}
                            </span>
                        </dd>
                    </div>

                    @if ($shipment->courier_status)
                        <div class="flex items-center justify-between sm:block">
                            <dt class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.courier_status') }}</dt>
                            <dd class="mt-1 text-sm font-semibold capitalize text-site-text dark:text-site-text">
                                {{ str_replace('_', ' ', $shipment->courier_status) }}
                            </dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between sm:block">
                        <dt class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.tracking_number') }}</dt>
                        <dd class="mt-1 text-sm font-mono font-bold text-site-text dark:text-site-text">
                            @if ($shipment->tracking_url)
                                <a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer" class="text-site-link hover:underline">
                                    {{ $shipment->tracking_number }} <i class="ri-external-link-line"></i>
                                </a>
                            @else
                                {{ $shipment->tracking_number }}
                            @endif
                        </dd>
                    </div>

                    @if ($shipment->estimated_delivery)
                        <div class="flex items-center justify-between sm:block">
                            <dt class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.estimated_delivery') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-site-text dark:text-site-text">{{ $shipment->estimated_delivery }}</dd>
                        </div>
                    @endif

                    @if ($shipment->last_synced_at)
                        <div class="flex items-center justify-between sm:block sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wider text-site-muted">{{ __('courier::site.track.last_updated') }}</dt>
                            <dd class="mt-1 text-sm text-site-muted dark:text-site-muted">{{ $shipment->last_synced_at->format('d M Y, h:i A') }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 border-t border-site-divider dark:border-site-divider pt-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-site-text dark:text-site-text">{{ __('courier::site.track.history') }}</h2>

                    @if ($events->isEmpty())
                        <p class="mt-3 text-sm text-site-muted dark:text-site-muted">{{ __('courier::site.track.history_empty') }}</p>
                    @else
                        <ol class="relative mt-4 ml-3 border-l border-site-divider dark:border-site-divider">
                            @foreach ($events as $event)
                                <li class="mb-6 ml-4 last:mb-0">
                                    <span class="absolute -left-[5px] mt-1.5 h-2.5 w-2.5 rounded-full bg-site-primary ring-4 ring-site-focus"></span>
                                    <p class="text-sm font-bold capitalize text-site-text dark:text-site-text">
                                        {{ str_replace('_', ' ', $event->status) }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-site-muted dark:text-site-muted">
                                        {{ $event->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        @endisset
    </div>
@endsection
