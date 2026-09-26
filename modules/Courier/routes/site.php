<?php

use Illuminate\Support\Facades\Route;
use Modules\Courier\Http\Controllers\TrackController;

Route::get('track', [
    TrackController::class,
    'show',
])->name('site.track');

Route::get('track/result', [
    TrackController::class,
    'result',
])->middleware('throttle:10,1')->name('site.track.result');
