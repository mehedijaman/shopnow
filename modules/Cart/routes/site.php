<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\SiteCartController;

Route::get('cart', [SiteCartController::class, 'index'])->name('site.cart.index');
