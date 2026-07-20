@props(['latestPosts'])

@if ($latestPosts->isNotEmpty())
    <section class="mt-12 lg:mt-16">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-gray-200 pb-4 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Latest from the Blog
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Stay updated with our latest news and articles.</p>
            </div>
            <a href="/blog"
                class="group inline-flex w-full sm:w-auto items-center justify-center gap-1 rounded-lg border border-primary-600 bg-transparent px-4 py-2 text-sm font-medium text-primary-600 transition-all duration-300 hover:bg-primary-50 dark:border-primary-500 dark:text-primary-400 dark:hover:bg-gray-800">
                View All Posts
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($latestPosts as $post)
                <article class="flex flex-col">
                    <a href="/blog/{{ $post->slug }}" class="block overflow-hidden rounded-lg">
                        @if ($post->image_url)
                            <img
                                src="{{ $post->image_url }}"
                                alt="{{ $post->title }}"
                                loading="lazy"
                                class="aspect-video w-full rounded-lg bg-gray-100 object-cover transition-transform duration-300 hover:scale-105"
                            />
                        @else
                            <div class="flex aspect-video w-full items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </a>

                    <div class="mt-4 flex flex-1 flex-col">
                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                <path d="M17 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9V3H15V1H17V3ZM4 9V19H20V9H4ZM6 11H8V13H6V11ZM11 11H13V13H11V11ZM16 11H18V13H16V11Z" />
                            </svg>
                            {{ $post->published_at->format('M d, Y') }}
                        </div>

                        <h3 class="mt-2 flex-1 text-base font-semibold leading-snug text-gray-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">
                            <a href="/blog/{{ $post->slug }}">{{ $post->title }}</a>
                        </h3>

                        @if ($post->content)
                            <p class="mt-2 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                        @endif

                        <a
                            href="/blog/{{ $post->slug }}"
                            class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400"
                        >
                            Read more
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
