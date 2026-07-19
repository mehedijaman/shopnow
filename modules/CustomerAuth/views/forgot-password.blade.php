@extends('site-layout')

@section('seo_title', 'Forgot Password — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[70vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Reset your password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Enter your email address and we'll send you a recovery link.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-sm sm:rounded-lg sm:px-10 border border-gray-150 dark:border-gray-700">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 p-4 border border-green-200">
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
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-xs hover:bg-primary-700 focus:outline-hidden focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-250">
                        Send reset link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
