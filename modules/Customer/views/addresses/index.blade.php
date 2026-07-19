@extends('site-layout')

@section('seo_title', 'My Addresses — ' . setting('branding.site_name', config('app.name')))

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

            <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-gray-200 bg-white dark:bg-gray-800">
                <div class="space-y-6 px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between border-b border-gray-150 pb-2">
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">My Address Book</h4>
                        <a href="{{ route('account.addresses.create') }}" class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-1.5 px-3 text-sm font-medium text-white shadow-xs hover:bg-primary-700 transition-all duration-250">
                            Add New Address
                        </a>
                    </div>
                    
                    @if($addresses->isEmpty())
                        <div class="text-center py-12">
                            <i class="ri-map-pin-line text-4xl text-gray-400"></i>
                            <p class="mt-2 text-sm text-gray-500">You don't have any addresses saved yet.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($addresses as $address)
                                <div class="relative flex flex-col justify-between rounded-lg border {{ $address->default ? 'border-primary-500 ring-2 ring-primary-100 dark:ring-primary-900/20' : 'border-gray-250 dark:border-gray-700' }} bg-white dark:bg-gray-850 p-4 shadow-xs">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            @if($address->default)
                                                <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-semibold text-primary-800 dark:bg-primary-900/30 dark:text-primary-300">
                                                    Default Address
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">Saved Address</span>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-sm text-gray-900 dark:text-white">
                                            <p class="font-medium">{{ $address->address }}</p>
                                            <p class="mt-1 text-gray-500 dark:text-gray-400">
                                                {{ $address->union?->name ? $address->union->name . ', ' : '' }}
                                                {{ $address->upazila?->name ? $address->upazila->name . ', ' : '' }}
                                                {{ $address->district?->name ? $address->district->name . ', ' : '' }}
                                                {{ $address->division?->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-3">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('account.addresses.edit', $address->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                                Edit
                                            </a>
                                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                        @if(!$address->default)
                                            <form action="{{ route('account.addresses.default', $address->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                                                    Set as Default
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
