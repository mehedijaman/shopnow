@php
    $footerLogo = setting('branding.logo_url');
    $footerSiteName = setting('branding.site_name', 'ShopNow');
    $footerDescription = setting('general.site_description');
    $contactSettings = settings_group('contact');
    $socialSettings = settings_group('social');

    $phones = is_array($contactSettings['phone'] ?? null) ? array_filter($contactSettings['phone'], fn($v) => !empty(trim($v))) : [];
    $emails = is_array($contactSettings['email'] ?? null) ? array_filter($contactSettings['email'], fn($v) => !empty(trim($v))) : [];
    $addresses = is_array($contactSettings['address'] ?? null) ? array_filter($contactSettings['address'], fn($v) => !empty(trim($v))) : [];

    $socials = [
        'facebook'  => ['icon' => 'ri-facebook-fill'],
        'x'         => ['icon' => 'ri-twitter-x-line'],
        'instagram' => ['icon' => 'ri-instagram-line'],
        'youtube'   => ['icon' => 'ri-youtube-fill'],
        'linkedin'  => ['icon' => 'ri-linkedin-fill'],
        'tiktok'    => ['icon' => 'ri-tiktok-fill'],
        'github'    => ['icon' => 'ri-github-fill'],
    ];
@endphp

<footer class="bg-gray-900 pb-8 pt-16 font-sans text-gray-300 dark:bg-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
            
            {{-- Column 1: Brand & Description --}}
            <div class="space-y-6">
                <a href="{{ route('site.index') }}" class="inline-block">
                    @if ($footerLogo)
                        {{-- brightness-0 invert makes the logo white --}}
                        <img src="{{ $footerLogo }}" alt="{{ $footerSiteName }}" class="h-12 w-auto object-contain brightness-0 invert" />
                    @else
                        <img src="{{ asset('logo.png') }}" alt="{{ $footerSiteName }}" class="h-12 w-auto object-contain brightness-0 invert" />
                    @endif
                </a>
                @if ($footerDescription)
                    <p class="text-[15px] leading-relaxed text-gray-400">
                        {{ $footerDescription }}
                    </p>
                @endif

            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Quick Links</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('site.index') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Home</a></li>
                    <li><a href="{{ route('shop.index') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Shop</a></li>
                    <li><a href="/blog" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Blog</a></li>
                    <li><a href="{{ route('site.about') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">About Us</a></li>
                    <li><a href="{{ route('site.contact') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Contact</a></li>
                </ul>
            </div>

            {{-- Column 3: Legal & Support --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Legal & Support</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('site.termsOfService') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Terms of Service</a></li>
                    <li><a href="{{ route('site.privacyPolicy') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Privacy Policy</a></li>
                    <li><a href="{{ route('site.refundPolicy') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">Refund Policy</a></li>
                    <li><a href="{{ route('customerAuth.loginForm') }}" class="text-[15px] text-gray-400 transition-colors hover:text-primary-400">My Account</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Contact Info</h3>
                <ul class="space-y-5">
                    @if (count($addresses))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-800 dark:bg-gray-800/50">
                                <i class="ri-map-pin-line text-lg text-primary-400"></i>
                            </div>
                            <div class="flex-1 pt-1 text-[15px] text-gray-400">
                                @foreach ($addresses as $address)
                                    <p>{{ $address }}</p>
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($phones))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-800 dark:bg-gray-800/50">
                                <i class="ri-phone-line text-lg text-primary-400"></i>
                            </div>
                            <div class="flex-1 pt-1 text-[15px] text-gray-400">
                                @foreach ($phones as $phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="block transition-colors hover:text-primary-400">{{ $phone }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($emails))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-800 dark:bg-gray-800/50">
                                <i class="ri-mail-line text-lg text-primary-400"></i>
                            </div>
                            <div class="flex-1 pt-1 text-[15px] text-gray-400">
                                @foreach ($emails as $email)
                                    <a href="mailto:{{ $email }}" class="block transition-colors hover:text-primary-400">{{ $email }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
                
                {{-- Social Icons --}}
                @php
                    $activeSocials = array_filter($socials, fn($key) => !empty($socialSettings[$key] ?? null), ARRAY_FILTER_USE_KEY);
                @endphp
                @if (count($activeSocials))
                    <div class="mt-6 flex flex-wrap gap-3">
                        @foreach ($activeSocials as $key => $meta)
                            <a href="{{ $socialSettings[$key] }}" target="_blank" rel="noopener noreferrer" 
                               class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 transition-all hover:-translate-y-1 hover:bg-primary-600 hover:text-white hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-primary-600">
                                <i class="{{ $meta['icon'] }} text-lg"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <div class="mt-16 flex flex-col items-center justify-between border-t border-gray-800 pt-8 sm:flex-row">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ $footerSiteName }}. All rights reserved.
            </p>
            
            <p class="mt-4 text-sm text-gray-500 sm:mt-0">
                Developed by <a href="#" class="font-medium text-gray-400 hover:text-white transition-colors">Developer</a>
            </p>
        </div>
    </div>
</footer>
