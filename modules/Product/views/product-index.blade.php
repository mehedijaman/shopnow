@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    @if ($products->count())
        <x-product-list :products="$products"></x-product-list>
    @endif

    {{ $products->links() }}
@endsection
