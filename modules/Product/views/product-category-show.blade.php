@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div
        class="bg-gradient-to-r from-blue-700 to-[#B06AB3] px-6 py-12 font-sans"
    >
        <div
            class="container mx-auto flex flex-col items-center justify-center text-center"
        >
            <h2 class="mb-4 text-3xl font-bold text-white sm:text-4xl">
                Category: {{ $category->name }}
            </h2>

            <p class="mb-8 text-center text-base text-white">
                {!! $category->description !!}
            </p>
        </div>
    </div>

    @if ($products->count())
        <x-product-list :products="$products"></x-product-list>
    @endif

    {{ $products->links() }}
@endsection
