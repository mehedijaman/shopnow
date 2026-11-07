@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('seo_title', __('product::site.downloads.title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-site-text dark:text-site-text">{{ __('site.account.manage_heading') }}</h3>
                <p class="mt-1 text-sm text-site-muted dark:text-site-text">{{ __('site.account.manage_hint_downloads') }}</p>
                
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('account.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted">
                        <i class="ri-user-line mr-2 text-lg"></i> {{ __('site.account.my_profile') }}
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted">
                        <i class="ri-shopping-bag-line mr-2 text-lg"></i> {{ __('site.account.my_orders') }}
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text">
                        <i class="ri-download-line mr-2 text-lg"></i> {{ __('site.account.my_downloads') }}
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="ri-logout-box-r-line mr-2 text-lg"></i> {{ __('site.account.logout') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="mt-5 md:col-span-2 md:mt-0">
            <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted p-6">
                <h2 class="text-xl font-semibold text-site-text dark:text-site-text sm:text-2xl mb-6 border-b border-site-divider dark:border-site-divider pb-2">
                    {{ __('product::site.downloads.title') }}
                </h2>

                @if ($permissions->isEmpty())
                    <p class="text-site-muted dark:text-site-muted py-6 text-center">{{ __('product::site.downloads.empty') }}</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-site-divider dark:border-site-divider">
                        <table class="min-w-full divide-y divide-site-divider dark:divide-site-divider">
                            <thead class="bg-site-surface-muted dark:bg-site-surface-muted">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_product') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_file') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_downloads') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_expires') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_status') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-site-muted dark:text-site-muted">{{ __('product::site.downloads.col_action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-site-divider dark:divide-site-divider bg-site-surface dark:bg-site-surface-muted">
                                @foreach ($permissions as $p)
                                    <tr class="hover:bg-site-surface-muted dark:hover:bg-site-surface-muted/50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-site-text dark:text-site-text">{{ $p['product_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-site-muted dark:text-site-text">{{ $p['file_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-site-muted dark:text-site-text">
                                            {{ $p['download_count'] }}{{ $p['download_limit'] ? ' / ' . $p['download_limit'] : '' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-site-muted dark:text-site-text">{{ $p['expires_at'] ?? __('product::site.downloads.never') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if ($p['active'])
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">{{ __('product::site.downloads.active') }}</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">{{ __('product::site.downloads.revoked') }}</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            @if ($p['active'] && $p['download_url'])
                                                <a href="{{ $p['download_url'] }}" class="inline-flex items-center gap-1 rounded bg-site-primary px-3 py-1.5 text-xs font-semibold text-site-on-primary hover:bg-site-primary-hover transition-all duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('product::site.downloads.download') }}
                                                </a>
                                            @else
                                                <span class="text-xs text-site-muted">{{ __('product::site.downloads.unavailable') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
