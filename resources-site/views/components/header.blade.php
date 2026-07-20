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

    $facebook = setting('social.facebook');
    $facebook = is_string($facebook) && !empty(trim($facebook)) ? trim($facebook) : null;
    
    $twitter = setting('social.x');
    $twitter = is_string($twitter) && !empty(trim($twitter)) ? trim($twitter) : null;
    
    $instagram = setting('social.instagram');
    $instagram = is_string($instagram) && !empty(trim($instagram)) ? trim($instagram) : null;
    
    $youtube = setting('social.youtube');
    $youtube = is_string($youtube) && !empty(trim($youtube)) ? trim($youtube) : null;
@endphp

{{-- ==============================================
     TOP ANNOUNCEMENT BAR
================================================ --}}
@if(!empty($phones) || $email || $facebook || $twitter || $instagram || $youtube)
<div class="bg-gray-900 text-[13px] font-medium tracking-wide text-white dark:bg-black">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
        
        {{-- Left: Contact / Phone (Hidden on very small screens) --}}
        <div class="hidden items-center gap-4 sm:flex">
            @foreach($phones as $phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="flex items-center gap-1.5 transition-colors hover:text-primary-400">
                    <i class="ri-phone-line text-[15px]"></i>
                    <span>{{ $phone }}</span>
                </a>
                @if(!$loop->last)
                    <div class="h-3 w-px bg-gray-700"></div>
                @endif
            @endforeach
            
            @if(!empty($phones) && $email)
                <div class="h-3 w-px bg-gray-700"></div>
            @endif
            
            @if($email)
                <a href="mailto:{{ $email }}" class="flex items-center gap-1.5 transition-colors hover:text-primary-400">
                    <i class="ri-mail-send-line text-[15px]"></i>
                    <span>{{ $email }}</span>
                </a>
            @endif
        </div>

        {{-- Center: Announcement --}}
        <!-- <div class="flex-1 text-center">
            Free shipping on all orders over <span class="font-bold text-primary-400">$100!</span> 
            <a href="{{ route('shop.index') }}" class="ml-1 underline underline-offset-2 transition-colors hover:text-gray-300">Shop Now</a>
        </div> -->

        {{-- Right: Social Icons (Hidden on mobile) --}}
        <div class="hidden items-center gap-3.5 md:flex">
            @if($facebook)
                <a href="{{ $facebook }}" target="_blank" aria-label="Facebook" class="transition-colors hover:text-primary-400">
                    <i class="ri-facebook-circle-fill text-[16px]"></i>
                </a>
            @endif
            
            @if($twitter)
                <a href="{{ $twitter }}" target="_blank" aria-label="X (Twitter)" class="transition-colors hover:text-primary-400">
                    <i class="ri-twitter-x-line text-[16px]"></i>
                </a>
            @endif
            
            @if($instagram)
                <a href="{{ $instagram }}" target="_blank" aria-label="Instagram" class="transition-colors hover:text-primary-400">
                    <i class="ri-instagram-line text-[16px]"></i>
                </a>
            @endif
            
            @if($youtube)
                <a href="{{ $youtube }}" target="_blank" aria-label="YouTube" class="transition-colors hover:text-primary-400">
                    <i class="ri-youtube-line text-[16px]"></i>
                </a>
            @endif
        </div>

    </div>
</div>
@endif

{{-- ==============================================
     MOBILE HEADER
================================================ --}}
<div class="block lg:hidden">
    {{-- Top Sticky Bar --}}
    <header class="sticky top-0 z-50 flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center pl-2 sm:pl-4">
            <button id="toggleOpen" class="rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                <i class="ri-menu-line text-2xl"></i>
            </button>
        </div>

        <a href="{{ route('site.index') }}" class="flex flex-1 items-center justify-center">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain sm:h-14" />
            @else
                <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain sm:h-14" />
            @endif
        </a>

        <div class="flex items-center pr-2 sm:pr-4">
            <navbar-cart-menu></navbar-cart-menu>
        </div>
    </header>
    
    {{-- Mobile Search Field (Non-sticky, scrolls away) --}}
    <div class="relative z-40 border-b border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/50">
        <shop-search></shop-search>
    </div>
</div>

{{-- ==============================================
     DESKTOP HEADER (Two Rows)
================================================ --}}

{{-- Top Row: Logo, Search, Actions (Not Sticky) --}}
<div class="hidden lg:block w-full bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 relative z-[60]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-10 py-5">
            {{-- Logo --}}
            <a href="{{ route('site.index') }}" class="flex-shrink-0">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-16 w-auto object-contain xl:h-20" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-16 w-auto object-contain xl:h-20" />
                @endif
            </a>

            {{-- Search --}}
            <div class="w-full max-w-2xl flex-1">
                <shop-search></shop-search>
            </div>

            {{-- Actions --}}
            <div class="flex flex-shrink-0 items-center space-x-5">
                @if (auth('customer')->check())
                    <div class="group relative">
                        <button class="flex items-center gap-2.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                                <i class="ri-user-smile-line text-lg"></i>
                            </div>
                            <div class="flex flex-col items-start text-left">
                                <span class="text-[10px] font-bold uppercase leading-none tracking-widest text-gray-400">Account</span>
                                <span class="text-[13px] font-extrabold leading-tight text-gray-900 dark:text-white">{{ explode(' ', auth('customer')->user()->name)[0] }}</span>
                            </div>
                            <i class="ri-arrow-down-s-line ml-1 text-gray-400 transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        
                        {{-- Desktop Dropdown --}}
                        <div class="invisible absolute right-0 top-full mt-2 w-64 origin-top-right rounded-2xl border border-gray-100 bg-white opacity-0 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] ring-1 ring-black ring-opacity-5 transition-all duration-200 group-hover:visible group-hover:opacity-100 dark:border-gray-700 dark:bg-gray-800 z-50">
                            <div class="p-3">
                                <div class="mb-2 px-3 pb-2 pt-1">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Welcome Back</p>
                                    <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ auth('customer')->user()->name }}</p>
                                </div>
                                <hr class="mb-2 border-gray-100 dark:border-gray-700" />
                                
                                <a href="{{ route('account.profile') }}" class="group/item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors group-hover/item:bg-primary-50 group-hover/item:text-primary-600 dark:bg-gray-700 dark:text-gray-400 dark:group-hover/item:bg-primary-900/30 dark:group-hover/item:text-primary-400">
                                        <i class="ri-user-settings-line text-lg"></i>
                                    </div>
                                    My Profile
                                </a>
                                <a href="{{ route('account.orders') }}" class="group/item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors group-hover/item:bg-primary-50 group-hover/item:text-primary-600 dark:bg-gray-700 dark:text-gray-400 dark:group-hover/item:bg-primary-900/30 dark:group-hover/item:text-primary-400">
                                        <i class="ri-file-list-3-line text-lg"></i>
                                    </div>
                                    Orders
                                </a>
                                <a href="{{ route('account.downloads') }}" class="group/item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary-600 dark:text-gray-300 dark:hover:bg-gray-700/50 dark:hover:text-primary-400">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors group-hover/item:bg-primary-50 group-hover/item:text-primary-600 dark:bg-gray-700 dark:text-gray-400 dark:group-hover/item:bg-primary-900/30 dark:group-hover/item:text-primary-400">
                                        <i class="ri-download-2-line text-lg"></i>
                                    </div>
                                    Downloads
                                </a>
                                
                                <hr class="my-2 border-gray-100 dark:border-gray-700" />
                                
                                <a href="{{ route('customerAuth.logout') }}" class="group/item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-500 transition-colors group-hover/item:bg-red-100 dark:bg-red-900/20 dark:group-hover/item:bg-red-900/40">
                                        <i class="ri-logout-box-r-line text-lg"></i>
                                    </div>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customerAuth.loginForm') }}" class="flex items-center gap-2.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-700">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <i class="ri-user-line text-lg"></i>
                        </div>
                        <div class="flex flex-col items-start text-left pr-2">
                            <span class="text-[10px] font-bold uppercase leading-none tracking-widest text-gray-400">Welcome</span>
                            <span class="text-[13px] font-extrabold leading-tight text-gray-900 dark:text-white">Sign In</span>
                        </div>
                    </a>
                @endif
                
                {{-- Separator --}}
                <div class="hidden sm:block h-8 w-px bg-gray-200 dark:bg-gray-700"></div>

                <navbar-cart-menu></navbar-cart-menu>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Row: Navbar (Sticky) --}}
