@php 
    $logoUrl = setting('branding.logo_url'); 
    $darkLogoUrl = setting('branding.dark_logo_url');
    $siteName = setting('branding.site_name', 'ShopNow'); 
    
    // Helper function to extract repeater value safely
    $getRepeaterValue = function($key) {
        $val = setting($key);
        return is_array($val) && !empty($val) ? $val[0] : (is_string($val) && !empty(trim($val)) ? trim($val) : null);
    };
    
    // Helper function to extract all repeater values
    $getRepeaterArray = function($key) {
        $val = setting($key);
        if (is_array($val)) return array_filter($val, fn($v) => !empty(trim($v)));
        if (is_string($val) && !empty(trim($val))) return [trim($val)];
        return [];
    };

    $phones = $getRepeaterArray('contact.phone');
    $email = $getRepeaterValue('contact.email');
    $whatsapp = $getRepeaterValue('contact.whatsapp');

    $socialSettings = settings_group('social');
    $socialMeta = [
        'facebook'  => ['icon' => 'ri-facebook-circle-fill', 'label' => 'Facebook'],
        'x'         => ['icon' => 'ri-twitter-x-line', 'label' => 'X (Twitter)'],
        'instagram' => ['icon' => 'ri-instagram-line', 'label' => 'Instagram'],
        'youtube'   => ['icon' => 'ri-youtube-line', 'label' => 'YouTube'],
        'linkedin'  => ['icon' => 'ri-linkedin-fill', 'label' => 'LinkedIn'],
        'tiktok'    => ['icon' => 'ri-tiktok-fill', 'label' => 'TikTok'],
        'github'    => ['icon' => 'ri-github-fill', 'label' => 'GitHub'],
    ];

    $activeSocials = [];
    foreach ($socialMeta as $key => $meta) {
        $val = $socialSettings[$key] ?? null;
        if (is_string($val) && !empty(trim($val))) {
            $activeSocials[$key] = [
                'url'   => trim($val),
                'icon'  => $meta['icon'],
                'label' => $meta['label'],
            ];
        }
    }

    $headerSearchQuery = request()->route('searchText') ?? request()->query('q', '');
    $headerSearchQuery = is_string($headerSearchQuery) ? trim($headerSearchQuery) : '';
    $headerMenuLinks = [
        ['url' => route('site.index'), 'label' => __('site.nav.home')],
        ['url' => route('shop.index'), 'label' => __('site.nav.shop')],
        ['url' => url('/blog'), 'label' => __('site.nav.blog')],
        ['url' => route('site.about'), 'label' => __('site.nav.about')],
        ['url' => route('site.contact'), 'label' => __('site.nav.contact')],
    ];
@endphp

