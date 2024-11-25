<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::get('order', [
    OrderController::class,
    'index',
])->name('order.index');

Route::get('order/create', [
    OrderController::class,
    'create',
])->name('order.create');

Route::post('order', [
    OrderController::class,
    'store',
])->name('order.store');

Route::get('order/{id}/show', [
    OrderController::class,
    'show',
])->name('order.show');

Route::get('order/{id}/edit', [
    OrderController::class,
    'edit',
])->name('order.edit');

Route::put('order/{id}', [
    OrderController::class,
    'update',
])->name('order.update');

Route::delete('order/{id}', [
    OrderController::class,
    'destroy',
])->name('order.destroy');
