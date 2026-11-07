@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('seo_title', __('customer-auth::site.login.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[75vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-site-surface-muted dark:bg-site-surface-muted">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-site-text dark:text-site-text">
            {{ __('customer-auth::site.login.heading') }}
        </h2>
        <p class="mt-2 text-center text-sm text-site-muted dark:text-site-muted">
            {{ __('customer-auth::site.login.or') }}
            <a href="{{ route('customerAuth.signupForm') }}" class="font-semibold text-site-link transition-colors hover:text-site-link dark:text-site-link">
                {{ __('customer-auth::site.login.create_account') }}
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-site-surface dark:bg-site-surface-muted py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 ring-1 ring-site-border dark:ring-site-border">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/30 p-4 border border-green-200">
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

            <form class="space-y-6" action="{{ route('customerAuth.login') }}" method="POST">
                @csrf

                <div>
                    <label for="login_identity" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.login.identity_label') }}
                    </label>
                    <div class="mt-1">
                        <input id="login_identity" name="login_identity" type="text" required value="{{ old('login_identity') }}"
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('login_identity') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('login_identity')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.login.password') }}
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-site-border dark:border-site-border text-site-link focus:ring-site-focus transition-colors">
                        <label for="remember" class="ml-2 block text-sm font-medium text-site-text dark:text-site-text">
                            {{ __('customer-auth::site.login.remember_me') }}
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('customerAuth.forgotPassword') }}" class="font-semibold text-site-link hover:text-site-link dark:text-site-link transition-colors">
                            {{ __('customer-auth::site.login.forgot_password') }}
                        </a>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="flex w-full justify-center rounded-xl border border-transparent bg-site-primary py-2.5 px-4 text-sm font-bold text-site-on-primary shadow-md hover:bg-site-primary-hover hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-site-focus focus:ring-offset-2 transition-all duration-300">
                        {{ __('customer-auth::site.login.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
