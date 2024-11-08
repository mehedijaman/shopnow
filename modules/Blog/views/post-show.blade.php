@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <article>
                <h2
                    class="mb-2 mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                >
                    {{ $post->title }}
                </h2>

                <div class="mb-4 flex items-center gap-2 text-sm">
                    <div class="flex items-center gap-1 italic text-gray-600">
                        <i class="ri-calendar-2-line"></i>
                        {{ $post->published_at->format('F d, Y') }}
                    </div>

                    <div class="flex items-center gap-1 italic text-gray-600">
                        @if ($post->author)
                            @if ($post->author->image_url)
                                <img
                                    src="{{ $post->author->image_url }}"
                                    alt="{{ $post->author->name }}"
                                    class="h-4 w-4 rounded-md bg-gray-100 object-cover"
                                />
                            @else
                                <i class="ri-user-3-line"></i>
                            @endif
                            {{ $post->author->name }}
                        @endif
                    </div>
                </div>

                <div class="relative mb-4 w-full">
                    <a href="/blog/{{ $post->slug }}" class="block">
                        @if ($post->image_url)
                            <img
                                src="{{ $post->image_url }}"
                                alt="{{ $post->title }}"
                                class="aspect-[16/9] w-full rounded-md bg-gray-100 object-cover sm:aspect-[2/1] lg:aspect-[3/2]"
                            />
                        @endif

                        <div
                            class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10"
                        ></div>
                    </a>
                </div>

                <div>
                    {!! $post->content !!}
                </div>
            </article>
        </div>
    </div>
@endsection
