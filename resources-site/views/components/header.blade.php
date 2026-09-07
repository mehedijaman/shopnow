@php 
    $logoUrl = setting('branding.logo_url'); 
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

    $currentRoute = request()->route()?->getName();
@endphp

{{-- ==============================================
     TOP ANNOUNCEMENT & CONTACT BAR
================================================ --}}
@if(!empty($phones) || $email || !empty($activeSocials))
<div class="border-b border-slate-800/80 bg-slate-950 text-xs font-medium tracking-wide text-slate-300 dark:bg-black">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
        
        {{-- Left: Contact Details --}}
        <div class="flex items-center gap-4 text-xs">
            @if(!empty($phones))
                <div class="flex items-center gap-1.5">
                    <i class="ri-phone-line text-sm text-primary-400"></i>
                    <span>
                        @foreach($phones as $phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="transition-colors hover:text-white">
                                {{ $phone }}
                            </a>@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif
            
            @if(!empty($phones) && $email)
                <div class="hidden sm:block h-3 w-px bg-slate-800"></div>
            @endif
            
            @if($email)
                <a href="mailto:{{ $email }}" class="hidden sm:flex items-center gap-1.5 transition-colors hover:text-white">
                    <i class="ri-mail-send-line text-sm text-primary-400"></i>
                    <span>{{ $email }}</span>
                </a>
            @endif
        </div>

        {{-- Right: Social Links --}}
        @if(!empty($activeSocials))
        <div class="flex items-center gap-3">
            @foreach($activeSocials as $social)
                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" class="text-slate-400 transition-colors hover:text-primary-400">
                    <i class="{{ $social['icon'] }} text-sm"></i>
                </a>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endif

{{-- ==============================================
     MOBILE HEADER
================================================ --}}
<div class="block lg:hidden">
    {{-- Top Sticky Header Bar --}}
    <header class="sticky top-0 z-50 flex h-16 w-full items-center justify-between border-b border-slate-200/80 bg-white/95 px-4 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/95">
        <button id="toggleOpen" aria-label="Open Menu" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
            <i class="ri-menu-line text-2xl"></i>
        </button>

        <a href="{{ route('site.index') }}" class="flex items-center justify-center">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[220px] sm:h-16 sm:max-w-[280px] object-contain" onerror="this.src='/logo.png'" />
            @else
                <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[220px] sm:h-16 sm:max-w-[280px] object-contain" />
            @endif
        </a>

        <div class="flex items-center">
            <navbar-cart-menu></navbar-cart-menu>
        </div>
    </header>
    
    {{-- Mobile Search Field --}}
    <div class="relative z-40 border-b border-slate-100 bg-slate-50 px-4 py-2.5 dark:border-slate-800 dark:bg-slate-900/60">
        <shop-search></shop-search>
    </div>
</div>

{{-- ==============================================
     DESKTOP HEADER (Two Rows)
================================================ --}}

{{-- Top Row: Logo, Search, User & Cart Actions --}}
<div class="relative z-[60] hidden w-full border-b border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-900 lg:block">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-8 py-5">
            {{-- Logo --}}
            <a href="{{ route('site.index') }}" class="flex shrink-0 items-center">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-20 w-auto max-w-[300px] object-contain xl:h-28 xl:max-w-[380px]" onerror="this.src='/logo.png'" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-20 w-auto max-w-[300px] object-contain xl:h-28 xl:max-w-[380px]" />
                @endif
            </a>

            {{-- Search Bar --}}
            <div class="w-full max-w-2xl flex-1">
                <shop-search></shop-search>
            </div>

            {{-- Customer Account & Cart Actions --}}
            <div class="flex shrink-0 items-center gap-4">
                @if (auth('customer')->check())
                    <div class="group relative">
                        <button type="button" class="flex items-center gap-2.5 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-3.5 py-2 text-slate-800 shadow-sm transition hover:border-slate-300 hover:bg-white hover:shadow-md dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-750">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                                <i class="ri-user-3-fill text-sm"></i>
                            </div>
                            <div class="flex flex-col text-left">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Account</span>
                                <span class="max-w-[100px] truncate text-xs font-black text-slate-900 dark:text-white">{{ explode(' ', auth('customer')->user()->name)[0] }}</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        
                        {{-- Dropdown Menu --}}
                        <div class="invisible absolute right-0 top-full z-50 mt-2 w-60 origin-top-right rounded-2xl border border-slate-100 bg-white p-2 opacity-0 shadow-xl transition-all duration-200 group-hover:visible group-hover:opacity-100 dark:border-slate-800 dark:bg-slate-900">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 mb-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Signed in as</p>
                                <p class="truncate text-xs font-bold text-slate-900 dark:text-white">{{ auth('customer')->user()->name }}</p>
                            </div>

                            <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-primary-400">
                                <i class="ri-user-settings-line text-sm text-slate-400"></i> My Profile
                            </a>
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-primary-400">
                                <i class="ri-file-list-3-line text-sm text-slate-400"></i> My Orders
                            </a>
                            <a href="{{ route('account.downloads') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-primary-400">
                                <i class="ri-download-2-line text-sm text-slate-400"></i> My Downloads
                            </a>

                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>

                            <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                <i class="ri-logout-box-r-line text-sm"></i> Logout
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customerAuth.loginForm') }}" class="flex items-center gap-2.5 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-3.5 py-2 text-slate-800 shadow-sm transition hover:border-slate-300 hover:bg-white hover:shadow-md dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                            <i class="ri-user-line text-sm"></i>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Welcome</span>
                            <span class="text-xs font-black text-slate-900 dark:text-white">Sign In / Register</span>
                        </div>
                    </a>
                @endif
                
                {{-- Separator --}}
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>

                {{-- Cart Menu Island --}}
                <navbar-cart-menu></navbar-cart-menu>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Row: Sticky Navbar --}}
