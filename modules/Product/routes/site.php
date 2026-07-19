<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\DownloadController;
use Modules\Product\Http\Controllers\SiteProductController;

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [SiteProductController::class, 'index'])->name('index');
    Route::get('/search/{searchText?}', [SiteProductController::class, 'search'])->name('search');
    Route::get('product/{id}/{slug?}', [SiteProductController::class, 'show'])->name('product');
    Route::get('category/{id}/{slug?}', [SiteProductController::class, 'category'])->name('category');
});

Route::get('brand/{id}/{slug?}', [SiteProductController::class, 'brand'])->name('brand');
Route::get('brands', [SiteProductController::class, 'brands'])->name('brands');

Route::get('download/{token}', [DownloadController::class, 'show'])->name('product.download');

Route::middleware('auth:customer')->group(function () {
    Route::get('account/downloads', [DownloadController::class, 'index'])->name('account.downloads');
});
