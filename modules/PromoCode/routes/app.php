<?php

use Illuminate\Support\Facades\Route;
use Modules\PromoCode\Http\Controllers\PromoCodeController;

/** promoCode Routes */
Route::prefix('promo-codes')->name('promoCode.')->controller(PromoCodeController::class)->group(function () {
    Route::get('/', 'index')
        ->name('index')
        ->can('promo-code-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('promo-code-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('promo-code-create');

    Route::get('/{id}/edit', 'edit')
        ->name('edit')
        ->can('promo-code-edit');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('promo-code-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('promo-code-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('promo-code-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('promo-code-recycle-bin-restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('promo-code-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('promo-code-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('promo-code-recycle-bin-restore');
    });
});
