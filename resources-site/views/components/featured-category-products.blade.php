@props(['categories'])

@foreach ($categories as $category)
    @if ($category->products->isNotEmpty())
        <section class="mb-12 lg:mb-16">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-site-divider pb-4 dark:border-site-divider">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-site-text dark:text-site-text sm:text-3xl">
                        {{ $category->name }}
                    </h2>
                    <!-- <p class="mt-2 text-sm text-site-muted dark:text-site-muted">Discover our latest collection of {{ strtolower($category->name) }} products.</p> -->
                </div>
                <a href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                    class="group inline-flex w-full sm:w-auto items-center justify-center gap-1 rounded-lg border border-site-focus bg-transparent px-4 py-2 text-sm font-medium text-site-link transition-all duration-300 hover:bg-site-primary-soft dark:border-site-focus dark:text-site-link dark:hover:bg-site-surface-muted">
                    {{ __('site.home.view_all') }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 justify-items-center gap-x-4 gap-y-8 sm:gap-x-8 sm:gap-y-10 lg:grid-cols-4">
                @foreach ($category->products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif
@endforeach
