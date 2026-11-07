@php
    $footerLogo = setting('branding.logo_url');
    $footerDarkLogo = setting('branding.dark_logo_url');
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

<footer class="site-footer border-t border-site-divider bg-site-strong pb-10 pt-16 font-sans text-site-muted dark:bg-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
            
            {{-- Column 1: Brand & Description --}}
            <div class="space-y-5">
                <a href="{{ route('site.index') }}" class="block text-center sm:inline-block">
                    @if ($footerDarkLogo)
                        <img src="{{ $footerDarkLogo }}" alt="{{ $footerSiteName }}" class="h-24 w-auto max-w-[300px] sm:h-20 sm:max-w-[280px] object-contain" onerror="this.src='/logo.png'" />
                    @elseif ($footerLogo)
                        <img src="{{ $footerLogo }}" alt="{{ $footerSiteName }}" class="h-24 w-auto max-w-[300px] sm:h-20 sm:max-w-[280px] object-contain" onerror="this.src='/logo.png'" />
                    @else
                        <img src="{{ asset('logo.png') }}" alt="{{ $footerSiteName }}" class="h-24 w-auto max-w-[300px] sm:h-20 sm:max-w-[280px] object-contain" />
                    @endif
                </a>

                @if ($footerDescription)
                    <p class="text-xs leading-relaxed text-site-muted">
                        {{ $footerDescription }}
                    </p>
                @else
                    <p class="text-xs leading-relaxed text-site-muted">
                        {{ __('site.footer.tagline') }}
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
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-site-strong text-site-muted transition-all duration-200 hover:-translate-y-0.5 hover:bg-site-primary-hover hover:text-site-on-primary shadow-sm"
                            >
                                <i class="{{ $meta['icon'] }} text-base"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Column 2: Navigation --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-site-text">
                    <span>{{ __('site.footer.quick_links') }}</span>
                </h3>
                <ul class="space-y-3 text-xs font-medium">
                    <li><a href="{{ route('site.index') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.nav.home') }}</a></li>
                    <li><a href="{{ route('shop.index') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.shop_products') }}</a></li>
                    <li><a href="/blog" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.latest_blog') }}</a></li>
                    <li><a href="{{ route('site.about') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.about_us') }}</a></li>
                    <li><a href="{{ route('site.contact') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.contact_support') }}</a></li>
                    <li><a href="{{ route('site.track') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.track_your_parcel') }}</a></li>
                </ul>
            </div>

            {{-- Column 3: Legal & Account --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-site-text">
                    <span>{{ __('site.footer.legal_policy') }}</span>
                </h3>
                <ul class="space-y-3 text-xs font-medium">
                    <li><a href="{{ route('site.termsOfService') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.terms_of_service') }}</a></li>
                    <li><a href="{{ route('site.privacyPolicy') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.privacy_policy') }}</a></li>
                    <li><a href="{{ route('site.refundPolicy') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.footer.refund_policy') }}</a></li>
                    <li><a href="{{ route('customerAuth.loginForm') }}" class="inline-flex items-center gap-1.5 transition-all duration-200 hover:translate-x-1 hover:text-site-link"><i class="ri-arrow-right-s-line text-site-muted dark:text-site-text"></i> {{ __('site.account.my_account') }}</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div>
                <h3 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-site-text">
                    <span>{{ __('site.footer.get_in_touch') }}</span>
                </h3>
                <ul class="space-y-4 text-xs font-medium">
                    @if (count($addresses))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-site-strong text-site-link">
                                <i class="ri-map-pin-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-site-text">
                                @foreach ($addresses as $address)
                                    <p class="leading-relaxed">{{ $address }}</p>
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($phones))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-site-strong text-site-link">
                                <i class="ri-phone-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-site-text">
                                @foreach ($phones as $phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="transition-colors hover:text-site-link">{{ $phone }}</a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @if (count($emails))
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-site-strong text-site-link">
                                <i class="ri-mail-send-line text-sm"></i>
                            </div>
                            <div class="flex-1 pt-1 text-site-text">
                                @foreach ($emails as $email)
                                    <a href="mailto:{{ $email }}" class="block truncate transition-colors hover:text-site-link">{{ $email }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-site-divider pt-8 text-xs text-site-muted dark:text-site-muted sm:flex-row">
            <div class="text-center sm:text-left">
                <p>
                    &copy; {{ date('Y') }} <span class="font-bold text-site-text">{{ $footerSiteName }}</span>. {{ __('site.footer.all_rights_reserved') }}
                </p>
                <p class="mt-1.5">
                    {{ __('site.footer.developed_by') }}
                    <a
                        href="https://www.gtechservice.net"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-semibold text-site-text transition-colors hover:text-site-link"
                    >gTechService</a>
                </p>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ route('site.privacyPolicy') }}" class="transition-colors hover:text-site-text">{{ __('site.footer.privacy') }}</a>
                <a href="{{ route('site.termsOfService') }}" class="transition-colors hover:text-site-text">{{ __('site.footer.terms') }}</a>
                <a href="{{ route('site.contact') }}" class="transition-colors hover:text-site-text">{{ __('site.footer.support') }}</a>
            </div>
        </div>
    </div>
</footer>
