<header
    class="relative z-50 bg-white font-[sans-serif] tracking-wide shadow-md"
>
    <section
        class="flex min-h-[75px] flex-wrap items-center gap-4 border-b border-gray-200 px-4 py-3 sm:px-10 lg:justify-center"
    >
        <div
            class="absolute left-10 z-50 flex gap-2 rounded bg-gray-100 pl-2 max-lg:hidden"
        >
            <i class="ri-search-line text-2xl hover:text-skin-primary-9"></i>
            <form action="{{ route('site.product.search') }}">
                <input
                    type="text"
                    name="searchText"
                    placeholder="Search..."
                    class="w-full rounded bg-transparent text-sm outline-none"
                />
            </form>
        </div>

        <a href="{{ route('site.index') }}" class="shrink-0">
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
    </section>

    <nav-bar :categories="{{ $categories }}"></nav-bar>
</header>
