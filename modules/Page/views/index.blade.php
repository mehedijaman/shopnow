@extends('site-layout')

@section('seo_title', ($page->meta_tag_title ?? $page->title) . ' — ' . setting('branding.site_name', config('app.name')))

@if (!empty($seo['description']))
    @section('seo_description', $seo['description'])
@endif

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb>
        <li class="min-w-0">
            <span class="block truncate font-semibold text-site-text dark:text-site-text" title="{{ $page->title }}">{{ $page->title }}</span>
        </li>
    </x-breadcrumb>

    <div class="relative bg-site-surface-muted py-12 transition-colors dark:bg-site-surface-muted sm:py-16 lg:py-20">
        {{-- Subtle background decoration --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-site-primary/40 to-transparent dark:from-site-primary-hover/20"></div>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Header section --}}
            <div class="mb-10 text-center">
                <div class="inline-flex items-center gap-2 rounded-full bg-site-primary-soft px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wider text-site-link ring-1 ring-inset ring-site-focus/20 dark:bg-site-primary/50 dark:text-site-link dark:ring-site-focus/30">
                    <i class="ri-article-line text-sm"></i>
                    <span>Information</span>
                </div>

                <h1 class="mt-4 text-3xl font-black tracking-tight text-site-text dark:text-site-text sm:text-4xl lg:text-5xl">
                    {{ $page->title }}
                </h1>

                @if(!empty($page->updated_at))
                    <p class="mt-3 text-xs font-medium text-site-muted dark:text-site-muted">
                        Last updated on {{ $page->updated_at->format('F j, Y') }}
                    </p>
                @endif
            </div>

            {{-- Featured Banner Image (if available) --}}
            @if ($page->image_url)
                <div class="mb-10 overflow-hidden rounded-3xl border border-site-divider bg-site-surface shadow-xl shadow-slate-200/50 dark:border-site-divider dark:bg-site-surface-muted dark:shadow-none">
                    <img
                        src="{{ $page->image_url }}"
                        alt="{{ $page->title }}"
                        class="max-h-[420px] w-full object-cover"
                    />
                </div>
            @endif

            {{-- Main Content Card --}}
            <div class="overflow-hidden rounded-3xl border border-site-divider bg-site-surface p-6 shadow-xl shadow-slate-200/50 dark:border-site-divider dark:bg-site-surface-muted dark:shadow-none sm:p-10 lg:p-14">
                <div class="prose prose-slate max-w-none dark:prose-invert prose-headings:font-extrabold prose-headings:tracking-tight prose-a:font-semibold prose-a:text-site-link hover:prose-a:underline dark:prose-a:text-site-link prose-blockquote:rounded-r-2xl prose-blockquote:border-l-4 prose-blockquote:border-site-focus prose-blockquote:bg-site-primary-soft/50 prose-blockquote:px-5 prose-blockquote:py-2 dark:prose-blockquote:bg-site-surface-muted/50 prose-img:rounded-2xl prose-img:shadow-md">
                    {!! $page->content !!}
                </div>
            </div>

            {{-- Contact / Support Banner Card --}}
            <div class="mt-10 rounded-3xl border border-site-divider bg-gradient-to-br from-site-strong to-site-strong p-6 text-site-on-strong shadow-xl dark:border-site-divider sm:p-8">
                <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                    <div class="flex items-center gap-4 text-center sm:text-left">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-site-primary/20 text-site-link ring-1 ring-site-focus/30">
                            <i class="ri-question-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">{{ __('page::site.support.heading') }}</h3>
                            <p class="mt-0.5 text-xs text-site-text">{{ __('page::site.support.body') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('site.contact') }}" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-site-primary px-5 py-2.5 text-xs font-bold text-site-on-primary shadow-md transition-all hover:bg-site-primary-hover hover:shadow-lg">
                        <i class="ri-mail-send-line text-sm"></i>
                        <span>{{ __('site.footer.contact_support') }}</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
