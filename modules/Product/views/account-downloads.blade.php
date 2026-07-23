@extends('site-layout')

@section('seo_title', 'My Downloads — ' . setting('branding.site_name', config('app.name')))

@section('robots', 'noindex, follow')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Manage Account</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">View your downloads and account details.</p>
                
                <nav class="mt-6 space-y-1.5">
                    <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="ri-user-line text-lg text-slate-400"></i> My Profile
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="ri-shopping-bag-line text-lg text-slate-400"></i> My Orders
                    </a>
                    <a href="{{ route('account.downloads') }}" class="flex items-center gap-2.5 rounded-xl bg-primary-50 px-3.5 py-2.5 text-sm font-bold text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-950/50 dark:text-primary-300">
                        <i class="ri-download-line text-lg text-primary-600 dark:text-primary-400"></i> My Downloads
                    </a>
                    <a href="{{ route('customerAuth.logout') }}" class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                        <i class="ri-logout-box-r-line text-lg"></i> Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="mt-6 md:col-span-2 md:mt-0">
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white sm:text-2xl">
                            My Downloads
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Access and download your digital products.</p>
                    </div>
                </div>

                @if ($permissions->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                            <i class="ri-download-cloud-line text-3xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">No downloads available</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">You haven't purchased any downloadable digital products yet.</p>
                        <a href="{{ route('shop.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary-700">
                            <i class="ri-store-2-line"></i> Browse Shop
                        </a>
                    </div>
                @else
                    {{-- Mobile Card List (hidden on md+) --}}
                    <div class="space-y-4 md:hidden">
                        @foreach ($permissions as $p)
                            <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $p['product_name'] }}</h4>
                                        <p class="mt-0.5 text-xs font-medium text-slate-500 dark:text-slate-400">{{ $p['file_name'] }}</p>
                                    </div>
                                    @if ($p['active'])
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-400">Active</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-950/50 dark:text-rose-400">Revoked</span>
                                    @endif
                                </div>

                                <div class="mt-3 flex items-center justify-between border-t border-slate-200/60 pt-3 text-xs text-slate-500 dark:border-slate-700/60 dark:text-slate-400">
                                    <span>Downloads: {{ $p['download_count'] }}{{ $p['download_limit'] ? ' / ' . $p['download_limit'] : '' }}</span>
                                    <span>Expires: {{ $p['expires_at'] ?? 'Never' }}</span>
                                </div>

                                <div class="mt-3">
                                    @if ($p['active'] && $p['download_url'])
                                        <a href="{{ $p['download_url'] }}" class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary-600 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary-700">
                                            <i class="ri-download-line text-sm"></i> Download File
                                        </a>
                                    @else
                                        <span class="block w-full text-center text-xs text-slate-400">Unavailable</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View (hidden on mobile) --}}
                    <div class="hidden overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 md:block">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                            <thead class="bg-slate-50 dark:bg-slate-900">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">File</th>
                                    <th class="px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Downloads</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Expires</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                    <th class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                @foreach ($permissions as $p)
                                    <tr class="transition-colors hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $p['product_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $p['file_name'] }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium text-slate-600 dark:text-slate-300">
                                            {{ $p['download_count'] }}{{ $p['download_limit'] ? ' / ' . $p['download_limit'] : '' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $p['expires_at'] ?? 'Never' }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            @if ($p['active'])
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-400">Active</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-950/50 dark:text-rose-400">Revoked</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            @if ($p['active'] && $p['download_url'])
                                                <a href="{{ $p['download_url'] }}" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    <i class="ri-download-line text-sm"></i>
                                                    <span>Download</span>
                                                </a>
                                            @else
                                                <span class="text-xs font-medium text-slate-400">Unavailable</span>
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
