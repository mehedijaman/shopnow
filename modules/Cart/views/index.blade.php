@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        <shopping-cart></shopping-cart>
    </div>
@endsection
