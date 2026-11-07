@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <x-breadcrumb>
        <li class="flex shrink-0 items-center gap-1">
            <a href="/blog" class="hover:text-site-link hover:underline">{{ __('blog::site.posts.breadcrumb') }}</a>
            <i class="ri-arrow-right-s-line text-site-muted"></i>
        </li>
        <li class="min-w-0">
            <span class="block truncate font-semibold text-site-text dark:text-site-text" title="{{ $post->title }}">{{ $post->title }}</span>
        </li>
    </x-breadcrumb>

    <div class="bg-site-surface-muted dark:bg-site-surface-muted py-12 sm:py-16">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <article itemscope itemtype="https://schema.org/Article" class="overflow-hidden rounded-2xl bg-site-surface dark:bg-site-surface-muted shadow-sm">

                {{-- Hero Image --}}
                @if ($post->image_url)
                    <div class="w-full overflow-hidden bg-site-surface-muted dark:bg-site-surface-muted">
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
                                    class="rounded-full bg-site-primary-soft px-2.5 py-0.5 text-xs font-medium text-site-link hover:bg-site-primary-soft"
                                >
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Title --}}
                    <h1
                        itemprop="headline"
                        class="text-2xl font-extrabold leading-snug tracking-tight text-site-text dark:text-site-text sm:text-3xl lg:text-4xl"
                    >
                        {{ $post->title }}
                    </h1>

                    {{-- Meta row --}}
                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-site-divider dark:border-site-divider pb-6 text-sm text-site-muted dark:text-site-muted">
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
                                        class="h-5 w-5 rounded-full object-cover ring-1 ring-site-border dark:ring-site-border"
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
                        class="prose prose-gray mt-8 max-w-none prose-headings:font-bold prose-headings:text-site-text prose-a:text-site-link prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl"
                    >
                        {!! $post->content !!}
                    </div>

                    {{-- Back link --}}
                    <div class="mt-10 border-t border-site-divider dark:border-site-divider pt-6">
                        <a href="/blog" class="inline-flex items-center gap-1.5 text-sm font-medium text-site-link hover:underline">
                            <i class="ri-arrow-left-line"></i>
                            {{ __('blog::site.posts.back_to_blog') }}
                        </a>
                    </div>

                </div>
            </article>
        </div>
    </div>
@endsection
