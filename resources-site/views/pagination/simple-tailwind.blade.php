@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-muted bg-site-surface border border-site-divider cursor-default leading-5 rounded-md dark:text-site-muted dark:bg-site-surface-muted dark:border-site-divider">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-text bg-site-surface border border-site-divider leading-5 rounded-md hover:text-site-muted dark:hover:text-site-muted focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-text transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:text-site-text dark:focus:border-site-focus dark:active:bg-site-surface-muted dark:active:text-site-text">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-text bg-site-surface border border-site-divider leading-5 rounded-md hover:text-site-muted dark:hover:text-site-muted focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-text transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:text-site-text dark:focus:border-site-focus dark:active:bg-site-surface-muted dark:active:text-site-text">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-muted bg-site-surface border border-site-divider cursor-default leading-5 rounded-md dark:text-site-muted dark:bg-site-surface-muted dark:border-site-divider">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
