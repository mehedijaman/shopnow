@extends('site-layout')

@section('seo_title', 'Order Confirmed — #' . $order->id)

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
            <div class="mx-auto max-w-2xl px-4 2xl:px-0">

                {{-- Success Icon --}}
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                            Thank you, {{ $order->name }}!
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Your order has been placed successfully.</p>
                    </div>
                </div>

                <p class="mb-6 text-gray-500 dark:text-gray-400 md:mb-8">
                    Your order
                    <span class="font-semibold text-gray-900 dark:text-white">#{{ $order->id }}</span>
                    will be processed within 24 hours during working days.
                    @if ($order->email)
                        We will notify you at <strong>{{ $order->email }}</strong> once your order has been shipped.
                    @endif
                </p>

                {{-- Order Details --}}
                <div class="mb-6 space-y-3 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 md:mb-8">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Order Details</h3>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Order Number</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white sm:text-end">#{{ $order->id }}</dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Date</dt>
                        <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $order->created_at->format('d M Y, h:i A') }}</dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Payment Method</dt>
                        <dd class="font-medium text-gray-900 dark:text-white sm:text-end">
                            {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ($order->payment_method ? ucfirst($order->payment_method) : '—') }}
                        </dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Payment Status</dt>
                        <dd class="sm:text-end">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </dd>
                    </dl>

                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Phone</dt>
                        <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $order->phone }}</dd>
                    </dl>

                    @if ($order->district || $order->upazila)
                        <dl class="items-center justify-between gap-4 sm:flex">
                            <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Area</dt>
                            <dd class="font-medium text-gray-900 dark:text-white sm:text-end">
                                {{ collect([$order->upazila, $order->district])->filter()->implode(', ') }}
                            </dd>
                        </dl>
                    @endif

                    @if ($order->address)
                        <dl class="items-center justify-between gap-4 sm:flex">
                            <dt class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0">Delivery Address</dt>
                            <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $order->address }}</dd>
                        </dl>
                    @endif
                </div>

                {{-- Items Table --}}
                <div class="mb-6 md:mb-8">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Items Ordered</h3>
                    <div class="overflow-hidden rounded-lg border border-gray-100 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Product</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500">Qty</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500">Price</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 dark:bg-gray-900">
                                @foreach ($order->orderProducts as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                                            {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 2) }} Tk</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($item->total_price, 2) }} Tk</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-500">Subtotal</td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-900 dark:text-white">{{ number_format($order->subtotal, 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-500">Shipping</td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-900 dark:text-white">
                                        @if ($order->shipping == 0)
                                            <span class="text-green-600">Free</span>
                                        @else
                                            {{ number_format($order->shipping, 2) }} Tk
                                        @endif
                                    </td>
                                </tr>
                                @if ($order->tax > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-500">Tax</td>
                                        <td class="px-4 py-2 text-right text-sm text-gray-900 dark:text-white">{{ number_format($order->tax, 2) }} Tk</td>
                                    </tr>
                                @endif
                                <tr class="border-t border-gray-200 dark:border-gray-600">
                                    <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Total</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">{{ number_format($order->total, 2) }} Tk</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('site.index') }}"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-hidden focus:ring-4 focus:ring-blue-300"
                    >
                        Continue Shopping
                    </a>
                    <a
                        href="{{ route('shop.index') }}"
                        class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:z-10 focus:outline-hidden focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                    >
                        Back to Shop
                    </a>
                </div>

            </div>
        </section>
    </div>
@endsection
