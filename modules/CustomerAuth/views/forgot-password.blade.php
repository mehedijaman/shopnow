@extends('site-layout')

@section('seo_title', __('customer-auth::site.forgot.seo_title') . ' — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[75vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-site-surface-muted dark:bg-site-surface-muted">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-site-text dark:text-site-text">
            {{ __('customer-auth::site.forgot.heading') }}
        </h2>
        <p class="mt-2 text-center text-sm text-site-muted dark:text-site-muted">
            {{ __('customer-auth::site.forgot.body') }}
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-site-surface dark:bg-site-surface-muted py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 ring-1 ring-site-border dark:ring-site-border">
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/30 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-green-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('customerAuth.sendResetLinkEmail') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-site-text dark:text-site-text">
                        {{ __('customer-auth::site.forgot.email_address') }}
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full appearance-none rounded-xl border border-site-border dark:border-site-border bg-site-surface dark:bg-site-surface-muted px-4 py-2.5 text-site-text dark:text-site-text placeholder-site-muted shadow-sm focus:border-site-focus focus:outline-none focus:ring-1 focus:ring-site-focus sm:text-sm transition-colors @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="flex w-full justify-center rounded-xl border border-transparent bg-site-primary py-2.5 px-4 text-sm font-bold text-site-on-primary shadow-md hover:bg-site-primary-hover hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-site-focus focus:ring-offset-2 transition-all duration-300">
                        {{ __('customer-auth::site.forgot.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
