<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\CartController;

Route::get('cart', [
    CartController::class, 'index',
])->name('cart.index');

Route::get('cart/create', [
    CartController::class, 'create',
])->name('cart.create');

Route::post('cart', [
    CartController::class, 'store',
])->name('cart.store');

Route::get('cart/{id}/edit', [
    CartController::class, 'edit',
])->name('cart.edit');

Route::put('cart/{id}', [
    CartController::class, 'update',
])->name('cart.update');

Route::delete('cart/{id}', [
    CartController::class, 'destroy',
])->name('cart.destroy');
