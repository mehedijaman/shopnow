@extends('site-layout')

@section('seo_title', __('order::site.orders.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-site-text dark:text-site-text">{{ __('site.account.manage_heading') }}</h3>
                <p class="mt-1 text-sm text-site-muted dark:text-site-text">{{ __('site.account.manage_hint_orders') }}</p>
                
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('account.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted">
                        <i class="ri-user-line mr-2 text-lg"></i> {{ __('site.account.my_profile') }}
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text">
                        <i class="ri-shopping-bag-line mr-2 text-lg"></i> {{ __('site.account.my_orders') }}
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted">
                        <i class="ri-download-line mr-2 text-lg"></i> {{ __('site.account.my_downloads') }}
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="ri-logout-box-r-line mr-2 text-lg"></i> {{ __('site.account.logout') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="mt-5 md:col-span-2 md:mt-0">
            <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted p-6">
                <h2 class="text-xl font-semibold text-site-text dark:text-site-text sm:text-2xl mb-6 border-b border-site-divider dark:border-site-divider pb-2">
                    {{ __('order::site.orders.heading') }}
                </h2>

                <div class="flow-root">
                    <div class="divide-y divide-site-divider dark:divide-site-divider">
                        @forelse ($orders as $order)
                            <div class="flex flex-wrap items-center gap-y-4 py-6">
                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-xs font-medium text-site-muted dark:text-site-muted uppercase">
                                        {{ __('order::site.orders.order_id') }}
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-site-text dark:text-site-text">
                                        <a href="{{ route('site.order.confirm', $order->id) }}" class="hover:underline text-site-link">
                                            #{{ $order->id }}
                                        </a>
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-xs font-medium text-site-muted dark:text-site-muted uppercase">
                                        {{ __('order::site.orders.date') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-site-text dark:text-site-text">
                                        {{ $order->created_at->format('d M Y') }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-xs font-medium text-site-muted dark:text-site-muted uppercase">
                                        {{ __('order::site.orders.total_price') }}
                                    </dt>
                                    <dd class="mt-1 text-sm font-semibold text-site-text dark:text-site-text">
                                        {{ number_format($order->total, 2) }} Tk
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-xs font-medium text-site-muted dark:text-site-muted uppercase">
                                        {{ __('order::site.orders.status') }}
                                    </dt>
                                    <dd class="mt-1">
                                        @if($order->status === \Modules\Order\Enums\OrderStatus::Completed)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                                                {{ $order->status->label() }}
                                            </span>
                                        @elseif($order->status === \Modules\Order\Enums\OrderStatus::Cancelled)
                                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">
                                                {{ $order->status->label() }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800">
                                                {{ $order->status->label() }}
                                            </span>
                                        @endif
                                    </dd>
                                </dl>

                                <div class="w-full sm:w-auto lg:w-auto flex items-center justify-end">
                                    <a href="{{ route('site.order.confirm', $order->id) }}"
                                       class="inline-flex items-center gap-1 rounded border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-3 py-1.5 text-xs font-semibold text-site-text dark:text-site-text hover:bg-site-surface-muted dark:hover:bg-site-surface-muted">
                                        {{ __('order::site.orders.view_details') }}
                                    </a>
                                </div>

                                @php
                                    $shipment = $order->orderShipments->first();
                                @endphp
                                @if ($shipment)
                                    @php
                                        $courierLabel = $shipment->carrier
                                            ? (\Modules\Courier\Enums\CourierProvider::tryFrom((string) $shipment->carrier)?->label()
                                                ?? (string) $shipment->carrier)
                                            : __('order::site.orders.not_shipped');
                                        $statusStyles = [
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'processing' => 'bg-site-primary-soft text-site-link',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'delivered' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusStyle = $statusStyles[$shipment->shopment_status->value] ?? 'bg-site-surface-muted text-site-text';
                                        $trackPageUrl = route('site.track', ['tracking' => $shipment->tracking_number ?: $order->id]);
                                        $trackUrl = $shipment->tracking_url ?: $trackPageUrl;
                                    @endphp
                                    <div class="mt-3 flex w-full flex-wrap items-center gap-x-3 gap-y-1.5 rounded-xl bg-site-surface-muted px-3 py-2 text-xs dark:bg-site-surface-muted">
                                        <span class="inline-flex items-center gap-1.5 font-bold text-site-text dark:text-site-text">
                                            <i class="ri-truck-line text-sm"></i>
                                            {{ $courierLabel }}
                                        </span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-bold {{ $statusStyle }}">
                                            {{ $shipment->shopment_status->label() }}
                                        </span>
                                        @if ($shipment->tracking_number)
                                            <a href="{{ $trackUrl }}" target="_blank" rel="noopener noreferrer"
                                               class="font-mono font-semibold text-site-link hover:underline">
                                                {{ $shipment->tracking_number }}
                                                <i class="ri-external-link-line"></i>
                                            </a>
                                        @endif
                                        <a href="{{ $trackPageUrl }}"
                                           class="text-site-muted dark:text-site-muted hover:underline">
                                            {{ $shipment->tracking_number ? __('order::site.orders.full_history') : __('order::site.orders.track_order') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="py-6 text-center text-site-muted dark:text-site-muted">{{ __('order::site.orders.empty') }}</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
