<?php

use Illuminate\Support\Facades\Route;
use Modules\Slider\Http\Controllers\SliderController;

/** slider Routes */
Route::prefix('slider')->name('slider.')->controller(SliderController::class)->group(function () {
    Route::get('/', 'index')
        ->name('index')
        ->can('slider-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('slider-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('slider-create');

    Route::get('/{id}/edit', 'edit')
        ->name('edit')
        ->can('slider-edit');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('slider-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('slider-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('slider-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('slider-recycle-Bin-Restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('slider-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('slider-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('slider-recycle-Bin-Restore');
    });
});
