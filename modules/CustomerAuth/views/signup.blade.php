@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('seo_title', __('customer-auth::site.signup.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[75vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-site-surface-muted dark:bg-site-surface-muted">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-site-text dark:text-site-text">
            {{ __('customer-auth::site.signup.heading') }}
        </h2>
        <p class="mt-2 text-center text-sm text-site-muted dark:text-site-muted">
            {{ __('customer-auth::site.signup.or') }}
            <a href="{{ route('customerAuth.loginForm') }}" class="font-semibold text-site-link transition-colors hover:text-site-link dark:text-site-link">
                {{ __('customer-auth::site.signup.signin_link') }}
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-site-surface dark:bg-site-surface-muted py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 ring-1 ring-site-border dark:ring-site-border">
            <form class="space-y-6" action="{{ route('customerAuth.signup') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.signup.full_name') }}
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('name') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.signup.email_address') }}
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.signup.phone_label') }}
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}"
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('phone') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.signup.password') }} <span class="text-xs font-normal text-site-muted dark:text-site-muted ml-1">{{ __('customer-auth::site.signup.min_8') }}</span>
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.signup.confirm_password') }}
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('confirm_password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('confirm_password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="flex w-full justify-center rounded-xl border border-transparent bg-site-primary py-2.5 px-4 text-sm font-bold text-site-on-primary shadow-md hover:bg-site-primary-hover hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-site-focus focus:ring-offset-2 transition-all duration-300">
                        {{ __('customer-auth::site.signup.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
