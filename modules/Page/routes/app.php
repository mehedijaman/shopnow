<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;

/** page Routes */
Route::prefix('page')->name('page.')->controller(PageController::class)->group(function () {
    Route::post('upload-editor-image', 'uploadEditorImage')
        ->name('uploadEditorImage')
        ->can('page-create')
        ->can('page-edit');

    Route::get('/', 'index')
        ->name('index')
        ->can('page-list');

    Route::get('/create', 'create')
        ->name('create')
        ->can('page-create');

    Route::post('/', 'store')
        ->name('store')
        ->can('page-create');

    Route::get('/{id}/edit', 'edit')
        ->name('edit')
        ->can('page-edit');

    Route::put('/{id}', 'update')
        ->name('update')
        ->can('page-edit');

    Route::delete('/{id}', 'destroy')
        ->name('destroy')
        ->can('page-delete');

    /** Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', 'recycleBin')
            ->name('index')
            ->can('page-recycle-bin-list');

        Route::get('/{id}/restore', 'restore')
            ->name('restore')
            ->can('page-recycle-Bin-Restore');

        Route::delete('/{id}/destroy', 'destroyForce')
            ->name('destroyForce')
            ->can('page-recycle-bin-delete');

        Route::delete('/empty', 'emptyRecycleBin')
            ->name('empty')
            ->can('page-recycle-bin-delete');

        Route::get('/restore', 'restoreRecycleBin')
            ->name('restoreAll')
            ->can('page-recycle-Bin-Restore');
    });
});
