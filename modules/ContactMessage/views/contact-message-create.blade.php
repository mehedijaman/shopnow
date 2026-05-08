@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="min-w-0">
            <span class="font-semibold text-gray-800">Contact Us</span>
        </li>
    </x-breadcrumb>

    <div class="bg-gray-50 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Page heading --}}
            <div class="mb-10 text-center">
                <div class="mb-3 flex items-center justify-center gap-3">
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Contact Us</h1>
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                </div>
                <p class="mx-auto max-w-xl text-sm text-gray-500">Have a question or need help? Fill out the form below and we'll get back to you as soon as possible.</p>
            </div>

            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 lg:grid-cols-3">

                {{-- Contact info sidebar --}}
                <div class="space-y-6 lg:col-span-1">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-gray-400">Get in Touch</h2>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                                    <i class="ri-map-pin-2-line text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Address</p>
                                    <p class="text-sm text-gray-700">{{ implode(', ', (array) (setting('contact.address') ?: ['Dhaka, Bangladesh'])) }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                                    <i class="ri-phone-line text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Phone</p>
                                    <p class="text-sm text-gray-700">{{ implode(', ', (array) (setting('contact.phone') ?: ['+880 1700 000000'])) }}</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                                    <i class="ri-mail-line text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Email</p>
                                    <p class="text-sm text-gray-700">{{ implode(', ', (array) (setting('contact.email') ?: ['hello@shopnow.com'])) }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Contact form --}}
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">

                        @if (session('success'))
                            <div class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                                <i class="ri-checkbox-circle-line text-xl text-green-500"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('site.contact.store') }}" method="POST" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Your Name <span class="text-red-500">*</span></label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. Mehedi Hasan"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 @error('name') border-red-400 bg-red-50 @enderror"
                                    />
                                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="e.g. 01712345678"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email Address</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="e.g. you@example.com"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 @error('email') border-red-400 bg-red-50 @enderror"
                                />
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="subject" class="mb-1.5 block text-sm font-medium text-gray-700">Subject</label>
                                <input
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    value="{{ old('subject') }}"
                                    placeholder="How can we help?"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                />
                            </div>

                            <div>
                                <label for="message" class="mb-1.5 block text-sm font-medium text-gray-700">Message <span class="text-red-500">*</span></label>
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="5"
                                    placeholder="Write your message here..."
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 @error('message') border-red-400 bg-red-50 @enderror"
                                >{{ old('message') }}</textarea>
                                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300"
                            >
                                <i class="ri-send-plane-line text-base"></i>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
