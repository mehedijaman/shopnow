<?php

use Illuminate\Support\Facades\Route;
use Modules\Courier\Http\Controllers\BookShipmentController;
use Modules\Courier\Http\Controllers\FraudCheckController;

Route::post('order/{id}/book-shipment', [
    BookShipmentController::class,
    'book',
])->name('order.bookShipment')->can('order-list');

Route::post('order/{id}/shipment/refresh', [
    BookShipmentController::class,
    'refresh',
])->name('order.refreshShipment')->can('order-list');

Route::post('order/{id}/fraud-check', [
    FraudCheckController::class,
    'run',
])->name('order.fraudCheck')->can('order-list');
