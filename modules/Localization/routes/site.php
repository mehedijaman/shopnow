<?php

use Illuminate\Support\Facades\Route;
use Modules\Localization\Http\Controllers\LocaleSwitchController;

Route::post('/locale', LocaleSwitchController::class)
    ->middleware('throttle:60,1')
    ->name('locale.switch');
