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
                Contact Us
            </h2>
        </div>
    </div>
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        <div class="mx-auto max-w-xl bg-white p-4 font-[sans-serif]">
            <form class="mt-8 space-y-4">
                <input
                    type="text"
                    placeholder="Name"
                    class="w-full rounded-md bg-gray-100 px-4 py-3 text-sm text-gray-800 outline-blue-500 focus:bg-transparent"
                />
                <input
                    type="email"
                    placeholder="Email"
                    class="w-full rounded-md bg-gray-100 px-4 py-3 text-sm text-gray-800 outline-blue-500 focus:bg-transparent"
                />
                <input
                    type="text"
                    placeholder="Subject"
                    class="w-full rounded-md bg-gray-100 px-4 py-3 text-sm text-gray-800 outline-blue-500 focus:bg-transparent"
                />
                <textarea
                    placeholder="Message"
                    rows="6"
                    class="w-full rounded-md bg-gray-100 px-4 pt-3 text-sm text-gray-800 outline-blue-500 focus:bg-transparent"
                ></textarea>
                <button
                    type="button"
                    class="w-full rounded-md bg-blue-500 px-4 py-3 text-sm tracking-wide text-white hover:bg-blue-600"
                >
                    Send
                </button>
            </form>
        </div>
    </div>
@endsection
