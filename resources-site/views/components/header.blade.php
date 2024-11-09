<div class="border-b border-gray-200">
    <div class="mx-auto max-w-7xl">
        <section
            class="relative flex flex-col items-center gap-5 px-10 py-3 max-lg:min-h-[60px] lg:min-h-[80px] lg:justify-center"
        >
            <a href="{{ route('site.index') }}">
                {{--
                    <img
                    src="https://readymadeui.com/readymadeui.svg"
                    alt="logo"
                    class="w-36 md:w-[170px]"
                    />
                --}}
                <span
                    class="rounded-md bg-blue-500 px-4 py-1 text-3xl font-extrabold text-skin-neutral-3 hover:text-skin-neutral-6 dark:text-skin-neutral-1 dark:hover:text-skin-primary-9"
                >
                    ShopNow
                </span>
            </a>

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
        </section>
    </div>
</div>

<header
    class="relative z-50 min-h-[60px] border-b bg-white font-sans tracking-wide"
>
    <div class="mx-auto max-w-7xl px-6 py-2 lg:px-6">
        <div
            class="mx-auto my-6 flex h-10 rounded-full border border-transparent bg-gray-100 px-6 focus-within:border-blue-500 focus-within:bg-transparent lg:w-2/4"
        >
            <i class="ri-search-2-line mr-2 mt-1 text-xl"></i>
            <input
                type="text"
                placeholder="Search..."
                class="w-full bg-transparent text-[15px] font-semibold text-gray-600 outline-none"
            />
        </div>

        <div class="relative mt-6 flex flex-wrap justify-between">
            <div
                id="collapseMenu"
                class="max-lg:hidden max-lg:before:fixed max-lg:before:inset-0 max-lg:before:z-50 max-lg:before:bg-black max-lg:before:opacity-40 lg:!block"
            >
                <button
                    id="toggleClose"
                    class="fixed right-4 top-2 z-[100] rounded-full bg-white p-3 lg:hidden"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>

                <ul
                    class="z-50 max-lg:fixed max-lg:left-0 max-lg:top-0 max-lg:h-full max-lg:w-2/3 max-lg:min-w-[300px] max-lg:space-y-3 max-lg:overflow-auto max-lg:bg-white max-lg:p-4 max-lg:shadow-md lg:flex lg:gap-x-10"
                >
                    <li class="px-3 max-lg:border-b max-lg:pb-4 lg:hidden">
                        <a href="{{ route('site.index') }}">
                            <span
                                class="rounded-md bg-blue-500 px-4 py-1 text-3xl font-extrabold text-skin-neutral-3 hover:text-skin-neutral-6 dark:text-skin-neutral-1 dark:hover:text-skin-primary-9"
                            >
                                ShopNow
                            </span>
                        </a>
                    </li>
                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/"
                            class="block text-[15px] font-semibold hover:text-[#007bff]"
                        >
                            <i class="ri-home-4-line"></i>
                            Home
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('shop.index') }}"
                            class="block text-[15px] font-semibold hover:text-[#007bff]"
                        >
                            <i class="ri-shopping-bag-line"></i>
                            Shop
                        </a>
                    </li>

                    <li
                        class="group relative max-lg:border-b max-lg:px-3 max-lg:py-3"
                    >
                        <a
                            href="javascript:void(0)"
                            class="block text-[15px] font-semibold text-gray-600 hover:fill-[#007bff] hover:text-[#007bff]"
                        >
                            <i class="ri-bookmark-line"></i>
                            Categories
                            <i class="ri-arrow-down-s-line"></i>
                        </a>
                        <ul
                            class="absolute left-0 top-5 z-50 block max-h-0 min-w-[350px] space-y-2 overflow-hidden bg-white px-6 shadow-lg transition-all duration-500 group-hover:max-h-[700px] group-hover:pb-4 group-hover:pt-6 group-hover:opacity-100 max-lg:top-8"
                        >
                            @foreach ($categories as $category)
                                <li class="border-b py-3">
                                    <a
                                        href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                                        class="flex gap-2 text-[15px] font-semibold text-gray-600 hover:fill-[#007bff] hover:text-[#007bff]"
                                    >
                                        <i class="ri-bookmark-line"></i>
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="/blog"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-newspaper-line"></i>
                            Blog
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('site.about') }}"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-question-line"></i>
                            About
                        </a>
                    </li>

                    <li class="max-lg:border-b max-lg:px-3 max-lg:py-3">
                        <a
                            href="{{ route('site.contact') }}"
                            class="block text-[15px] font-semibold text-gray-600 hover:text-[#007bff]"
                        >
                            <i class="ri-mail-send-line"></i>
                            Contact
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex lg:hidden">
                <button id="toggleOpen">
                    <i class="ri-menu-line"></i>
                </button>
            </div>

            <div
                class="ml-auto flex items-center space-x-6 lg:absolute lg:right-0"
            >
                <navbar-cart-menu></navbar-cart-menu>
                <button class="inline-block cursor-pointer border-gray-300">
                    <i
                        class="ri-user-line text-xl hover:text-skin-primary-9"
                    ></i>
                </button>
            </div>
        </div>
    </div>
</header>

{{--
    <script>
    var toggleOpen = document.getElementById('toggleOpen')
    var toggleClose = document.getElementById('toggleClose')
    var collapseMenu = document.getElementById('collapseMenu')
    
    function handleClick() {
    if (collapseMenu.style.display === 'block') {
    collapseMenu.style.display = 'none'
    } else {
    collapseMenu.style.display = 'block'
    }
    }
    
    toggleOpen.addEventListener('click', handleClick)
    toggleClose.addEventListener('click', handleClick)
    </script>
--}}
