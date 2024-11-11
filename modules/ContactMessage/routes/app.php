<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactMessage\Http\Controllers\ContactMessageController;

Route::prefix('contact-message')->name('contactMessage.')->group(function () {
    Route::prefix('recycle-bin')->name('recycleBin.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'recycleBin'])->name('index')->can('recycle-bin-list');
        Route::get('{id}/restore', [ContactMessageController::class, 'restore'])->name('restore')->can('recycle-bin-restore');
        Route::delete('{id}/destroy', [ContactMessageController::class, 'destroyForce'])->name('destroyForce')->can('recycle-bin-delete');
        Route::delete('empty', [ContactMessageController::class, 'emptyRecycleBin'])->name('empty')->can('recycle-bin-delete');
        Route::get('restore', [ContactMessageController::class, 'restoreRecycleBin'])->name('restoreAll')->can('recycle-bin-restore');
    });

    Route::get('/', [ContactMessageController::class, 'index'])->name('index');
    Route::get('{id}/edit', [ContactMessageController::class, 'edit'])->name('edit');
    Route::put('{id}', [ContactMessageController::class, 'update'])->name('update');
    Route::delete('{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
});