{{-- ==============================================
     TOP ANNOUNCEMENT & CONTACT BAR
================================================ --}}
@if(!empty($phones) || $email || !empty($activeSocials))
<div class="site-topbar border-b border-site-divider/80 text-xs font-medium tracking-wide">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
        
        {{-- Left: Contact Details --}}
        <div class="flex items-center gap-4 text-xs">
            @if(!empty($phones))
                <div class="flex items-center gap-1.5">
                    <i class="ri-phone-line text-sm text-site-link"></i>
                    <span>
                        @foreach($phones as $phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="transition-colors hover:text-site-text">
                                {{ $phone }}
                            </a>@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif
            
            @if(!empty($phones) && $email)
                <div class="hidden sm:block h-3 w-px bg-site-divider"></div>
            @endif
            
            @if($email)
                <a href="mailto:{{ $email }}" class="hidden sm:flex items-center gap-1.5 transition-colors hover:text-site-text">
                    <i class="ri-mail-send-line text-sm text-site-link"></i>
                    <span>{{ $email }}</span>
                </a>
            @endif
        </div>

        {{-- Right: Social Links --}}
        @if(!empty($activeSocials))
        <div class="flex items-center gap-3">
            @foreach($activeSocials as $social)
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" class="text-site-muted transition-colors hover:text-site-link">
                    <i class="{{ $social['icon'] }} text-sm"></i>
                </a>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endif

{{-- Mobile header and expanding search row --}}
<div class="site-header sticky top-0 z-50 lg:hidden" data-mobile-header>
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-2 px-3 sm:px-6">
        <a href="{{ route('site.index') }}" class="flex min-w-0 shrink items-center" data-header-logo>
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-[160px] object-contain sm:max-w-[200px] {{ $darkLogoUrl ? 'dark:hidden' : '' }}" onerror="this.src='/logo.png'" />
                @if ($darkLogoUrl)
                    <img src="{{ $darkLogoUrl }}" alt="{{ $siteName }}" class="hidden h-12 w-auto max-w-[160px] object-contain dark:block sm:max-w-[200px]" onerror="this.src='/logo.png'" />
                @endif
            @else
                <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-[160px] object-contain sm:max-w-[200px]" />
            @endif
        </a>
        <div class="flex shrink-0 items-center gap-0.5">
            <a href="{{ route('site.track') }}" aria-label="{{ __('site.nav.track_parcel') }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-site-text transition hover:text-site-link">
                <i class="ri-truck-line text-2xl" aria-hidden="true"></i>
            </a>
            <button type="button" data-mobile-search-toggle aria-controls="mobile-header-search" aria-expanded="false" aria-label="{{ __('site.aria.search_products') }}" data-open-label="{{ __('site.aria.search_products') }}" data-close-label="{{ __('site.aria.close_search') }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-site-text transition hover:text-site-link">
                <i class="ri-search-2-line text-2xl" data-mobile-search-icon aria-hidden="true"></i>
            </button>
            <button type="button" id="toggleOpen" aria-label="{{ __('site.aria.open_menu') }}" aria-controls="collapseMenu" aria-expanded="false" class="flex h-10 w-10 items-center justify-center rounded-lg text-site-text transition hover:text-site-link">
                <i class="ri-menu-3-line text-2xl" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <div id="mobile-header-search" hidden class="border-b border-site-divider px-2 pb-2 sm:px-6">
        <shop-search input-id="mobile-header-query" search-url="{{ route('shop.search') }}" store-name="{{ $siteName }}" :initial-query="{{ json_encode($headerSearchQuery) }}"></shop-search>
    </div>
</div>

{{-- Desktop logo, centered search and account actions --}}
<div class="site-header relative z-[60] hidden border-b border-site-divider lg:block" data-desktop-header>
    <div class="mx-auto flex min-h-20 max-w-7xl items-center gap-6 px-6 py-3 xl:gap-10 lg:px-8">
        <a href="{{ route('site.index') }}" class="flex w-44 shrink-0 items-center" data-header-logo>
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-full object-contain {{ $darkLogoUrl ? 'dark:hidden' : '' }}" onerror="this.src='/logo.png'" />
                @if ($darkLogoUrl)
                    <img src="{{ $darkLogoUrl }}" alt="{{ $siteName }}" class="hidden h-14 w-auto max-w-full object-contain dark:block" onerror="this.src='/logo.png'" />
                @endif
            @else
                <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-full object-contain" />
            @endif
        </a>
        <div class="min-w-0 flex-1" data-desktop-search>
            <shop-search input-id="desktop-header-query" search-url="{{ route('shop.search') }}" store-name="{{ $siteName }}" :initial-query="{{ json_encode($headerSearchQuery) }}"></shop-search>
        </div>
        <div class="flex shrink-0 items-center gap-3 xl:gap-4" data-header-actions>
            <a href="{{ route('site.track') }}" class="flex min-w-12 flex-col items-center gap-0.5 rounded-lg px-1.5 py-1 text-site-text transition hover:text-site-link">
                <i class="ri-truck-line text-2xl leading-none" aria-hidden="true"></i>
                <span class="whitespace-nowrap text-[10px] font-medium">{{ __('site.nav.track_parcel') }}</span>
            </a>
            @if (auth('customer')->check())
                <details class="group relative" data-header-menu>
                    <summary class="flex min-w-12 cursor-pointer list-none flex-col items-center gap-0.5 rounded-lg px-1.5 py-1 text-site-text hover:text-site-link [&::-webkit-details-marker]:hidden">
                        <i class="ri-user-line text-2xl leading-none" aria-hidden="true"></i>
                        <span class="text-[10px] font-medium">{{ __('site.nav.account') }}</span>
                    </summary>
                    <div class="absolute right-0 top-full mt-3 w-60 rounded-xl border border-site-divider bg-site-surface p-2 text-sm shadow-xl">
                        <p class="truncate px-3 py-2 font-semibold text-site-text">{{ auth('customer')->user()->name }}</p>
                        <a href="{{ route('account.profile') }}" class="block rounded-lg px-3 py-2 hover:bg-site-surface-muted">{{ __('site.account.my_profile') }}</a>
                        <a href="{{ route('account.orders') }}" class="block rounded-lg px-3 py-2 hover:bg-site-surface-muted">{{ __('site.account.my_orders') }}</a>
                        <a href="{{ route('account.downloads') }}" class="block rounded-lg px-3 py-2 hover:bg-site-surface-muted">{{ __('site.account.my_downloads') }}</a>
                        <a href="{{ route('customerAuth.logout') }}" class="block rounded-lg px-3 py-2 text-rose-600 hover:bg-rose-50">{{ __('site.account.logout') }}</a>
                    </div>
                </details>
            @else
                <a href="{{ route('customerAuth.loginForm') }}" class="flex min-w-12 flex-col items-center gap-0.5 rounded-lg px-1.5 py-1 text-site-text transition hover:text-site-link">
                    <i class="ri-user-line text-2xl leading-none" aria-hidden="true"></i>
                    <span class="whitespace-nowrap text-[10px] font-medium">{{ __('site.aria.sign_in') }}</span>
                </a>
            @endif
            <navbar-cart-menu></navbar-cart-menu>
            <details class="group relative" data-header-menu>
                <summary class="flex min-w-12 cursor-pointer list-none flex-col items-center gap-0.5 rounded-lg px-1.5 py-1 text-site-text hover:text-site-link [&::-webkit-details-marker]:hidden">
                    <i class="ri-menu-line text-2xl leading-none" aria-hidden="true"></i>
                    <span class="text-[10px] font-medium">{{ __('site.nav.more') }}</span>
                </summary>
                <div class="absolute right-0 top-full mt-3 w-56 rounded-xl border border-site-divider bg-site-surface p-2 text-sm shadow-xl">
                    @foreach ($headerMenuLinks as $link)
                        <a href="{{ $link['url'] }}" class="block rounded-lg px-3 py-2 hover:bg-site-surface-muted hover:text-site-link">{{ $link['label'] }}</a>
                    @endforeach
                    <div class="mt-2 flex items-center justify-between border-t border-site-divider px-2 pt-2" data-desktop-preferences>
                        <x-theme-switcher />
                        <x-locale-switcher />
                    </div>
                </div>
            </details>
        </div>
    </div>
</div>

{{-- Featured categories, independent of homepage section visibility --}}
<div class="site-header sticky top-0 z-50 hidden border-b border-site-divider lg:block">
    <nav class="mx-auto flex max-w-7xl items-center gap-6 overflow-x-auto px-8" aria-label="{{ __('site.home.featured_categories') }}" data-featured-category-nav>
        @forelse ($headerFeaturedCategories as $featuredCategory)
            @php $isCurrentCategory = request()->routeIs('shop.category') && (int) request()->route('id') === $featuredCategory->id; @endphp
            <a href="{{ route('shop.category', [$featuredCategory->id, $featuredCategory->slug]) }}" @if ($isCurrentCategory) aria-current="page" @endif class="shrink-0 whitespace-nowrap border-b-2 py-3 text-sm font-semibold transition hover:border-site-link hover:text-site-link {{ $isCurrentCategory ? 'border-site-link text-site-link' : 'border-transparent text-site-text' }}">{{ $featuredCategory->name }}</a>
        @empty
            <a href="{{ route('shop.index') }}" class="py-3 text-sm font-semibold text-site-text hover:text-site-link">{{ __('site.nav.shop') }}</a>
        @endforelse
    </nav>
</div>

{{-- ==============================================
     MOBILE MENU OFFCANVAS DRAWER
================================================ --}}
<div id="collapseMenu" role="dialog" aria-modal="true" aria-hidden="true" aria-label="{{ __('site.aria.open_menu') }}" class="fixed inset-0 z-[100] hidden lg:hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-site-strong/70 backdrop-blur-sm transition-opacity" id="toggleCloseBackdrop"></div>
    
    {{-- Drawer Sidebar --}}
    <div class="site-header fixed inset-y-0 left-0 flex w-[85%] max-w-xs flex-col bg-site-surface shadow-2xl transition-transform dark:bg-site-surface-muted">
        {{-- Header --}}
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-site-divider px-5 dark:border-site-divider">
            <a href="{{ route('site.index') }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[200px] object-contain {{ $darkLogoUrl ? 'dark:hidden' : '' }}" onerror="this.src='/logo.png'" />
                    @if ($darkLogoUrl)
                        <img src="{{ $darkLogoUrl }}" alt="{{ $siteName }}" class="hidden h-14 w-auto max-w-[200px] object-contain dark:block" onerror="this.src='/logo.png'" />
                    @endif
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[200px] object-contain" />
                @endif
            </a>
            <div class="flex items-center gap-1">
                <button type="button" id="toggleCloseBtn" aria-label="{{ __('site.aria.close_menu') }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-site-muted transition hover:bg-site-surface-muted dark:text-site-muted dark:hover:bg-site-surface-muted">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto px-4 py-5" data-mobile-menu-panel>
            @if ($headerFeaturedCategories->isNotEmpty())
                <nav class="mb-5 space-y-1 border-b border-site-divider pb-5" aria-label="{{ __('site.home.featured_categories') }}" data-mobile-featured-categories>
                    <p class="mb-2 px-4 text-xs font-bold text-site-muted">{{ __('site.home.featured_categories') }}</p>
                    @foreach ($headerFeaturedCategories as $featuredCategory)
                        <a href="{{ route('shop.category', [$featuredCategory->id, $featuredCategory->slug]) }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-site-text hover:bg-site-surface-muted hover:text-site-link">{{ $featuredCategory->name }}</a>
                    @endforeach
                </nav>
            @endif

            {{-- Navigation Links --}}
            <nav class="space-y-1">
                <a href="/" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-home-4-line text-lg text-site-muted"></i> {{ __('site.nav.home') }}
                </a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-shopping-bag-line text-lg text-site-muted"></i> {{ __('site.nav.shop') }}
                </a>
                <a href="/blog" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-newspaper-line text-lg text-site-muted"></i> {{ __('site.nav.blog') }}
                </a>
                <a href="{{ route('site.about') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-information-line text-lg text-site-muted"></i> {{ __('site.nav.about') }}
                </a>
                <a href="{{ route('site.contact') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-mail-send-line text-lg text-site-muted"></i> {{ __('site.nav.contact') }}
                </a>
                <a href="{{ route('site.track') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                    <i class="ri-truck-line text-lg text-site-muted"></i> {{ __('site.nav.track_parcel') }}
                </a>
            </nav>

            {{-- User Account Section --}}
            <div class="mt-6 border-t border-site-divider pt-5 dark:border-site-divider">
                @if (auth('customer')->check())
                    <p class="mb-2.5 px-4 text-[10px] font-bold uppercase tracking-wider text-site-muted">{{ __('site.account.my_account') }}</p>
                    <nav class="space-y-1">
                        <a href="{{ route('account.profile') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                            <i class="ri-user-settings-line text-lg text-site-muted"></i> {{ __('site.account.profile') }}
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                            <i class="ri-file-list-3-line text-lg text-site-muted"></i> {{ __('site.account.orders') }}
                        </a>
                        <a href="{{ route('account.downloads') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-site-text transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-text dark:hover:bg-site-surface-muted">
                            <i class="ri-download-2-line text-lg text-site-muted"></i> {{ __('site.account.downloads') }}
                        </a>
                        <div class="my-2 border-t border-site-divider dark:border-site-divider"></div>
                        <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                            <i class="ri-logout-box-r-line text-lg"></i> {{ __('site.account.logout') }}
                        </a>
                    </nav>
                @else
                    <div class="px-2">
                        <a href="{{ route('customerAuth.loginForm') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-site-primary py-3 text-sm font-bold text-site-on-primary shadow-md transition hover:bg-site-primary-hover">
                            <i class="ri-user-line text-base"></i> {{ __('site.account.sign_in_register') }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Social Links --}}
            @if(!empty($activeSocials))
                <div class="mt-6 border-t border-site-divider pt-5 dark:border-site-divider">
                    <p class="mb-3 px-4 text-[10px] font-bold uppercase tracking-wider text-site-muted">{{ __('common.follow_us') }}</p>
                    <div class="flex flex-wrap gap-2 px-4">
                        @foreach($activeSocials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-site-surface-muted text-site-text transition hover:bg-site-primary-hover hover:text-site-text dark:bg-site-surface-muted dark:text-site-text dark:hover:bg-site-primary-hover">
                                <i class="{{ $social['icon'] }} text-base"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div hidden class="flex-1 overflow-y-auto px-4 py-5" data-mobile-category-panel>
            <h2 class="mb-4 px-4 text-lg font-bold text-site-text">{{ __('product::site.shop.categories') }}</h2>
            <a href="{{ route('shop.index') }}" class="mb-3 block rounded-xl bg-site-surface-muted px-4 py-3 text-sm font-semibold text-site-text hover:text-site-link">{{ __('product::site.shop.all_products') }}</a>
            <nav class="space-y-1" aria-label="{{ __('product::site.shop.categories') }}" data-mobile-all-categories>
                @foreach ($headerCategories as $category)
                    @php $isCurrentCategory = request()->routeIs('shop.category') && (int) request()->route('id') === $category->id; @endphp
                    <a href="{{ route('shop.category', [$category->id, $category->slug]) }}" @if ($isCurrentCategory) aria-current="page" @endif class="flex items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold hover:bg-site-surface-muted hover:text-site-link {{ $isCurrentCategory ? 'bg-site-surface-muted text-site-link' : 'text-site-text' }}">
                        <span>{{ $category->name }}</span>
                        <i class="ri-arrow-right-s-line shrink-0 text-lg" aria-hidden="true"></i>
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="flex shrink-0 items-center justify-between border-t border-site-divider px-5 py-3" data-mobile-preferences>
            <span class="text-xs font-semibold text-site-muted">{{ __('site.nav.preferences') }}</span>
            <div class="flex items-center gap-2">
                <x-theme-switcher />
                <x-locale-switcher />
            </div>
        </div>
    </div>
</div>

{{-- Floating WhatsApp Button --}}
<whatsapp-floating-button :number="'{{ $whatsapp ?? '' }}'"></whatsapp-floating-button>

@push('scripts')
<script>
    (function () {
        var previousOverflow = ''
        var menuOpener = null

        function setSearch(open, restoreFocus) {
            var searchPanel = document.getElementById('mobile-header-search')
            var searchToggle = document.querySelector('[data-mobile-search-toggle]')
            searchPanel.hidden = !open
            searchToggle.setAttribute('aria-expanded', String(open))
            searchToggle.setAttribute('aria-label', open ? searchToggle.dataset.closeLabel : searchToggle.dataset.openLabel)
            var icon = searchToggle.querySelector('[data-mobile-search-icon]')
            icon.classList.toggle('ri-search-2-line', !open)
            icon.classList.toggle('ri-close-line', open)
            if (open) {
                var input = searchPanel.querySelector('input[type="search"]')
                if (input) input.focus()
            } else if (restoreFocus) {
                searchToggle.focus()
            }
        }

        function setMenu(open, restoreFocus, categories) {
            var menu = document.getElementById('collapseMenu')
            var menuToggle = document.getElementById('toggleOpen')
            var categoriesToggle = document.querySelector('[data-mobile-categories-toggle]')
            if (open) {
                setSearch(false, false)
                if (menu.classList.contains('hidden')) previousOverflow = document.body.style.overflow
                document.body.style.overflow = 'hidden'
                menuOpener = categories ? categoriesToggle : menuToggle
                menu.setAttribute('aria-label', menuOpener.getAttribute('aria-label'))
                document.querySelector('[data-mobile-menu-panel]').hidden = Boolean(categories)
                document.querySelector('[data-mobile-category-panel]').hidden = !categories
                document.querySelector('[data-mobile-preferences]').classList.toggle('hidden', Boolean(categories))
            } else if (!menu.classList.contains('hidden')) {
                document.body.style.overflow = previousOverflow
            }
            menu.classList.toggle('hidden', !open)
            menu.setAttribute('aria-hidden', String(!open))
            menuToggle.setAttribute('aria-expanded', String(open && !categories))
            if (categoriesToggle) categoriesToggle.setAttribute('aria-expanded', String(open && Boolean(categories)))
            if (open) document.getElementById('toggleCloseBtn').focus()
            else if (restoreFocus && menuOpener) menuOpener.focus()
        }

        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-mobile-search-toggle]')) setSearch(document.getElementById('mobile-header-search').hidden, false)
            else if (event.target.closest('#toggleOpen')) setMenu(true, false)
            else if (event.target.closest('[data-mobile-categories-toggle]')) setMenu(true, false, true)
            else if (event.target.closest('#toggleCloseBtn') || event.target.id === 'toggleCloseBackdrop') setMenu(false, true)
            document.querySelectorAll('[data-header-menu][open]').forEach(function (details) {
                if (!details.contains(event.target)) details.removeAttribute('open')
            })
        })

        document.addEventListener('keydown', function (event) {
            var menu = document.getElementById('collapseMenu')
            var searchPanel = document.getElementById('mobile-header-search')
            if (event.key === 'Escape') {
                if (!menu.classList.contains('hidden')) setMenu(false, true)
                else if (!searchPanel.hidden) setSearch(false, true)
                document.querySelectorAll('[data-header-menu][open]').forEach(function (details) {
                    details.removeAttribute('open')
                    details.querySelector('summary').focus()
                })
            }
            if (event.key === 'Tab' && !menu.classList.contains('hidden')) {
                var items = Array.from(menu.querySelectorAll('a[href], button:not([disabled]), select, input:not([type="hidden"])')).filter(function (element) { return element.offsetParent !== null })
                var first = items[0]
                var last = items[items.length - 1]
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus() }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus() }
            }
        })

        window.matchMedia('(min-width: 1024px)').addEventListener('change', function (event) {
            if (event.matches) { setMenu(false, false); setSearch(false, false) }
        })
    })()
</script>
@endpush
