@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <article itemscope itemtype="https://schema.org/Article">
                <h1
                    itemprop="headline"
                    class="mb-2 mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                >
                    {{ $post->title }}
                </h1>

                <div class="mb-4 flex items-center gap-2 text-sm">
                    <time
                        itemprop="datePublished"
                        datetime="{{ $post->published_at?->toIso8601String() }}"
                        class="flex items-center gap-1 italic text-gray-600"
                    >
                        <i class="ri-calendar-2-line"></i>
                        {{ $post->published_at?->format('F d, Y') }}
                    </time>

                    @if ($post->author)
                    <span itemprop="author" itemscope itemtype="https://schema.org/Person" class="flex items-center gap-1 italic text-gray-600">
                        @if ($post->author->image_url)
                            <img
                                src="{{ $post->author->image_url }}"
                                alt="{{ $post->author->name }}"
                                width="16"
                                height="16"
                                loading="lazy"
                                class="h-4 w-4 rounded-md bg-gray-100 object-cover"
                            />
                        @else
                            <i class="ri-user-3-line"></i>
                        @endif
                        <span itemprop="name">{{ $post->author->name }}</span>
                    </span>
                    @endif
                </div>

                @if ($post->image_url)
                <div class="relative mb-4 w-full">
                    <img
                        itemprop="image"
                        src="{{ $post->image_url }}"
                        alt="{{ $post->title }}"
                        width="1200"
                        height="675"
                        class="aspect-video w-full rounded-md bg-gray-100 object-cover sm:aspect-2/1 lg:aspect-3/2"
                    />
                </div>
                @endif

                <div itemprop="articleBody">
                    {!! $post->content !!}
                </div>
            </article>
        </div>
    </div>
@endsection
