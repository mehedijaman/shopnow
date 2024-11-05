<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;

Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/contact', [IndexController::class, 'contact'])->name('site.contact');
