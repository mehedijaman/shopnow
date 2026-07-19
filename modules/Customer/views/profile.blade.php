@extends('site-layout')

@section('seo_title', 'My Profile — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Manage Account</h3>
                <p class="mt-1 text-sm text-gray-600">Update your profile settings and secure your password.</p>
                
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('account.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.profile') ? 'bg-primary-50 text-primary-700 dark:bg-gray-800 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="ri-user-line mr-2 text-lg"></i> My Profile
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.addresses.*') ? 'bg-primary-50 text-primary-700 dark:bg-gray-800 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="ri-map-pin-line mr-2 text-lg"></i> My Addresses
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.orders') ? 'bg-primary-50 text-primary-700 dark:bg-gray-800 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="ri-shopping-bag-line mr-2 text-lg"></i> My Orders
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.downloads') ? 'bg-primary-50 text-primary-700 dark:bg-gray-800 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <i class="ri-download-line mr-2 text-lg"></i> My Downloads
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="ri-logout-box-r-line mr-2 text-lg"></i> Logout
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

                <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-gray-200 bg-white dark:bg-gray-800">
                    <div class="space-y-6 px-4 py-5 sm:p-6">
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-150 pb-2">Profile Information</h4>
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <input type="text" name="name" id="name" required value="{{ old('name', $customer->name) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" name="email" id="email" required value="{{ old('email', $customer->email) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                <input type="text" name="phone" id="phone" required value="{{ old('phone', $customer->phone) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('date_of_birth') border-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                                <select name="gender" id="gender"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('gender') border-red-500 @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $customer->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $customer->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h4 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-150 pt-6 pb-2">Change Password</h4>
                        <p class="text-xs text-gray-500">Leave password fields blank if you do not wish to change your password.</p>
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 text-right sm:px-6 border-t border-gray-250">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-xs hover:bg-primary-700 focus:outline-hidden focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-250">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
