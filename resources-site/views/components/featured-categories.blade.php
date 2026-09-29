@props(['categories'])

@if ($categories->isNotEmpty())
    <section class="mb-12 lg:mb-16">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('site.home.featured_categories') }}</h2>
        </div>

        <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                    class="group w-36 overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-gray-200/80 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:bg-gray-900 dark:ring-gray-800 sm:w-44">
                    <span class="relative block aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                        @if ($category->image_url)
                            <img
                                src="{{ $category->image_url }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                            >
                        @else
                            <span class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                                <i class="ri-folder-line text-3xl"></i>
                            </span>
                        @endif
                    </span>
                    <span class="block truncate px-3 py-2.5 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endif
