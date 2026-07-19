<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerAddressController;
use Modules\Customer\Http\Controllers\CustomerProfileController;
use Modules\Customer\Http\Controllers\GeocodeController;

Route::get('geocode/divisions', [GeocodeController::class, 'divisions'])->name('geocode.divisions');
Route::get('geocode/districts', [GeocodeController::class, 'districts'])->name('geocode.districts');
Route::get('geocode/upazilas', [GeocodeController::class, 'upazilas'])->name('geocode.upazilas');
Route::get('geocode/unions', [GeocodeController::class, 'unions'])->name('geocode.unions');

Route::middleware(['auth.customer'])->group(function () {
    Route::get('account/profile', [CustomerProfileController::class, 'show'])->name('account.profile');
    Route::put('account/profile', [CustomerProfileController::class, 'update'])->name('account.profile.update');

    Route::get('account/addresses', [CustomerAddressController::class, 'index'])->name('account.addresses.index');
    Route::get('account/addresses/create', [CustomerAddressController::class, 'create'])->name('account.addresses.create');
    Route::post('account/addresses', [CustomerAddressController::class, 'store'])->name('account.addresses.store');
    Route::get('account/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])->name('account.addresses.edit');
    Route::put('account/addresses/{address}', [CustomerAddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('account/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('account/addresses/{address}/default', [CustomerAddressController::class, 'makeDefault'])->name('account.addresses.default');
});
