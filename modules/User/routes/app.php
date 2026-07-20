<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::get('user/recycle-bin', [
    UserController::class, 'recycleBin',
])->name('user.recycleBin.index');

Route::get('user/recycle-bin/{id}/restore', [
    UserController::class, 'restore',
])->name('user.recycleBin.restore');

Route::delete('user/recycle-bin/{id}/destroy', [
    UserController::class, 'destroyForce',
])->name('user.recycleBin.destroyForce');

Route::delete('user/recycle-bin/empty', [
    UserController::class, 'emptyRecycleBin',
])->name('user.recycleBin.empty');

Route::get('user/recycle-bin/restore-all', [
    UserController::class, 'restoreRecycleBin',
])->name('user.recycleBin.restoreAll');

Route::get('user', [
    UserController::class, 'index',
])->name('user.index');

Route::get('user/create', [
    UserController::class, 'create',
])->name('user.create');

Route::get('user/{id}/edit', [
    UserController::class, 'edit',
])->name('user.edit');

Route::post('user', [
    UserController::class, 'store',
])->name('user.store');

Route::put('user/{id}', [
    UserController::class, 'update',
])->name('user.update');

Route::delete('user/{id}', [
    UserController::class, 'destroy',
])->name('user.destroy');
