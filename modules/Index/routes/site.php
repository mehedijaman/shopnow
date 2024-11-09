<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;

Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/about', [IndexController::class, 'about'])->name('site.about');
Route::get('/contact', [IndexController::class, 'contact'])->name('site.contact');
Route::get('/privacy-policy', [IndexController::class, 'privacyPolicy'])->name('site.privacyPolicy');
Route::get('/terms-of-service', [IndexController::class, 'termsOfService'])->name('site.termsOfService');
