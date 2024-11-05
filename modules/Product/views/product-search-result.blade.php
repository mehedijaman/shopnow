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
            {{--
                <p class="mb-8 text-center text-base text-white">
                Elevate your style with our latest arrivals. Shop now and enjoy
                exclusive discounts!
                </p>
            --}}
        </div>
    </div>

    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            @if ($products->count())
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
            @endif
        </div>
    </section>
@endsection
