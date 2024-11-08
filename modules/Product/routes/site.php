<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\SiteProductController;

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [SiteProductController::class, 'index'])->name('index');
    Route::get('/search/{searchText?}', [SiteProductController::class, 'search'])->name('search');
    Route::get('product/{id}/{slug?}', [SiteProductController::class, 'show'])->name('product');
    Route::get('category/{id}/{slug?}', [SiteProductController::class, 'category'])->name('category');
});
