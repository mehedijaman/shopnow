<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\SiteCartController;

Route::get('cart', [SiteCartController::class, 'index'])->name('shop.cart');
Route::get('checkout', [SiteCartController::class, 'checkout'])->middleware('auth.customer')->name('shop.checkout');
