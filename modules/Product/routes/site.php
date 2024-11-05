<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\SiteProductCategoryController;
use Modules\Product\Http\Controllers\SiteProductController;

Route::get('/products', [SiteProductController::class, 'index'])->name('site.product.index');
Route::get('/products/search', [SiteProductController::class, 'search'])->name('site.product.search');
Route::get('/products/{id}', [SiteProductController::class, 'show'])->name('site.product.show');

Route::get('/categories', [SiteProductCategoryController::class, 'index'])->name('site.product.category.index');
Route::get('/categories/{id}', [SiteProductCategoryController::class, 'show'])->name('site.product.category.show');
