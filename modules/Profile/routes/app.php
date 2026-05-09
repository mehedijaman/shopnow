<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\ProfileController;

Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
Route::put('profile/email', [ProfileController::class, 'updateEmail'])->name('profile.updateEmail');
