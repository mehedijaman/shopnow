<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerController;

Route::get('customer', [
    CustomerController::class, 'index',
])->name('customer.index');

Route::get('customer/create', [
    CustomerController::class, 'create',
])->name('customer.create');

Route::post('customer', [
    CustomerController::class, 'store',
])->name('customer.store');

Route::get('customer/{id}/edit', [
    CustomerController::class, 'edit',
])->name('customer.edit');

Route::put('customer/{id}', [
    CustomerController::class, 'update',
])->name('customer.update');

Route::delete('customer/{id}', [
    CustomerController::class, 'destroy',
])->name('customer.destroy');
