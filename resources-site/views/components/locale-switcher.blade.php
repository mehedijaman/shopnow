{{--
    Language switcher for the storefront.
    
    One plain POST per locale: it works with JavaScript disabled, needs no
    client-side router, and returns the visitor to the page they were on.
--}}

@props(['class' => ''])

@php
    $current = current_locale();
    $returnTo = url()->current();
    $target = collect(supported_locales())->except($current);
@endphp

<div
    {{ $attributes->merge(['class' => 'flex items-center gap-2 ' . $class]) }}
>
    @foreach ($target as $code => $meta)
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $code }}" />
            <input type="hidden" name="_redirect" value="{{ $returnTo }}" />

            <button
                type="submit"
                lang="{{ $code }}"
                hreflang="{{ $code }}"
                title="{{ __('common.switch_language') }}"
                class="flex h-9 items-center justify-center rounded-xl px-3 text-xs font-bold text-slate-500 transition hover:bg-slate-100 hover:text-primary-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-primary-400"
            >
                {{ $meta['native'] }}
            </button>
        </form>
    @endforeach
</div>