<div class="sticky top-0 z-50 hidden w-full border-b border-slate-200/60 bg-white/90 shadow-sm backdrop-blur-md dark:border-slate-800/60 dark:bg-slate-900/90 lg:block">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="flex h-12 items-center gap-x-8">
            <a href="/" class="relative flex items-center py-3 text-sm font-extrabold text-slate-700 transition hover:text-primary-600 dark:text-slate-300 dark:hover:text-primary-400 {{ request()->is('/') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                <span>Home</span>
                @if(request()->is('/'))
                    <span class="absolute inset-x-0 bottom-0 h-[2.5px] rounded-full bg-primary-600"></span>
                @endif
            </a>
            <a href="{{ route('shop.index') }}" class="relative flex items-center py-3 text-sm font-extrabold text-slate-700 transition hover:text-primary-600 dark:text-slate-300 dark:hover:text-primary-400 {{ request()->routeIs('shop.*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                <span>Shop</span>
                @if(request()->routeIs('shop.*'))
                    <span class="absolute inset-x-0 bottom-0 h-[2.5px] rounded-full bg-primary-600"></span>
                @endif
            </a>
            <a href="/blog" class="relative flex items-center py-3 text-sm font-extrabold text-slate-700 transition hover:text-primary-600 dark:text-slate-300 dark:hover:text-primary-400 {{ request()->is('blog*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                <span>Blog</span>
                @if(request()->is('blog*'))
                    <span class="absolute inset-x-0 bottom-0 h-[2.5px] rounded-full bg-primary-600"></span>
                @endif
            </a>
            <a href="{{ route('site.about') }}" class="relative flex items-center py-3 text-sm font-extrabold text-slate-700 transition hover:text-primary-600 dark:text-slate-300 dark:hover:text-primary-400 {{ request()->routeIs('site.about') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                <span>About</span>
                @if(request()->routeIs('site.about'))
                    <span class="absolute inset-x-0 bottom-0 h-[2.5px] rounded-full bg-primary-600"></span>
                @endif
            </a>
            <a href="{{ route('site.contact') }}" class="relative flex items-center py-3 text-sm font-extrabold text-slate-700 transition hover:text-primary-600 dark:text-slate-300 dark:hover:text-primary-400 {{ request()->routeIs('site.contact') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                <span>Contact</span>
                @if(request()->routeIs('site.contact'))
                    <span class="absolute inset-x-0 bottom-0 h-[2.5px] rounded-full bg-primary-600"></span>
                @endif
            </a>
        </nav>
    </div>
