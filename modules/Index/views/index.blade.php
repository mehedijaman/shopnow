@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        @if ($products->count())
            <section
                class="grid grid-cols-1 justify-center justify-items-center gap-x-8 gap-y-16 py-4 md:grid-cols-2 lg:grid-cols-4"
            >
                @foreach ($products as $product)
                    <x-product-card :product="$product"></x-product-card>
                @endforeach
            </section>
        @endif

        {{ $products->links() }}
    </div>
@endsection
