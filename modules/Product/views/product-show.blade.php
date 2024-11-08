@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        {{-- <product-details :product="$product"></product-details> --}}

        <section class="justify-center justify-items-center">
            <div>
                <h2 class="text-center text-3xl font-bold sm:text-3xl">
                    Product Description
                </h2>
                <hr class="my-6 border-gray-200 dark:border-gray-800" />
            </div>
            <div class="text-justify">
                {!! $product->description !!}
            </div>
        </section>
    </div>
@endsection
