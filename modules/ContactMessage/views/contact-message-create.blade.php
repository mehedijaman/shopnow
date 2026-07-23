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

    <div class="relative overflow-hidden bg-slate-50/60 py-10 sm:py-14 lg:py-16">
        <!-- Subtle decorative background blur shapes -->
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-primary-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 top-1/3 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="mx-auto mb-10 max-w-3xl text-center sm:mb-14">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-3.5 py-1 text-xs font-semibold text-primary-700 ring-1 ring-inset ring-primary-600/20">
                    <i class="ri-customer-service-2-line text-sm"></i> 24/7 Customer Support
                </span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                    Get in Touch With Us
                </h1>
                <p class="mt-3 text-base text-slate-600 sm:text-lg">
                    Have a question, feedback, or need help with your order? Send us a message and our support team will respond promptly.
                </p>
            </div>

            <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10">

                {{-- Contact Info Cards Sidebar --}}
                <div class="space-y-5 lg:col-span-4">

                    @php
                        $addresses = collect((array) setting('contact.address'))->filter()->values();
                        $phones = collect((array) setting('contact.phone'))->filter()->values();
                        $emails = collect((array) setting('contact.email'))->filter()->values();
                    @endphp

                    @if ($addresses->isNotEmpty() || $phones->isNotEmpty() || $emails->isNotEmpty())
                    <!-- Direct Contact Card -->
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:shadow-md sm:p-7">
                        <h2 class="mb-5 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <span class="h-2 w-2 rounded-full bg-primary-600"></span>
                            Reach Us Directly
                        </h2>

                        <div class="space-y-6">
                            <!-- Address -->
                            @if ($addresses->isNotEmpty())
                            <div class="group flex items-start gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 ring-1 ring-primary-600/10 transition group-hover:scale-105 group-hover:bg-primary-600 group-hover:text-white">
                                    <i class="ri-map-pin-2-fill text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-slate-400">Our Location</p>
                                    @foreach ($addresses as $address)
                                        <p class="mt-0.5 text-sm font-semibold text-slate-800">{{ $address }}</p>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Phone -->
                            @if ($phones->isNotEmpty())
                            <div class="group flex items-start gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 ring-1 ring-primary-600/10 transition group-hover:scale-105 group-hover:bg-primary-600 group-hover:text-white">
                                    <i class="ri-phone-fill text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-slate-400">Call Us</p>
                                    <p class="mt-0.5 text-sm font-semibold text-slate-800">
                                        @foreach ($phones as $phone)
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="transition hover:text-primary-600">
                                                {{ $phone }}
                                            </a>@if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                            @endif

                            <!-- Email -->
                            @if ($emails->isNotEmpty())
                            <div class="group flex items-start gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 ring-1 ring-primary-600/10 transition group-hover:scale-105 group-hover:bg-primary-600 group-hover:text-white">
                                    <i class="ri-mail-fill text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-slate-400">Email Support</p>
                                    @foreach ($emails as $email)
                                        <a href="mailto:{{ $email }}" class="mt-0.5 block text-sm font-semibold text-slate-800 transition hover:text-primary-600 break-all">
                                            {{ $email }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Business Hours Card -->
                    @php
                        $workingHours = collect((array) setting('contact.working_hours'))->filter()->values();
                    @endphp
                    @if ($workingHours->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-7">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <i class="ri-time-fill text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Working Hours</h3>
                                <p class="text-xs text-slate-500">Support Availability</p>
                            </div>
                        </div>
                        <ul class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-xs">
                            @foreach ($workingHours as $wh)
                                <li class="flex items-center justify-between text-slate-700 font-medium">
                                    <span class="flex items-center gap-1.5">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $wh }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Quick Guarantee Card -->
                    <div class="rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 p-6 text-white shadow-lg shadow-primary-600/20">
                        <div class="flex items-center gap-3">
                            <i class="ri-shield-check-fill text-2xl text-primary-200"></i>
                            <h3 class="text-sm font-bold">Fast & Friendly Support</h3>
                        </div>
                        <p class="mt-2 text-xs leading-relaxed text-primary-100">
                            We aim to respond to all inquiries within 2 to 4 business hours. Thank you for choosing {{ setting('branding.site_name', config('app.name')) }}!
                        </p>
                    </div>

                </div>

                {{-- Contact Form Section --}}
                <div class="lg:col-span-8">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 lg:p-10">

                        <div class="mb-6 border-b border-slate-100 pb-5">
                            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Send Us a Message</h2>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                                Please fill out all required fields below marked with an asterisk (<span class="text-red-500 font-bold">*</span>).
                            </p>
                        </div>

                        {{-- Success Alert --}}
                        @if (session('success'))
                            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-900 shadow-xs">
                                <i class="ri-checkbox-circle-fill text-xl text-emerald-600 shrink-0"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-emerald-900">Message Sent Successfully!</p>
                                    <p class="mt-0.5 text-xs text-emerald-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('site.contact.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Your Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <i class="ri-user-3-line text-base"></i>
                                        </div>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            value="{{ old('name') }}"
                                            placeholder="e.g. Mehedi Hasan"
                                            required
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('name') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                        />
                                    </div>
                                    @error('name') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <i class="ri-phone-line text-base"></i>
                                        </div>
                                        <input
                                            type="text"
                                            id="phone"
                                            name="phone"
                                            value="{{ old('phone') }}"
                                            placeholder="e.g. 01712345678"
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <i class="ri-mail-line text-base"></i>
                                        </div>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="e.g. you@example.com"
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('email') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                        />
                                    </div>
                                    @error('email') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label for="subject" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Subject
                                    </label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                            <i class="ri-chat-3-line text-base"></i>
                                        </div>
                                        <input
                                            type="text"
                                            id="subject"
                                            name="subject"
                                            value="{{ old('subject') }}"
                                            placeholder="e.g. Order Inquiry / Support"
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows="5"
                                        required
                                        placeholder="Write your message here... Please include order details if applicable."
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('message') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                    >{{ old('message') }}</textarea>
                                </div>
                                @error('message') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    class="group inline-flex w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-600/30 focus:outline-none focus:ring-4 focus:ring-primary-600/20 active:scale-[0.99] sm:w-auto"
                                >
                                    <span>Send Message</span>
                                    <i class="ri-send-plane-fill text-base transition-transform group-hover:translate-x-1"></i>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- Google Map Embed Section --}}
            @php
                $googleMapEmbed = setting('contact.google_map');
            @endphp
            @if (!empty($googleMapEmbed))
                <div class="mx-auto mt-10 max-w-6xl sm:mt-12">
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-2 shadow-sm sm:p-3">
                        <div class="relative h-80 w-full overflow-hidden rounded-xl bg-slate-100 sm:h-96 [&_iframe]:h-full [&_iframe]:w-full [&_iframe]:border-0 [&_iframe]:rounded-xl">
                            @if (str_contains($googleMapEmbed, '<iframe'))
                                {!! $googleMapEmbed !!}
                            @else
                                <iframe src="{{ $googleMapEmbed }}" class="h-full w-full border-0 rounded-xl" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

