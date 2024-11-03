<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\SiteProductController;

Route::get('/product/{id}', [SiteProductController::class, 'show'])->name('site.product.show');
