<div class="border-b border-gray-200">
    <div class="mx-auto max-w-7xl">
        <section
            class="relative flex flex-col items-center px-10 py-4 max-lg:min-h-[60px] lg:min-h-[80px] lg:justify-center"
        >
            <a href="{{ route('site.index') }}">
                @php $logoUrl = setting('branding.logo_url'); $siteName = setting('branding.site_name', 'ShopNow'); @endphp
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-30 w-auto" />
                @else
                    <img src="{{ asset('logo.png') }}" alt="{{ $siteName }}" class="h-30 w-auto" />
                @endif
            </a>

            {{-- <shop-search></shop-search> --}}

            {{--
                <div
                class="flex items-center space-x-6 max-md:ml-auto md:absolute md:right-10"
                >
                <a href="https://facebook.com" target="_blank">
                <i
                class="ri-facebook-box-line text-2xl hover:text-skin-primary-9"
                ></i>
                </a>
                <a href="https://twitter.com" target="_blank">
                <i
                class="ri-twitter-line text-2xl hover:text-skin-primary-9"
                ></i>
                </a>
                <a href="https://instagram.com" target="_blank">
                <i
                class="ri-instagram-line text-2xl hover:text-skin-primary-9"
                ></i>
                </a>
                <a href="https://pinterest.com" target="_blank">
                <i
                class="ri-pinterest-line text-2xl hover:text-skin-primary-9"
                ></i>
                </a>
                </div>
            --}}
        </section>
    </div>
</div>

{{-- Navbar --}}
<header
    class="sticky top-0 z-50 border-b bg-skin-primary-4 font-sans tracking-wide"
>
    <div class="mx-auto max-w-7xl px-6 md:py-3 lg:px-6">

        <div class="relative flex flex-wrap justify-between">
            <div
                id="collapseMenu"
                class="max-lg:hidden max-lg:before:fixed max-lg:before:inset-0 max-lg:before:z-50 max-lg:before:bg-black max-lg:before:opacity-40 lg:block!"
            >
                <button
                    id="toggleClose"
                    class="fixed right-4 top-2 z-100 rounded-full bg-white p-3 lg:hidden"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>

                <ul
                    class="z-50 max-lg:fixed max-lg:left-0 max-lg:top-0 max-lg:h-full max-lg:w-2/3 max-lg:min-w-[300px] max-lg:space-y-3 max-lg:overflow-auto max-lg:bg-white max-lg:p-4 max-lg:shadow-md lg:flex lg:gap-x-10"
                >
                    <li class="px-3 max-lg:border-b max-lg:pb-4 lg:hidden">
                        <a href="{{ route('site.index') }}">
                            @if ($logoUrl ?? false)
                                <img src="{{ $logoUrl }}" alt="{{ $siteName ?? 'ShopNow' }}" class="h-10 w-auto object-contain" />
                            @else
                                <img src="{{ asset('logo.png') }}" alt="{{ $siteName ?? 'ShopNow' }}" class="h-10 w-auto object-contain" />
                            @endif
                        </a>
                    </li>
                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/"
                            class="block text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                        >
                            <i class="ri-home-4-line"></i>
                            Home
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('shop.index') }}"
                            class="block text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                        >
                            <i class="ri-shopping-bag-line"></i>
                            Shop
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/blog"
                            class="block text-[15px] font-semibold text-gray-700 hover:text-[#007bff]"
                        >
                            <i class="ri-newspaper-line"></i>
                            Blog
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('site.about') }}"
                            class="block text-[15px] font-semibold text-gray-700 hover:text-[#007bff]"
                        >
                            <i class="ri-question-line"></i>
                            About
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('site.contact') }}"
                            class="block text-[15px] font-semibold text-gray-700 hover:text-[#007bff]"
                        >
                            <i class="ri-mail-send-line"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex lg:hidden">
                <button id="toggleOpen" class="rounded-md p-2 text-gray-700 hover:bg-gray-100">
                    <i class="ri-menu-line text-2xl"></i>
                </button>
            </div>

            <div
                class="ml-auto flex items-center space-x-6 lg:absolute lg:right-0"
            >
                <navbar-cart-menu></navbar-cart-menu>

                @if (auth('customer')->check())
                    <div
                        class="group relative max-lg:border-b max-lg:px-3 max-lg:py-3"
                    >
                        <a
                            href="javascript:void(0)"
                            class="block text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                        >
                            Welcome, {{ auth('customer')->user()->name }}
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul
                            class="absolute left-0 top-5 z-50 block max-h-0 min-w-[200px] space-y-2 overflow-hidden bg-white px-6 shadow-lg transition-all duration-500 group-hover:max-h-[700px] group-hover:pb-4 group-hover:pt-6 group-hover:opacity-100 max-lg:top-8"
                        >
                            <li class="border-b py-2">
                                <a
                                    href="{{ route('account.profile') }}"
                                    class="flex gap-2 text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                                >
                                    My Profile
                                </a>
                            </li>

                            <li class="border-b py-2">
                                <a
                                    href="{{ route('account.orders') }}"
                                    class="flex gap-2 text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                                >
                                    Orders
                                </a>
                            </li>

                            <li class="border-b py-2">
                                <a
                                    href="{{ route('account.downloads') }}"
                                    class="flex gap-2 text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                                >
                                    Downloads
                                </a>
                            </li>

                            <li class="border-b py-2">
                                <a
                                    href="{{ route('customerAuth.logout') }}"
                                    class="flex gap-2 text-[15px] font-semibold text-gray-700 hover:fill-[#007bff] hover:text-[#007bff]"
                                >
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    <a
                        href="{{ route('customerAuth.loginForm') }}"
                        class="inline-block cursor-pointer border-gray-300"
                    >
                        <i
                            class="ri-user-line text-xl hover:text-skin-primary-9"
                        ></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</header>


@push('scripts')
<script>
    // Use event delegation so listeners survive Vue's deferred DOM replacement
    document.addEventListener('click', function (e) {
        var menu = document.getElementById('collapseMenu')
        if (!menu) { return }

        if (e.target.closest('#toggleOpen')) {
            menu.style.display = 'block'
            document.body.style.overflow = 'hidden'
            return
        }

        if (e.target.closest('#toggleClose')) {
            menu.style.display = ''
            document.body.style.overflow = ''
            return
        }

        // Click on the dark backdrop (the wrapper div, not the <ul> inside)
        if (e.target === menu) {
            menu.style.display = ''
            document.body.style.overflow = ''
        }
    })
</script>
@endpush
