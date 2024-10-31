<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductTagController;
use Modules\Product\Http\Controllers\ProductBrandController;
use Modules\Product\Http\Controllers\ProductCategoryController;

/** Product Category routes **/
Route::prefix('product-category')->name('productCategory.')->middleware('can:product-category')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [ProductCategoryController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [ProductCategoryController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [ProductCategoryController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [ProductCategoryController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    // Editor Image Upload
    Route::post('upload-editor-image', [ProductCategoryController::class, 'uploadEditorImage'])->name('uploadEditorImage')->can('create')->can('edit');

    Route::get('/', [ProductCategoryController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductCategoryController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductCategoryController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [ProductCategoryController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [ProductCategoryController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [ProductCategoryController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [ProductCategoryController::class, 'destroy'])->name('destroy')->can('delete');
});

/** Product Brand routes **/
Route::prefix('product-brand')->name('productBrand.')->middleware('can:product-brand')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [ProductBrandController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [ProductBrandController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [ProductBrandController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [ProductBrandController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [ProductBrandController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    // Editor Image Upload
    Route::post('upload-editor-image', [ProductBrandController::class, 'uploadEditorImage'])->name('uploadEditorImage')->can('create')->can('edit');

    Route::get('/', [ProductBrandController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductBrandController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductBrandController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [ProductBrandController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [ProductBrandController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [ProductBrandController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [ProductBrandController::class, 'destroy'])->name('destroy')->can('delete');
});


/** Product Tag routes **/
Route::prefix('product-tag')->name('productTag.')->middleware('can:product-tag')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [ProductTagController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [ProductTagController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [ProductTagController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [ProductTagController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [ProductTagController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    Route::get('/', [ProductTagController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductTagController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductTagController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [ProductTagController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [ProductTagController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [ProductTagController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [ProductTagController::class, 'destroy'])->name('destroy')->can('delete');
});


/** Product routes **/
Route::prefix('product')->name('product.')->middleware('can:product')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [ProductController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [ProductController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [ProductController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [ProductController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [ProductController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    // Editor Image Upload
    Route::post('upload-editor-image', [ProductController::class, 'uploadEditorImage'])->name('uploadEditorImage')->can('create')->can('edit');

    Route::get('/', [ProductController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [ProductController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [ProductController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [ProductController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [ProductController::class, 'destroy'])->name('destroy')->can('delete');
});
