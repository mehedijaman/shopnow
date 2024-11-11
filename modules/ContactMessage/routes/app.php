<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactMessage\Http\Controllers\ContactMessageController;

Route::get('contact-message', [
    ContactMessageController::class, 'index',
])->name('contactMessage.index');

Route::get('contact-message/create', [
    ContactMessageController::class, 'create',
])->name('contactMessage.create');

Route::post('contact-message', [
    ContactMessageController::class, 'store',
])->name('contactMessage.store');

Route::get('contact-message/{id}/edit', [
    ContactMessageController::class, 'edit',
])->name('contactMessage.edit');

Route::put('contact-message/{id}', [
    ContactMessageController::class, 'update',
])->name('contactMessage.update');

Route::delete('contact-message/{id}', [
    ContactMessageController::class, 'destroy',
])->name('contactMessage.destroy');
