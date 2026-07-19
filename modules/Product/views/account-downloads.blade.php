@extends('site-layout')

@section('seo_title', 'My Downloads — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Manage Account</h3>
                <p class="mt-1 text-sm text-gray-600">View your downloads and manage account settings.</p>
                
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('account.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="ri-user-line mr-2 text-lg"></i> My Profile
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="ri-shopping-bag-line mr-2 text-lg"></i> My Orders
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md bg-primary-50 text-primary-700 dark:bg-gray-800 dark:text-white">
                        <i class="ri-download-line mr-2 text-lg"></i> My Downloads
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="ri-logout-box-r-line mr-2 text-lg"></i> Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="mt-5 md:col-span-2 md:mt-0">
            <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-gray-200 bg-white dark:bg-gray-800 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-6 border-b border-gray-150 pb-2">
                    My Downloads
                </h2>

                @if ($permissions->isEmpty())
                    <p class="text-gray-500 py-6 text-center">You have no downloads yet.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">File</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Downloads</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Expires</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                                @foreach ($permissions as $p)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $p['product_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $p['file_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ $p['download_count'] }}{{ $p['download_limit'] ? ' / ' . $p['download_limit'] : '' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">{{ $p['expires_at'] ?? 'Never' }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if ($p['active'])
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Revoked</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            @if ($p['active'] && $p['download_url'])
                                                <a href="{{ $p['download_url'] }}" class="inline-flex items-center gap-1 rounded bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700 transition-all duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">Unavailable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
