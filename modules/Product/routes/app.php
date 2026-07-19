<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductAttributeController;
use Modules\Product\Http\Controllers\ProductBrandController;
use Modules\Product\Http\Controllers\ProductBundleController;
use Modules\Product\Http\Controllers\ProductCategoryController;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductFileController;
use Modules\Product\Http\Controllers\ProductPermissionController;
use Modules\Product\Http\Controllers\ProductReportController;
use Modules\Product\Http\Controllers\ProductTagController;
use Modules\Product\Http\Controllers\ProductVariationController;

Route::get('product/report', [ProductReportController::class, 'index'])->name('product.report');

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
    Route::post('reorder', [ProductCategoryController::class, 'reorder'])->name('reorder')->can('edit');
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

/** Product Attribute routes (admin CRUD + inline AJAX) **/
Route::prefix('product-attribute')->name('productAttribute.')->middleware('can:product')->group(function () {
    Route::get('/', [ProductAttributeController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductAttributeController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductAttributeController::class, 'store'])->name('store')->can('create');
    Route::get('{productAttribute}/edit', [ProductAttributeController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{productAttribute}', [ProductAttributeController::class, 'update'])->name('update')->can('edit');
    Route::delete('{productAttribute}', [ProductAttributeController::class, 'destroy'])->name('destroy')->can('delete');

    // AJAX endpoint for inline creation from the product form
    Route::post('ajax', [ProductAttributeController::class, 'storeAjax'])->name('storeAjax')->can('create');
    // AJAX endpoint to fetch all attributes (for refreshing after inline creation)
    Route::get('ajax/all', [ProductAttributeController::class, 'indexAjax'])->name('indexAjax')->can('list');
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

    // Gallery
    Route::delete('{id}/gallery/{mediaId}', [ProductController::class, 'destroyGalleryImage'])->name('gallery.destroy')->can('edit');

    // Downloadable files
    Route::post('{product}/downloads', [ProductFileController::class, 'store'])->name('downloads.store')->can('edit');
    Route::delete('{product}/downloads/{file}', [ProductFileController::class, 'destroy'])->name('downloads.destroy')->can('edit');

    // Download Permission (reachable from order show)
    Route::patch('download-permission/{id}/toggle', [ProductPermissionController::class, 'toggle'])->name('downloadPermission.toggle')->can('edit');

    // Variations
    Route::get('{product}/variations', [ProductVariationController::class, 'attributes'])->name('variations.attributes')->can('edit');
    Route::post('{product}/variations/generate', [ProductVariationController::class, 'generate'])->name('variations.generate')->can('edit');
    Route::put('{product}/variations/{variation}', [ProductVariationController::class, 'update'])->name('variations.update')->can('edit');
    Route::delete('{product}/variations/{variation}', [ProductVariationController::class, 'destroy'])->name('variations.destroy')->can('edit');

    // Bundle
    Route::get('{product}/bundle', [ProductBundleController::class, 'config'])->name('bundle.config')->can('edit');
    Route::post('{product}/bundle/config', [ProductBundleController::class, 'saveConfig'])->name('bundle.saveConfig')->can('edit');
    Route::post('{product}/bundle/items', [ProductBundleController::class, 'addItem'])->name('bundle.items.add')->can('edit');
    Route::put('{product}/bundle/items/{item}', [ProductBundleController::class, 'updateItem'])->name('bundle.items.update')->can('edit');
    Route::delete('{product}/bundle/items/{item}', [ProductBundleController::class, 'removeItem'])->name('bundle.items.remove')->can('edit');
    Route::post('{product}/bundle/reorder', [ProductBundleController::class, 'reorder'])->name('bundle.reorder')->can('edit');

    Route::get('/', [ProductController::class, 'index'])->name('index')->can('list');
    Route::get('create', [ProductController::class, 'create'])->name('create')->can('create');
    Route::post('/', [ProductController::class, 'store'])->name('store')->can('create');
    Route::get('{id}', [ProductController::class, 'show'])->name('show')->can('view');
    Route::get('{id}/edit', [ProductController::class, 'edit'])->name('edit')->can('edit');
    Route::put('{id}', [ProductController::class, 'update'])->name('update')->can('edit');
    Route::delete('{id}', [ProductController::class, 'destroy'])->name('destroy')->can('delete');
});
