@extends('site-layout')

@section('seo_title', 'Edit Address — ' . setting('branding.site_name', config('app.name')))

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
            <form action="{{ route('account.addresses.update', $address->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="shadow-sm sm:overflow-hidden sm:rounded-md border border-gray-200 bg-white dark:bg-gray-800">
                    <div class="space-y-6 px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between border-b border-gray-150 pb-2">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">Edit Address</h4>
                            <a href="{{ route('account.addresses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
                                Back to list
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-6 gap-6">

                            {{-- District (required) — all districts loaded, current pre-selected --}}
                            <div class="col-span-6">
                                <label for="district_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">District <span class="text-red-500">*</span></label>
                                <select id="district_id" name="district_id" required
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('district_id') border-red-500 @enderror">
                                    <option value="">Select District</option>
                                    @foreach(\Devfaysal\BangladeshGeocode\Models\District::orderBy('name')->get() as $dist)
                                        <option value="{{ $dist->id }}" {{ old('district_id', $address->district_id) == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upazila (optional) --}}
                            <div class="col-span-6 sm:col-span-3">
                                <label for="upazilla_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Upazila / Thana
                                    <span class="text-xs text-gray-400">(Optional)</span>
                                </label>
                                <select id="upazilla_id" name="upazilla_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Upazila</option>
                                    @if($address->district_id)
                                        @foreach(\Devfaysal\BangladeshGeocode\Models\Upazila::where('district_id', old('district_id', $address->district_id))->orderBy('name')->get() as $upz)
                                            <option value="{{ $upz->id }}" {{ old('upazilla_id', $address->upazilla_id) == $upz->id ? 'selected' : '' }}>{{ $upz->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- Union (optional) --}}
                            <div class="col-span-6 sm:col-span-3">
                                <label for="union_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Union
                                    <span class="text-xs text-gray-400">(Optional)</span>
                                </label>
                                <select id="union_id" name="union_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Union</option>
                                    @if($address->upazilla_id)
                                        @foreach(\Devfaysal\BangladeshGeocode\Models\Union::where('upazila_id', old('upazilla_id', $address->upazilla_id))->orderBy('name')->get() as $un)
                                            <option value="{{ $un->id }}" {{ old('union_id', $address->union_id) == $un->id ? 'selected' : '' }}>{{ $un->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- Street Address --}}
                            <div class="col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street Address <span class="text-red-500">*</span></label>
                                <input type="text" name="address" id="address" required value="{{ old('address', $address->address) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white shadow-xs focus:border-primary-500 focus:outline-hidden focus:ring-primary-500 sm:text-sm @error('address') border-red-500 @enderror"
                                    placeholder="House number, street name, etc.">
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="default" name="default" type="checkbox" value="1" {{ old('default', $address->default) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded-sm border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="default" class="font-medium text-gray-700 dark:text-gray-300">Set as default shipping address</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 text-right sm:px-6 border-t border-gray-250">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-xs hover:bg-primary-700 focus:outline-hidden focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-250">
                            Update Address
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const districtSelect = document.getElementById('district_id');
    const upazilaSelect = document.getElementById('upazilla_id');
    const unionSelect = document.getElementById('union_id');

    // When district changes, reload upazilas
    districtSelect.addEventListener('change', function () {
        upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
        unionSelect.innerHTML = '<option value="">Select Union</option>';

        if (!this.value) return;

        fetch(`/geocode/upazilas?district_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name;
                    upazilaSelect.appendChild(opt);
                });
            });
    });

    // When upazila changes, reload unions
    upazilaSelect.addEventListener('change', function () {
        unionSelect.innerHTML = '<option value="">Select Union</option>';

        if (!this.value) return;

        fetch(`/geocode/unions?upazila_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name;
                    unionSelect.appendChild(opt);
                });
            });
    });
});
</script>
@endsection
