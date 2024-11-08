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
                Search : {{ $searchText }}
            </h2>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        @if ($products->count())
            <x-product-list :products="$products"></x-product-list>
        @endif

        {{ $products->links() }}
    </div>
@endsection
