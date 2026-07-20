@extends('site-layout')

@section('seo_title', 'Register Account — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[75vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Or
            <a href="{{ route('customerAuth.loginForm') }}" class="font-semibold text-primary-600 transition-colors hover:text-primary-500 dark:text-primary-400">
                sign in to your existing account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 ring-1 ring-gray-200 dark:ring-gray-700">
            <form class="space-y-6" action="{{ route('customerAuth.signup') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                            class="block w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm transition-colors @error('name') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm transition-colors @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Phone Number (11 digits, e.g. 01712345678)
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}"
                            class="block w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm transition-colors @error('phone') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Password <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-1">(Min 8 chars)</span>
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm transition-colors @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm transition-colors @error('confirm_password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('confirm_password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="flex w-full justify-center rounded-xl border border-transparent bg-primary-600 py-2.5 px-4 text-sm font-bold text-white shadow-md hover:bg-primary-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-300">
                        Register Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
