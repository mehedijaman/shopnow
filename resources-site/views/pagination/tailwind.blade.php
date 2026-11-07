@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('pagination.navigation') }}" class="flex items-center justify-between ">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-muted bg-site-surface border border-site-divider cursor-default leading-5 rounded-md dark:text-site-muted dark:bg-site-surface-muted dark:border-site-divider">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-site-text bg-site-surface border border-site-divider leading-5 rounded-md hover:text-site-muted dark:hover:text-site-muted focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-text transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:text-site-text dark:focus:border-site-focus dark:active:bg-site-surface-muted dark:active:text-site-text">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-site-text bg-site-surface border border-site-divider leading-5 rounded-md hover:text-site-muted dark:hover:text-site-muted focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-text transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:text-site-text dark:focus:border-site-focus dark:active:bg-site-surface-muted dark:active:text-site-text">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-site-muted bg-site-surface border border-site-divider cursor-default leading-5 rounded-md dark:text-site-muted dark:bg-site-surface-muted dark:border-site-divider">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-site-text leading-5 dark:text-site-muted">
                    {!! __('pagination.Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        {!! __('pagination.to') !!}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('pagination.of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('pagination.results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse shadow-xs rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-site-text bg-site-surface border border-site-divider cursor-default rounded-l-md leading-5 dark:bg-site-surface-muted dark:border-site-divider"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-site-muted dark:text-site-muted bg-site-surface border border-site-divider rounded-l-md leading-5 hover:text-site-muted focus:z-10 focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-muted dark:active:text-site-muted transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:active:bg-site-surface-muted dark:focus:border-site-focus"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-site-text dark:text-site-text bg-site-surface border border-site-divider cursor-default leading-5 dark:bg-site-surface-muted dark:border-site-divider">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-site-text bg-site-surface border border-site-divider cursor-default leading-5 dark:bg-site-surface-muted dark:border-site-divider">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-site-text bg-site-surface border border-site-divider leading-5 hover:text-site-muted focus:z-10 focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-text dark:active:text-site-text transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:text-site-muted dark:hover:text-site-text dark:active:bg-site-surface-muted dark:focus:border-site-focus"
                                        aria-label="{{ __('pagination.go_to_page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-site-muted dark:text-site-muted bg-site-surface border border-site-divider rounded-r-md leading-5 hover:text-site-muted focus:z-10 focus:outline-hidden focus:ring-3 ring-site-border dark:ring-site-border focus:border-site-focus active:bg-site-surface-muted active:text-site-muted dark:active:text-site-muted transition ease-in-out duration-150 dark:bg-site-surface-muted dark:border-site-divider dark:active:bg-site-surface-muted dark:focus:border-site-focus"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-site-text bg-site-surface border border-site-divider cursor-default rounded-r-md leading-5 dark:bg-site-surface-muted dark:border-site-divider"
                                aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
