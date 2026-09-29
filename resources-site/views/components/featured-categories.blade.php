@props(['categories'])

@if ($categories->isNotEmpty())
    <section class="mb-12 lg:mb-16">
        <div class="mb-6 flex items-end justify-between">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('site.home.featured_categories') }}</h2>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                    class="group overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-gray-200/80 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:bg-gray-900 dark:ring-gray-800">
                    <span class="relative block aspect-[3/2] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                        @if ($category->image_url)
                            <img
                                src="{{ $category->image_url }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                            >
                        @else
                            <span class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                                <i class="ri-folder-line text-4xl"></i>
                            </span>
                        @endif
                    </span>
                    <span class="block truncate px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endif
