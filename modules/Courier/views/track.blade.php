@extends('site-layout')

@section('seo_title', 'Track Your Parcel — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
    @php
        use Modules\Courier\Enums\CourierProvider;

        $statusStyles = [
            'pending' => 'bg-amber-100 text-amber-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                <i class="ri-truck-line text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Track Your Parcel</h1>
            <p class="mt-2 text-sm text-slate-500">
                Enter your tracking number and the phone number you used for the order.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <form action="{{ route('site.track.result') }}" method="GET" class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="tracking" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Tracking Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="ri-barcode-line text-base"></i>
                        </div>
                        <input
                            type="text"
                            id="tracking"
                            name="tracking"
                            value="{{ old('tracking', $tracking ?? '') }}"
                            maxlength="64"
                            required
                            placeholder="e.g. STF123456789"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('tracking') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                    </div>
                    @error('tracking') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="ri-phone-line text-base"></i>
                        </div>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $phone ?? '') }}"
                            maxlength="14"
                            required
                            placeholder="e.g. 01712345678"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('phone') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                    </div>
                    @error('phone') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <button
                        type="submit"
                        class="group inline-flex w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-4 focus:ring-primary-600/20 active:scale-[0.99] sm:w-auto"
                    >
                        <span>Track Parcel</span>
                        <i class="ri-search-line text-base transition-transform group-hover:translate-x-0.5"></i>
                    </button>
                </div>
            </form>

            @if (!empty($notFound))
                <div class="mt-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50/80 p-4 text-sm text-red-900">
                    <i class="ri-error-warning-fill mt-0.5 shrink-0 text-xl text-red-600"></i>
                    <div>
                        <p class="font-semibold">Shipment not found</p>
                        <p class="mt-0.5 text-xs text-red-700">
                            No shipment matches that tracking number and phone combination. Please double-check both and try again.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        @isset($shipment)
            @php
                $courierLabel = CourierProvider::tryFrom((string) $shipment->carrier)?->label()
                    ?? (string) $shipment->carrier;
                $statusStyle = $statusStyles[$shipment->shopment_status->value] ?? 'bg-slate-100 text-slate-800';
            @endphp

            <div class="mt-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Order</p>
                        <p class="mt-0.5 text-lg font-bold text-slate-900">#{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Carrier</p>
                        <p class="mt-0.5 text-lg font-bold text-slate-900">{{ $courierLabel }}</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 pt-5 sm:grid-cols-2">
                    <div class="flex items-center justify-between sm:block">
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Shipment Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusStyle }}">
                                {{ $shipment->shopment_status->label() }}
                            </span>
                        </dd>
                    </div>

                    @if ($shipment->courier_status)
                        <div class="flex items-center justify-between sm:block">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Courier Status</dt>
                            <dd class="mt-1 text-sm font-semibold capitalize text-slate-800">
                                {{ str_replace('_', ' ', $shipment->courier_status) }}
                            </dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between sm:block">
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Tracking Number</dt>
                        <dd class="mt-1 text-sm font-mono font-bold text-slate-800">
                            @if ($shipment->tracking_url)
                                <a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:underline">
                                    {{ $shipment->tracking_number }} <i class="ri-external-link-line"></i>
                                </a>
                            @else
                                {{ $shipment->tracking_number }}
                            @endif
                        </dd>
                    </div>

                    @if ($shipment->estimated_delivery)
                        <div class="flex items-center justify-between sm:block">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Estimated Delivery</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $shipment->estimated_delivery }}</dd>
                        </div>
                    @endif

                    @if ($shipment->last_synced_at)
                        <div class="flex items-center justify-between sm:block sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-slate-500">{{ $shipment->last_synced_at->format('d M Y, h:i A') }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Shipment History</h2>

                    @if ($events->isEmpty())
                        <p class="mt-3 text-sm text-slate-500">No status updates recorded yet. Check back soon.</p>
                    @else
                        <ol class="relative mt-4 ml-3 border-l border-slate-200">
                            @foreach ($events as $event)
                                <li class="mb-6 ml-4 last:mb-0">
                                    <span class="absolute -left-[5px] mt-1.5 h-2.5 w-2.5 rounded-full bg-primary-600 ring-4 ring-primary-50"></span>
                                    <p class="text-sm font-bold capitalize text-slate-900">
                                        {{ str_replace('_', ' ', $event->status) }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
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
