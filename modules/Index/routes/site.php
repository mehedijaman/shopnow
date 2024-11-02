<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;

Route::get('/', [IndexController::class, 'index'])->name('index.index');
Route::get('/shop', [IndexController::class, 'shop'])->name('shop.index');
