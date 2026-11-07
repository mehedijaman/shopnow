@extends('site-layout')

@section('seo_title', __('customer::site.addresses.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-site-text dark:text-site-text"{{ __('site.account.manage_heading') }}</h3>
                <p class="mt-1 text-sm text-site-muted dark:text-site-text"{{ __('site.account.manage_hint_profile') }}</p>
                
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('account.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.profile') ? 'bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted' }}">
                        <i class="ri-user-line mr-2 text-lg"></i> {{ __('site.account.my_profile') }}
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.addresses.*') ? 'bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted' }}">
                        <i class="ri-map-pin-line mr-2 text-lg"></i> {{ __('site.account.my_addresses') }}
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.orders') ? 'bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted' }}">
                        <i class="ri-shopping-bag-line mr-2 text-lg"></i> {{ __('site.account.my_orders') }}
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.downloads') ? 'bg-site-primary-soft text-site-link dark:bg-site-surface-muted dark:text-site-text' : 'text-site-muted hover:bg-site-surface-muted hover:text-site-text dark:text-site-text dark:hover:bg-site-surface-muted' }}">
                        <i class="ri-download-line mr-2 text-lg"></i> {{ __('site.account.my_downloads') }}
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="ri-logout-box-r-line mr-2 text-lg"></i> {{ __('site.account.logout') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Form Area -->
        <div class="mt-5 md:col-span-2 md:mt-0">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-green-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted">
                <div class="space-y-6 px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between border-b border-site-divider dark:border-site-divider pb-2">
                        <h4 class="text-base font-semibold text-site-text dark:text-site-text">{{ __('customer::site.addresses.heading') }}</h4>
                        <a href="{{ route('account.addresses.create') }}" class="inline-flex justify-center rounded-md border border-transparent bg-site-primary py-1.5 px-3 text-sm font-medium text-site-on-primary shadow-xs hover:bg-site-primary-hover transition-all duration-250">
                            {{ __('customer::site.addresses.add_new') }}
                        </a>
                    </div>
                    
                    @if($addresses->isEmpty())
                        <div class="text-center py-12">
                            <i class="ri-map-pin-line text-4xl text-site-muted"></i>
                            <p class="mt-2 text-sm text-site-muted dark:text-site-muted">{{ __('customer::site.addresses.empty') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($addresses as $address)
                                <div class="relative flex flex-col justify-between rounded-lg border {{ $address->default ? 'border-site-focus ring-2 ring-site-focus dark:ring-site-focus/20' : 'border-site-divider dark:border-site-divider' }} bg-site-surface dark:bg-site-surface-muted p-4 shadow-xs">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            @if($address->default)
                                                <span class="inline-flex items-center rounded-full bg-site-primary-soft px-2.5 py-0.5 text-xs font-semibold text-site-link dark:bg-site-primary/30 dark:text-site-link">
                                                    {{ __('customer::site.addresses.default_address') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-site-muted">{{ __('customer::site.addresses.saved_address') }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-sm text-site-text dark:text-site-text">
                                            <p class="font-medium">{{ $address->address }}</p>
                                            <p class="mt-1 text-site-muted dark:text-site-muted">
                                                {{ $address->union?->name ? $address->union->name . ', ' : '' }}
                                                {{ $address->upazila?->name ? $address->upazila->name . ', ' : '' }}
                                                {{ $address->district?->name ? $address->district->name . ', ' : '' }}
                                                {{ $address->division?->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between border-t border-site-divider dark:border-site-divider pt-3">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('account.addresses.edit', $address->id) }}" class="text-sm font-medium text-site-link hover:text-site-link dark:text-site-link">
                                                {{ __('customer::site.addresses.edit') }}
                                            </a>
                                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('{{ __('customer::site.addresses.confirm_delete') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">
                                                    {{ __('customer::site.addresses.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                        @if(!$address->default)
                                            <form action="{{ route('account.addresses.default', $address->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs font-medium text-site-muted hover:text-site-text dark:text-site-muted dark:hover:text-site-text">
                                                    {{ __('customer::site.addresses.set_default') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
