@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        @if ($products->count())
            <x-product-list :products="$products"></x-product-list>
        @endif

        {{ $products->links() }}
    </div>
@endsection
