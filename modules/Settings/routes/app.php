<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::get('settings', [
    SettingsController::class, 'redirect',
])->name('settings.index');

Route::get('settings/{group}', [
    SettingsController::class, 'show',
])->name('settings.show');

Route::post('settings/{group}', [
    SettingsController::class, 'update',
])->name('settings.update');
