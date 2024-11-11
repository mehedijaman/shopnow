<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\SiteOrderController;

Route::post('order', [
    SiteOrderController::class,
    'store',
])->name('site.order.store');
