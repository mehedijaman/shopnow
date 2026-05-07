@php
    $footerLogo = setting('branding.logo_url');
    $footerSiteName = setting('branding.site_name', 'ShopNow');
    $footerDescription = setting('general.site_description');
    $contactSettings = settings_group('contact');
    $socialSettings = settings_group('social');

    $phones = is_array($contactSettings['phone'] ?? null) ? $contactSettings['phone'] : [];
    $emails = is_array($contactSettings['email'] ?? null) ? $contactSettings['email'] : [];
    $addresses = is_array($contactSettings['address'] ?? null) ? $contactSettings['address'] : [];

    $socials = [
        'facebook'  => ['icon' => 'ri-facebook-box-fill',  'color' => 'text-blue-500'],
        'x'         => ['icon' => 'ri-twitter-x-line',     'color' => 'text-white'],
        'instagram' => ['icon' => 'ri-instagram-fill',     'color' => 'text-pink-400'],
        'youtube'   => ['icon' => 'ri-youtube-fill',       'color' => 'text-red-500'],
        'linkedin'  => ['icon' => 'ri-linkedin-box-fill',  'color' => 'text-blue-400'],
        'tiktok'    => ['icon' => 'ri-tiktok-fill',        'color' => 'text-white'],
        'github'    => ['icon' => 'ri-github-fill',        'color' => 'text-gray-300'],
    ];
@endphp

<footer class="bg-[#0b0e37] px-6 py-12 font-sans tracking-wide text-gray-300">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-3">

            {{-- Left: Logo + Description --}}
            <div class="space-y-4">
                <a href="{{ route('site.index') }}" class="inline-block">
                    @if ($footerLogo)
                        <img src="{{ $footerLogo }}" alt="{{ $footerSiteName }}" class="h-12 w-auto object-contain brightness-200" />
                    @else
                        <img src="{{ asset('logo.png') }}" alt="{{ $footerSiteName }}" class="h-12 w-auto object-contain" />
                    @endif
                </a>
                @if ($footerDescription)
                    <p class="text-sm leading-relaxed text-gray-400">{{ $footerDescription }}</p>
                @endif
            </div>

            {{-- Center: Navigation Links --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-400">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="/blog" class="text-sm text-gray-300 transition hover:text-white hover:underline">Blog</a></li>
                    <li><a href="{{ route('site.about') }}" class="text-sm text-gray-300 transition hover:text-white hover:underline">About Us</a></li>
                    <li><a href="{{ route('site.contact') }}" class="text-sm text-gray-300 transition hover:text-white hover:underline">Contact</a></li>
                    <li><a href="{{ route('site.termsOfService') }}" class="text-sm text-gray-300 transition hover:text-white hover:underline">Terms of Service</a></li>
                    <li><a href="{{ route('site.privacyPolicy') }}" class="text-sm text-gray-300 transition hover:text-white hover:underline">Privacy Policy</a></li>
                </ul>
            </div>

            {{-- Right: Contact + Social --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-400">Contact Us</h4>

                @if (count($phones))
                    <div class="flex items-start gap-2">
                        <i class="ri-phone-line mt-0.5 text-gray-400"></i>
                        <div class="space-y-1">
                            @foreach ($phones as $phone)
                                @if (!empty($phone))
                                    <p class="text-sm">{{ $phone }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (count($emails))
                    <div class="flex items-start gap-2">
                        <i class="ri-mail-line mt-0.5 text-gray-400"></i>
                        <div class="space-y-1">
                            @foreach ($emails as $email)
                                @if (!empty($email))
                                    <a href="mailto:{{ $email }}" class="block text-sm transition hover:text-white">{{ $email }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (count($addresses))
                    <div class="flex items-start gap-2">
                        <i class="ri-map-pin-line mt-0.5 text-gray-400"></i>
                        <div class="space-y-1">
                            @foreach ($addresses as $address)
                                @if (!empty($address))
                                    <p class="text-sm">{{ $address }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Social Icons --}}
                @php
                    $activeSocials = array_filter($socials, fn($key) => !empty($socialSettings[$key] ?? null), ARRAY_FILTER_USE_KEY);
                @endphp
                @if (count($activeSocials))
                    <div class="flex flex-wrap gap-3 pt-1">
                        @foreach ($activeSocials as $key => $meta)
                            <a href="{{ $socialSettings[$key] }}" target="_blank" rel="noopener noreferrer" class="transition hover:opacity-75">
                                <i class="{{ $meta['icon'] }} text-2xl {{ $meta['color'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <hr class="my-8 border-gray-700" />

        <p class="text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ $footerSiteName }}. All rights reserved.
        </p>
    </div>
</footer>
