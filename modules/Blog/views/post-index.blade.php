@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="min-w-0">
            <span class="font-semibold text-gray-800">Blog</span>
        </li>
    </x-breadcrumb>

    <blog-toolbar
        :archive-options="{{ json_encode($archiveOptions) }}"
        :tags="{{ json_encode($tags) }}"
    ></blog-toolbar>

    <div class="bg-gray-50 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Section heading --}}
            <div class="mb-10 text-center">
                <div class="mb-3 flex items-center justify-center gap-3">
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Blog</h1>
                    <span class="h-1.5 w-8 rounded-full bg-primary-600"></span>
                </div>
                @if (isset($fromArchive) || isset($fromTag) || isset($fromSearch))
                    <p class="mt-2 text-sm text-gray-500">
                        @if (isset($fromArchive))
                            Posts from archive: <span class="font-semibold text-gray-700">{{ $fromArchive }}</span>
                        @elseif (isset($fromTag))
                            Posts tagged with: <span class="font-semibold text-gray-700">{{ $fromTag }}</span>
                        @elseif (isset($fromSearch))
                            Posts matching: <span class="font-semibold text-gray-700">{{ $fromSearch }}</span>
                        @endif
                    </p>
                @endif
            </div>

            @if ($posts->count())
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="group flex flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">

                            {{-- Thumbnail --}}
                            <a href="/blog/{{ $post->slug }}" class="block overflow-hidden bg-gray-100">
                                @if ($post->image_url)
                                    <img
                                        src="{{ $post->image_url }}"
                                        alt="{{ $post->title }}"
                                        loading="lazy"
                                        width="800"
                                        height="450"
                                        class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                @else
                                    <div class="flex aspect-video w-full items-center justify-center bg-gray-200">
                                        <i class="ri-image-line text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Body --}}
                            <div class="flex flex-1 flex-col p-5">

                                {{-- Tags --}}
                                @if ($post->tags->count())
                                    <div class="mb-3 flex flex-wrap gap-1.5">
                                        @foreach ($post->tags as $tag)
                                            <a
                                                href="/blog/tag/{{ $tag->slug }}"
                                                class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 hover:bg-primary-100"
                                            >
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Title --}}
                                <h2 class="flex-1 text-base font-semibold leading-snug text-gray-900 transition-colors group-hover:text-primary-600">
                                    <a href="/blog/{{ $post->slug }}">{{ $post->title }}</a>
                                </h2>

                                {{-- Excerpt --}}
                                @if ($post->content)
                                    <p class="mt-2 line-clamp-2 text-sm text-gray-500">
                                        {{ Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                @endif

                                {{-- Meta --}}
                                <div class="mt-4 flex items-center gap-3 border-t border-gray-100 pt-4 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="ri-calendar-line text-sm"></i>
                                        {{ $post->published_at->format('M d, Y') }}
                                    </span>
                                    @if ($post->author)
                                        <span class="flex items-center gap-1">
                                            @if ($post->author->image_url)
                                                <img
                                                    src="{{ $post->author->image_url }}"
                                                    alt="{{ $post->author->name }}"
                                                    loading="lazy"
                                                    class="h-4 w-4 rounded-full object-cover"
                                                />
                                            @else
                                                <i class="ri-user-line text-sm"></i>
                                            @endif
                                            {{ $post->author->name }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <i class="ri-article-line mb-3 block text-4xl text-gray-300"></i>
                    <p class="text-gray-500">No posts found.</p>
                </div>
            @endif

        </div>
    </div>
@endsection
