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
                About Us
            </h2>
        </div>
    </div>
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Laboriosam
        quibusdam sit dolorum magnam quo, fugit veniam quaerat libero molestias
        non ex est at quae reprehenderit corrupti sapiente. Natus, deleniti
        aspernatur?
    </div>
@endsection