</div>

{{-- ==============================================
     MOBILE MENU OFFCANVAS DRAWER
================================================ --}}
<div id="collapseMenu" class="fixed inset-0 z-[100] hidden lg:hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" id="toggleCloseBackdrop"></div>
    
    {{-- Drawer Sidebar --}}
    <div class="fixed inset-y-0 left-0 flex w-[85%] max-w-xs flex-col bg-white shadow-2xl transition-transform dark:bg-slate-900">
        {{-- Header --}}
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-5 dark:border-slate-800">
            <a href="{{ route('site.index') }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[200px] object-contain" onerror="this.src='/logo.png'" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-14 w-auto max-w-[200px] object-contain" />
                @endif
            </a>
            <button id="toggleCloseBtn" aria-label="Close Menu" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto px-4 py-5">
            {{-- Navigation Links --}}
            <nav class="space-y-1">
                <a href="/" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="ri-home-4-line text-lg text-slate-400"></i> Home
                </a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="ri-shopping-bag-line text-lg text-slate-400"></i> Shop
                </a>
                <a href="/blog" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="ri-newspaper-line text-lg text-slate-400"></i> Blog
                </a>
                <a href="{{ route('site.about') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="ri-information-line text-lg text-slate-400"></i> About
                </a>
                <a href="{{ route('site.contact') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="ri-mail-send-line text-lg text-slate-400"></i> Contact
                </a>
            </nav>

            {{-- User Account Section --}}
            <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-800">
                @if (auth('customer')->check())
                    <p class="mb-2.5 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">My Account</p>
                    <nav class="space-y-1">
                        <a href="{{ route('account.profile') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800">
                            <i class="ri-user-settings-line text-lg text-slate-400"></i> Profile
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800">
                            <i class="ri-file-list-3-line text-lg text-slate-400"></i> Orders
                        </a>
                        <a href="{{ route('account.downloads') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-primary-600 dark:text-slate-300 dark:hover:bg-slate-800">
                            <i class="ri-download-2-line text-lg text-slate-400"></i> Downloads
                        </a>
                        <div class="my-2 border-t border-slate-100 dark:border-slate-800"></div>
                        <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-3.5 rounded-2xl px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                            <i class="ri-logout-box-r-line text-lg"></i> Logout
                        </a>
                    </nav>
                @else
                    <div class="px-2">
                        <a href="{{ route('customerAuth.loginForm') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 py-3 text-sm font-bold text-white shadow-md transition hover:bg-primary-700">
                            <i class="ri-user-line text-base"></i> Sign In / Register
                        </a>
                    </div>
                @endif
            </div>

            {{-- Social Links --}}
            @if(!empty($activeSocials))
                <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-800">
                    <p class="mb-3 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Follow Us</p>
                    <div class="flex flex-wrap gap-2 px-4">
                        @foreach($activeSocials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-primary-600 hover:text-white dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-primary-600">
                                <i class="{{ $social['icon'] }} text-base"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Floating WhatsApp Button --}}
<whatsapp-floating-button :number="'{{ $whatsapp ?? '' }}'"></whatsapp-floating-button>

@push('scripts')
<script>
    document.addEventListener('click', function (e) {
        var menu = document.getElementById('collapseMenu')
        if (!menu) { return }

        // Open Menu
        if (e.target.closest('#toggleOpen')) {
            menu.classList.remove('hidden')
            document.body.style.overflow = 'hidden'
            return
        }

        // Close Menu via Button
        if (e.target.closest('#toggleCloseBtn')) {
            menu.classList.add('hidden')
            document.body.style.overflow = ''
            return
        }

        // Close Menu via Backdrop
        if (e.target === document.getElementById('toggleCloseBackdrop')) {
            menu.classList.add('hidden')
            document.body.style.overflow = ''
        }
    })
</script>
@endpush
