@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <nav-bar></nav-bar>
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div
                class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4"
            >
                @foreach ($products as $product)
                    <product-card :product="{{ $product }}"></product-card>
                @endforeach
            </div>

            <div class="w-full text-center">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
