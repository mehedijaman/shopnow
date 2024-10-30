<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;

/** Product Category Routes */
Route::prefix('product-category')->name('productCategory.')->group(function () {

    // Editor Image Upload
    Route::post('upload-editor-image', [ProductCategoryController::class, 'uploadEditorImage'])
        ->name('uploadEditorImage')
        ->can('product-category-create')
        ->can('product-category-edit');

    // Listing and Creation
    Route::get('/', [ProductCategoryController::class, 'index'])
        ->name('index')
        ->can('product-category-list');

    Route::get('create', [ProductCategoryController::class, 'create'])
        ->name('create')
        ->can('product-category-create');

    Route::post('/', [ProductCategoryController::class, 'store'])
        ->name('store')
        ->can('product-category-create');

    // Editing and Updating
    Route::get('{id}/edit', [ProductCategoryController::class, 'edit'])
        ->name('edit')
        ->can('product-category-edit');

    Route::put('{id}', [ProductCategoryController::class, 'update'])
        ->name('update')
        ->can('product-category-edit');

    // Deleting
    Route::delete('{id}', [ProductCategoryController::class, 'destroy'])
        ->name('destroy')
        ->can('product-category-delete');

    /** Product Category Recycle Bin Routes */
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {

        // Recycle Bin Listing
        Route::get('/', [ProductCategoryController::class, 'recycleBin'])
            ->name('index')
            ->can('product-category-recycle-bin-list');

        // Restoring
        Route::get('{id}/restore', [ProductCategoryController::class, 'restore'])
            ->name('restore')
            ->can('product-category-recycle-bin-restore');

        Route::get('restore', [ProductCategoryController::class, 'restoreRecycleBin'])
            ->name('restoreAll')
            ->can('product-category-recycle-bin-restore');

        // Permanent Deletion
        Route::delete('{id}/destroy', [ProductCategoryController::class, 'destroyForce'])
            ->name('destroyForce')
            ->can('product-category-recycle-bin-delete');

        Route::delete('empty', [ProductCategoryController::class, 'emptyRecycleBin'])
            ->name('empty')
            ->can('product-category-recycle-bin-delete');
    });
});
