@extends('site-layout')

@section('seo_title', 'Register Account — ' . setting('branding.site_name', config('app.name')))

@section('content')
<div class="flex min-h-[70vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
            Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            Or
            <a href="{{ route('customerAuth.loginForm') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                sign in to your existing account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-sm sm:rounded-lg sm:px-10 border border-gray-150 dark:border-gray-700">
            <form class="space-y-6" action="{{ route('customerAuth.signup') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('name') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone Number (11 digits, e.g. 01712345678)
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('phone') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Date of Birth
                    </label>
                    <div class="mt-1">
                        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('date_of_birth') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('date_of_birth')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Gender
                    </label>
                    <div class="mt-1">
                        <select id="gender" name="gender"
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('gender') border-red-500 ring-1 ring-red-500 @enderror">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    @error('gender')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password (Min 8 characters)
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                            class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('confirm_password') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('confirm_password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-xs hover:bg-primary-700 focus:outline-hidden focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-250">
                        Register Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
