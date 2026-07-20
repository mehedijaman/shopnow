@php 
    $logoUrl = setting('branding.logo_url'); 
    $siteName = setting('branding.site_name', 'ShopNow'); 
@endphp

<header class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/90 backdrop-blur-md transition-all dark:border-gray-800 dark:bg-gray-900/90">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        
        {{-- Mobile Menu Button --}}
        <div class="flex items-center lg:hidden">
            <button id="toggleOpen" class="rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                <i class="ri-menu-line text-2xl"></i>
            </button>
        </div>

        {{-- Logo --}}
        <div class="flex lg:flex-1">
            <a href="{{ route('site.index') }}" class="flex items-center gap-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto sm:h-12 object-contain" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-10 w-auto sm:h-12 object-contain" />
                @endif
            </a>
        </div>

        {{-- Desktop Navigation --}}
        <nav class="hidden lg:flex lg:gap-x-10">
            <a href="/" class="text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">Home</a>
            <a href="{{ route('shop.index') }}" class="text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">Shop</a>
            <a href="/blog" class="text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">Blog</a>
            <a href="{{ route('site.about') }}" class="text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">About</a>
            <a href="{{ route('site.contact') }}" class="text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">Contact</a>
        </nav>

        {{-- Right Actions (Cart & Auth) --}}
        <div class="flex flex-1 items-center justify-end space-x-4 sm:space-x-6">
            <navbar-cart-menu></navbar-cart-menu>

            @if (auth('customer')->check())
                <div class="group relative hidden lg:block">
                    <button class="flex items-center gap-1.5 rounded-full px-3 py-2 text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <i class="ri-user-smile-line text-xl"></i>
                        <span class="text-sm font-semibold">{{ explode(' ', auth('customer')->user()->name)[0] }}</span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    {{-- Desktop Dropdown --}}
                    <div class="invisible absolute right-0 top-full mt-1 w-56 origin-top-right rounded-2xl border border-gray-100 bg-white opacity-0 shadow-xl ring-1 ring-black ring-opacity-5 transition-all duration-200 group-hover:visible group-hover:opacity-100 dark:border-gray-700 dark:bg-gray-800">
                        <div class="p-2">
                            <a href="{{ route('account.profile') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                <i class="ri-user-settings-line text-lg text-gray-400"></i> My Profile
                            </a>
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                <i class="ri-file-list-3-line text-lg text-gray-400"></i> Orders
                            </a>
                            <a href="{{ route('account.downloads') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                <i class="ri-download-2-line text-lg text-gray-400"></i> Downloads
                            </a>
                            <hr class="my-2 border-gray-100 dark:border-gray-700" />
                            <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">
                                <i class="ri-logout-box-r-line text-lg text-red-400"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('customerAuth.loginForm') }}" class="hidden lg:flex items-center gap-2 rounded-full px-3 py-2 text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                    <i class="ri-user-line text-xl"></i>
                    <span class="text-sm font-semibold">Sign In</span>
                </a>
            @endif
        </div>
    </div>
</header>

{{-- Mobile Menu (Offcanvas) --}}
<div id="collapseMenu" class="fixed inset-0 z-[100] hidden lg:hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" id="toggleCloseBackdrop"></div>
    
    {{-- Sidebar --}}
    <div class="fixed inset-y-0 left-0 w-[85%] max-w-sm flex flex-col bg-white shadow-2xl dark:bg-gray-900">
        <div class="flex h-16 shrink-0 items-center justify-between px-6 border-b border-gray-100 dark:border-gray-800">
            <a href="{{ route('site.index') }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-8 w-auto object-contain" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-8 w-auto object-contain" />
                @endif
            </a>
            <button id="toggleCloseBtn" class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4">
            <nav class="flex flex-col gap-2">
                <a href="/" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-bold text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                    <i class="ri-home-4-line text-xl text-gray-400"></i> Home
                </a>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-bold text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                    <i class="ri-shopping-bag-line text-xl text-gray-400"></i> Shop
                </a>
                <a href="/blog" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-bold text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                    <i class="ri-newspaper-line text-xl text-gray-400"></i> Blog
                </a>
                <a href="{{ route('site.about') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-bold text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                    <i class="ri-question-line text-xl text-gray-400"></i> About
                </a>
                <a href="{{ route('site.contact') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-bold text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                    <i class="ri-mail-send-line text-xl text-gray-400"></i> Contact
                </a>
            </nav>

            <div class="mt-8">
                @if (auth('customer')->check())
                    <div class="mb-4 px-4 text-xs font-bold uppercase tracking-widest text-gray-400">My Account</div>
                    <nav class="flex flex-col gap-2">
                        <a href="{{ route('account.profile') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                            <i class="ri-user-settings-line text-xl text-gray-400"></i> Profile
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                            <i class="ri-file-list-3-line text-xl text-gray-400"></i> Orders
                        </a>
                        <a href="{{ route('account.downloads') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-medium text-gray-900 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-white dark:hover:bg-gray-800">
                            <i class="ri-download-2-line text-xl text-gray-400"></i> Downloads
                        </a>
                        <div class="my-2 border-t border-gray-100 dark:border-gray-800"></div>
                        <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-4 rounded-xl px-4 py-3.5 text-base font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">
                            <i class="ri-logout-box-r-line text-xl text-red-400"></i> Logout
                        </a>
                    </nav>
                @else
                    <div class="px-2">
                        <a href="{{ route('customerAuth.loginForm') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3.5 text-base font-bold text-white shadow-md transition-all hover:bg-primary-700 hover:shadow-lg">
                            <i class="ri-user-line text-xl"></i> Sign In / Register
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
