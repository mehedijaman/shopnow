@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('seo_title', __('customer::site.profile.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

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

            <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted">
                    <div class="space-y-6 px-4 py-5 sm:p-6">
                        <h4 class="text-base font-semibold text-site-text dark:text-site-text border-b border-site-divider dark:border-site-divider pb-2">{{ __('customer::site.profile.heading') }}</h4>
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.full_name') }}</label>
                                <input type="text" name="name" id="name" required value="{{ old('name', $customer->name) }}"
                                    class="mt-1 block w-full rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.email_address') }}</label>
                                <input type="email" name="email" id="email" required value="{{ old('email', $customer->email) }}"
                                    class="mt-1 block w-full rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="phone" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.phone_number') }}</label>
                                <input type="text" name="phone" id="phone" required value="{{ old('phone', $customer->phone) }}"
                                    class="mt-1 block w-full rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="date_of_birth" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.date_of_birth') }}</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border border-site-divider dark:border-site-divider bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('date_of_birth') border-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="gender" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.gender') }}</label>
                                <select name="gender" id="gender"
                                    class="mt-1 block w-full rounded-md border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('gender') border-red-500 @enderror">
                                    <option value="">{{ __('customer::site.profile.select_gender') }}</option>
                                    <option value="male" {{ old('gender', $customer->gender) === 'male' ? 'selected' : '' }}>{{ __('customer::site.profile.male') }}</option>
                                    <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>{{ __('customer::site.profile.female') }}</option>
                                    <option value="other" {{ old('gender', $customer->gender) === 'other' ? 'selected' : '' }}>{{ __('customer::site.profile.other') }}</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h4 class="text-base font-semibold text-site-text dark:text-site-text border-b border-site-divider dark:border-site-divider pt-6 pb-2">{{ __('customer::site.profile.change_password') }}</h4>
                        <p class="text-xs text-site-muted dark:text-site-muted">{{ __('customer::site.profile.change_password_hint') }}</p>
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="password" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.new_password') }}</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full rounded-md border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-site-text dark:text-site-text">{{ __('customer::site.profile.confirm_new_password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full rounded-md border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-3 py-2 text-site-text dark:text-site-text shadow-xs focus:border-site-focus focus:outline-hidden focus:ring-site-focus sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-site-surface-muted dark:bg-site-surface-muted px-4 py-3 text-right sm:px-6 border-t border-site-divider dark:border-site-divider">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-site-primary py-2 px-4 text-sm font-medium text-site-on-primary shadow-xs hover:bg-site-primary-hover focus:outline-hidden focus:ring-2 focus:ring-site-focus focus:ring-offset-2 transition-all duration-250">
                            {{ __('customer::site.profile.save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
