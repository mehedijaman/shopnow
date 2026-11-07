@extends('site-layout')

@section('seo_title', __('cart::site.cart.checkout') . ' — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="flex shrink-0 items-center gap-1">
            <a href="{{ route('shop.cart') }}" class="hover:text-site-link hover:underline">{{ __('cart::site.cart.title') }}</a>
            <i class="ri-arrow-right-s-line text-site-muted"></i>
        </li>
        <li class="flex shrink-0 items-center">
            <span class="font-medium text-site-text dark:text-site-text">{{ __('cart::site.cart.checkout') }}</span>
        </li>
    </x-breadcrumb>

    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        <section class="py-8 antialiased dark:bg-site-surface-muted md:py-16">
            <form action="#" class="mx-auto max-w-(--breakpoint-xl) px-4 2xl:px-0">
                <ol
                    class="flex w-full max-w-2xl items-center text-center text-sm font-medium text-site-muted dark:text-site-muted sm:text-base"
                >
                    <li
                        class="after:border flex items-center text-site-link after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-site-divider dark:text-site-link dark:after:border-site-divider sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10"
                    >
                        <a href="{{ route('shop.cart') }}">
                            <span
                                class="flex items-center after:mx-2 after:text-site-text after:content-['/'] dark:after:text-site-muted sm:after:hidden"
                            >
                                <svg
                                    class="me-2 h-4 w-4 sm:h-5 sm:w-5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                                {{ __('cart::site.cart.title') }}
                            </span>
                        </a>
                    </li>

                    <li
                        class="after:border flex items-center text-site-link after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-site-divider dark:text-site-link dark:after:border-site-divider sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10"
                    >
                        <span
                            class="flex items-center after:mx-2 after:text-site-text after:content-['/'] dark:after:text-site-muted sm:after:hidden"
                        >
                            <svg
                                class="me-2 h-4 w-4 sm:h-5 sm:w-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                />
                            </svg>
                            {{ __('cart::site.cart.checkout') }}
                        </span>
                    </li>

                    <li class="flex shrink-0 items-center">
                        <svg
                            class="me-2 h-4 w-4 sm:h-5 sm:w-5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                        {{ __('cart::site.checkout.order_summary_step') }}
                    </li>
                </ol>

                <checkout-form
                    :shipping-flat-rate="{{ $shippingFlatRate }}"
                    :free-shipping-threshold="{{ $freeShippingThreshold }}"
                    :requires-shipping="{{ $totals['requiresShipping'] ? 'true' : 'false' }}"
                    :customer="{{ $customer ? json_encode($customer->only('name', 'email', 'phone')) : 'null' }}"
                    :addresses="{{ json_encode($addresses) }}"
                ></checkout-form>
            </form>
        </section>
    </div>
@endsection
