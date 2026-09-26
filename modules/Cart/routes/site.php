<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\SiteCartController;

Route::middleware('resolve.cart')->group(function () {
    Route::get('cart', [SiteCartController::class, 'index'])->name('shop.cart');
    Route::get('checkout', fn () => redirect()->route('shop.cart'))->name('shop.checkout');

    Route::get('cart/fetch', [SiteCartController::class, 'fetch'])->name('shop.cart.fetch');
    Route::post('cart/coupon', [SiteCartController::class, 'applyCoupon'])->name('shop.cart.coupon.apply');
    Route::delete('cart/coupon', [SiteCartController::class, 'removeCoupon'])->name('shop.cart.coupon.remove');
    Route::post('cart/items', [SiteCartController::class, 'store'])->name('shop.cart.items.store');
    Route::put('cart/items/{itemId}', [SiteCartController::class, 'update'])->name('shop.cart.items.update');
    Route::delete('cart/items/{itemId}', [SiteCartController::class, 'destroy'])->name('shop.cart.items.destroy');
    Route::delete('cart/items', [SiteCartController::class, 'destroyAll'])->name('shop.cart.items.destroyAll');
});
