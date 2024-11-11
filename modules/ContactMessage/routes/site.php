<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactMessage\Http\Controllers\SiteContactMessageController;

Route::get('/contact', [SiteContactMessageController::class, 'create'])->name('site.contact');
Route::post('/contact', [SiteContactMessageController::class, 'store'])->name('site.contact.store');
