@props(['categories'])

@if ($categories->isNotEmpty())
    <section class="mb-12 lg:mb-16">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold tracking-tight text-site-text dark:text-site-text">{{ __('site.home.featured_categories') }}</h2>
        </div>

        <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                    class="group w-36 overflow-hidden rounded-md bg-site-surface shadow-sm ring-1 ring-site-border/80 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:bg-site-surface-muted dark:ring-site-border sm:w-44">
                    <span class="relative block aspect-[4/3] w-full overflow-hidden bg-site-surface-muted dark:bg-site-surface-muted">
                        @if ($category->image_url)
                            <img
                                src="{{ $category->image_url }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                            >
                        @else
                            <span class="flex h-full w-full items-center justify-center text-site-muted dark:text-site-muted">
                                <i class="ri-folder-line text-3xl"></i>
                            </span>
                        @endif
                    </span>
                    <span class="block truncate px-3 py-2.5 text-center text-sm font-semibold text-site-text dark:text-site-text">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endif
