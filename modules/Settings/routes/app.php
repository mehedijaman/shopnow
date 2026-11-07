<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Http\Controllers\SmsController;

Route::post('settings/sms/test', [SmsController::class, 'test'])->middleware('throttle:3,1')->name('settings.sms.test');
Route::get('settings/sms/activity', [SmsController::class, 'activity'])->middleware('throttle:60,1')->name('settings.sms.activity');
Route::get('settings/sms/history', [SmsController::class, 'history'])->name('settings.sms.history');

Route::get('settings', [
    SettingsController::class, 'redirect',
])->name('settings.index');

Route::post('settings/{group}/preview', [SettingsController::class, 'previewAppearance'])
    ->where('group', 'appearance')->middleware('throttle:120,1')->name('settings.appearance.preview');

Route::post('settings/mail/test', [
    SettingsController::class, 'sendTestEmail',
])->name('settings.mail.test');

Route::get('settings/{group}', [
    SettingsController::class, 'show',
])->name('settings.show');

Route::post('settings/{group}', [
    SettingsController::class, 'update',
])->name('settings.update');
