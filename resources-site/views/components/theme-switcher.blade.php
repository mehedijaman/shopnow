{{--
    Dark/light theme switch for the storefront.

    Stateless: the pre-paint script in site-layout.blade.php owns the
    localStorage decision and exposes window.ShopNowTheme, so every instance
    on the page stays in sync through one delegated listener.
--}}

@props(['class' => ''])

<div
    {{ $attributes->merge(['class' => 'flex items-center ' . $class]) }}
>
    <button
        type="button"
        data-theme-toggle
        aria-label="{{ __('site.aria.toggle_theme') }}"
        title="{{ __('site.aria.toggle_theme') }}"
        class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-primary-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-primary-400"
    >
        <i class="ri-moon-line text-lg dark:hidden"></i>
        <i class="ri-sun-line hidden text-lg dark:inline-block"></i>
    </button>
</div>
