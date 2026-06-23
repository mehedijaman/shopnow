@extends('site-layout')

@section('seo_title', 'Shopping Cart — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="flex shrink-0 items-center">
            <span class="font-medium text-gray-700">Cart</span>
        </li>
    </x-breadcrumb>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <shopping-cart
            :shipping-flat-rate="{{ $shippingFlatRate }}"
            :free-shipping-threshold="{{ $freeShippingThreshold }}"
        ></shopping-cart>
    </div>
@endsection
