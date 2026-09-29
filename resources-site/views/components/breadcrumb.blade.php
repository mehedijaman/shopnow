<div class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6">
        <nav aria-label="{{ __('common.breadcrumb') }}">
            <ol class="flex min-w-0 flex-wrap items-center gap-x-1 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                <li class="flex shrink-0 items-center gap-1">
                    <a href="{{ route('site.index') }}" class="hover:text-primary-600 hover:underline">{{ __('site.nav.home') }}</a>
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                </li>
                {{ $slot }}
            </ol>
        </nav>
    </div>
</div>
