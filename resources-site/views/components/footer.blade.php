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
        'facebook'  => ['icon' => 'ri-facebook-fill', 'label' => 'Facebook'],
        'x'         => ['icon' => 'ri-twitter-x-line', 'label' => 'X (Twitter)'],
        'instagram' => ['icon' => 'ri-instagram-line', 'label' => 'Instagram'],
        'youtube'   => ['icon' => 'ri-youtube-fill', 'label' => 'YouTube'],
        'linkedin'  => ['icon' => 'ri-linkedin-fill', 'label' => 'LinkedIn'],
        'tiktok'    => ['icon' => 'ri-tiktok-fill', 'label' => 'TikTok'],
        'github'    => ['icon' => 'ri-github-fill', 'label' => 'GitHub'],
    ];

    $activeSocials = array_filter($socials, fn($key) => !empty($socialSettings[$key] ?? null), ARRAY_FILTER_USE_KEY);
@endphp

<footer class="border-t border-slate-900 bg-slate-950 pb-10 pt-16 font-sans text-slate-400 dark:bg-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
            
            {{-- Column 1: Brand & Description --}}
            <div class="space-y-5">
                <a href="{{ route('site.index') }}" class="inline-block">
                    @if ($footerLogo)
                        <img src="{{ $footerLogo }}" alt="{{ $footerSiteName }}" class="h-12 w-auto max-w-[180px] object-contain" onerror="this.src='/logo.png'" />
                    @else
                        <img src="{{ asset('logo.png') }}" alt="{{ $footerSiteName }}" class="h-12 w-auto max-w-[180px] object-contain" />
                    @endif
                </a>

                @if ($footerDescription)
                    <p class="text-xs leading-relaxed text-slate-400">
                        {{ $footerDescription }}
                    </p>
                @else
                    <p class="text-xs leading-relaxed text-slate-400">
                        Your trusted online store for high-quality products, fast delivery, and exceptional customer experience.
                    </p>
                @endif

                @if (count($activeSocials))
                    <div class="flex flex-wrap gap-2 pt-2">
                        @foreach ($activeSocials as $key => $meta)
                            <a
                                href="{{ $socialSettings[$key] }}"
                                target="_blank"
                                rel="noopener noreferrer" 
                                aria-label="{{ $meta['label'] }}"
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-slate-400 transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary-600 hover:text-white shadow-sm"
                            >
                                <i class="{{ $meta['icon'] }} text-base"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Column 2: Navigation --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-white">
                    <span>Quick Links</span>
                </h3>
                <ul class="space-y-3 text-xs font-medium">
                    <li><a href="{{ route('site.index') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Home</a></li>
                    <li><a href="{{ route('shop.index') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Shop Products</a></li>
                    <li><a href="/blog" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Latest Blog</a></li>
                    <li><a href="{{ route('site.about') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> About Us</a></li>
                    <li><a href="{{ route('site.contact') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Contact Support</a></li>
                </ul>
            </div>

            {{-- Column 3: Legal & Account --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-white">
                    <span>Legal & Policy</span>
                </h3>
                <ul class="space-y-3 text-xs font-medium">
                    <li><a href="{{ route('site.termsOfService') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Terms of Service</a></li>
                    <li><a href="{{ route('site.privacyPolicy') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Privacy Policy</a></li>
                    <li><a href="{{ route('site.refundPolicy') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> Refund & Return Policy</a></li>
                    <li><a href="{{ route('customerAuth.loginForm') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-primary-400"><i class="ri-arrow-right-s-line text-slate-600"></i> My Account</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-white">
                    <span>Get in Touch</span>
                </h3>
                <ul class="space-y-4 text-xs font-medium">
                    @if (count($addresses))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-primary-400">
                                <i class="ri-map-pin-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-slate-300">
                                @foreach ($addresses as $address)
                                    <p class="leading-relaxed">{{ $address }}</p>
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($phones))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-primary-400">
                                <i class="ri-phone-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-slate-300">
                                @foreach ($phones as $phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="transition-colors hover:text-primary-400">{{ $phone }}</a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($emails))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-primary-400">
                                <i class="ri-mail-send-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-slate-300">
                                @foreach ($emails as $email)
                                    <a href="mailto:{{ $email }}" class="block truncate transition-colors hover:text-primary-400">{{ $email }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-slate-900 pt-8 text-xs text-slate-500 sm:flex-row">
            <p>
                &copy; {{ date('Y') }} <span class="font-bold text-slate-300">{{ $footerSiteName }}</span>. All rights reserved.
            </p>
            
            <div class="flex items-center gap-6">
                <a href="{{ route('site.privacyPolicy') }}" class="transition-colors hover:text-slate-300">Privacy</a>
                <a href="{{ route('site.termsOfService') }}" class="transition-colors hover:text-slate-300">Terms</a>
                <a href="{{ route('site.contact') }}" class="transition-colors hover:text-slate-300">Support</a>
            </div>
        </div>
    </div>
</footer>
