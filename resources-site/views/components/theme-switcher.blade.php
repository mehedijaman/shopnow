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
        class="flex h-9 w-9 items-center justify-center rounded-xl text-site-muted transition hover:bg-site-surface-muted hover:text-site-link dark:text-site-muted dark:hover:bg-site-surface-muted dark:hover:text-site-link"
    >
        <i class="ri-moon-line text-lg dark:hidden"></i>
        <i class="ri-sun-line hidden text-lg dark:inline-block"></i>
    </button>
</div>
