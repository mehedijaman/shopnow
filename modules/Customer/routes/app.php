<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerController;

/** Customer routes **/
Route::prefix('customer')->name('customer.')->middleware('can:customer')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [CustomerController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [CustomerController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [CustomerController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [CustomerController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [CustomerController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    Route::get('/', [CustomerController::class, 'index'])->name('index')->can('list');
    Route::get('create', [CustomerController::class, 'create'])->name('create')->can('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [CustomerController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [CustomerController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [CustomerController::class, 'destroy'])->name('destroy')->can('delete');
});
