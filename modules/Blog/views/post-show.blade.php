@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="flex shrink-0 items-center gap-1">
            <a href="/blog" class="hover:text-primary-600 hover:underline">Blog</a>
            <i class="ri-arrow-right-s-line text-gray-400"></i>
        </li>
        <li class="min-w-0">
            <span class="block truncate font-semibold text-gray-800" title="{{ $post->title }}">{{ $post->title }}</span>
        </li>
    </x-breadcrumb>

    <div class="bg-gray-50 py-12 sm:py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <article itemscope itemtype="https://schema.org/Article" class="overflow-hidden rounded-2xl bg-white shadow-sm">

                {{-- Hero Image --}}
                @if ($post->image_url)
                    <div class="w-full overflow-hidden bg-gray-100">
                        <img
                            itemprop="image"
                            src="{{ $post->image_url }}"
                            alt="{{ $post->title }}"
                            width="1200"
                            height="675"
                            class="w-full object-contain"
                        />
                    </div>
                @endif

                <div class="p-6 sm:p-10">

                    {{-- Tags --}}
                    @if ($post->tags->count())
                        <div class="mb-4 flex flex-wrap gap-1.5">
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
                    <h1
                        itemprop="headline"
                        class="text-2xl font-extrabold leading-snug tracking-tight text-gray-900 sm:text-3xl lg:text-4xl"
                    >
                        {{ $post->title }}
                    </h1>

                    {{-- Meta row --}}
                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-gray-100 pb-6 text-sm text-gray-500">
                        <time
                            itemprop="datePublished"
                            datetime="{{ $post->published_at?->toIso8601String() }}"
                            class="flex items-center gap-1.5"
                        >
                            <i class="ri-calendar-2-line text-base"></i>
                            {{ $post->published_at?->format('F d, Y') }}
                        </time>

                        @if ($post->author)
                            <span itemprop="author" itemscope itemtype="https://schema.org/Person" class="flex items-center gap-1.5">
                                @if ($post->author->image_url)
                                    <img
                                        src="{{ $post->author->image_url }}"
                                        alt="{{ $post->author->name }}"
                                        width="20"
                                        height="20"
                                        loading="lazy"
                                        class="h-5 w-5 rounded-full object-cover ring-1 ring-gray-200"
                                    />
                                @else
                                    <i class="ri-user-3-line text-base"></i>
                                @endif
                                <span itemprop="name">{{ $post->author->name }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div
                        itemprop="articleBody"
                        class="prose prose-gray mt-8 max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl"
                    >
                        {!! $post->content !!}
                    </div>

                    {{-- Back link --}}
                    <div class="mt-10 border-t border-gray-100 pt-6">
                        <a href="/blog" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:underline">
                            <i class="ri-arrow-left-line"></i>
                            Back to Blog
                        </a>
                    </div>

                </div>
            </article>
        </div>
    </div>
@endsection
