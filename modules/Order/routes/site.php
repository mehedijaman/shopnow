<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\SiteOrderController;

Route::post('site-order-store', [
    SiteOrderController::class,
    'store',
])->name('site.order.store');