<div class="hidden lg:block sticky top-0 z-50 w-full border-b border-gray-100 bg-white/80 backdrop-blur-lg shadow-[0_4px_20px_-15px_rgba(0,0,0,0.1)] dark:border-gray-800 dark:bg-gray-900/80">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="flex h-14 items-center gap-x-10">
            <a href="/" class="group relative py-4 text-[15px] font-medium text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                Home
                <span class="absolute inset-x-0 bottom-0 h-[2px] scale-x-0 transform bg-primary-600 transition-transform duration-300 group-hover:scale-x-100"></span>
            </a>
            <a href="{{ route('shop.index') }}" class="group relative py-4 text-[15px] font-medium text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                Shop
                <span class="absolute inset-x-0 bottom-0 h-[2px] scale-x-0 transform bg-primary-600 transition-transform duration-300 group-hover:scale-x-100"></span>
            </a>
            <a href="/blog" class="group relative py-4 text-[15px] font-medium text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                Blog
                <span class="absolute inset-x-0 bottom-0 h-[2px] scale-x-0 transform bg-primary-600 transition-transform duration-300 group-hover:scale-x-100"></span>
            </a>
            <a href="{{ route('site.about') }}" class="group relative py-4 text-[15px] font-medium text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                About
                <span class="absolute inset-x-0 bottom-0 h-[2px] scale-x-0 transform bg-primary-600 transition-transform duration-300 group-hover:scale-x-100"></span>
            </a>
            <a href="{{ route('site.contact') }}" class="group relative py-4 text-[15px] font-medium text-gray-700 transition-colors hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                Contact
                <span class="absolute inset-x-0 bottom-0 h-[2px] scale-x-0 transform bg-primary-600 transition-transform duration-300 group-hover:scale-x-100"></span>
            </a>
        </nav>
    </div>
</div>

{{-- ==============================================
     MOBILE MENU OFFCANVAS
================================================ --}}
<div id="collapseMenu" class="fixed inset-0 z-[100] hidden lg:hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" id="toggleCloseBackdrop"></div>
    
    {{-- Sidebar --}}
    <div class="fixed inset-y-0 left-0 w-[85%] max-w-sm flex flex-col bg-white shadow-2xl dark:bg-gray-900">
        <div class="flex h-16 shrink-0 items-center justify-between px-6 border-b border-gray-100 dark:border-gray-800">
            <a href="{{ route('site.index') }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain" />
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

{{-- WhatsApp Floating Button --}}
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
