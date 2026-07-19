@extends('site-layout')

@section('seo_title', 'Customer Login — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[70vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Or
            <a href="{{ route('customerAuth.signupForm') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                create a new customer account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-sm sm:rounded-lg sm:px-10 border border-gray-150 dark:border-gray-700">
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

            <form class="space-y-6" action="{{ route('customerAuth.login') }}" method="POST">
                @csrf

                <div>
                    <label for="login_identity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email address or Phone number
                    </label>
                    <div class="mt-1">
                        <input id="login_identity" name="login_identity" type="text" required value="{{ old('login_identity') }}"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('login_identity') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('login_identity')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded-sm border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('customerAuth.forgotPassword') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-xs hover:bg-primary-700 focus:outline-hidden focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-250">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
