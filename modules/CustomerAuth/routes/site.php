<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerAuth\Http\Controllers\AuthenticatedSessionController;
use Modules\CustomerAuth\Http\Controllers\NewPasswordController;
use Modules\CustomerAuth\Http\Controllers\PasswordResetLinkController;

Route::get('login', [
    AuthenticatedSessionController::class,
    'loginForm',
])->name('customerAuth.loginForm');

Route::post('login', [
    AuthenticatedSessionController::class,
    'login',
])->name('customerAuth.login');

Route::get('signup', [
    AuthenticatedSessionController::class,
    'signupForm',
])->name('customerAuth.signupForm');

Route::post('signup', [
    AuthenticatedSessionController::class,
    'signup',
])->name('customerAuth.signup');

Route::get('logout', [
    AuthenticatedSessionController::class,
    'logout',
])->name('customerAuth.logout');

// form to receive the email that contains the link to reset password
Route::get('forgot-password', [
    PasswordResetLinkController::class,
    'forgotPasswordForm',
])->name('customerAuth.forgotPassword');

Route::post('send-reset-link-email', [
    PasswordResetLinkController::class,
    'sendResetLinkEmail',
])->name('customerAuth.sendResetLinkEmail');

// password reset form
Route::get('reset-password/{token}', [
    NewPasswordController::class,
    'resetPasswordForm',
])->name('customerAuth.resetPasswordForm');

Route::post('reset-password', [
    NewPasswordController::class,
    'store',
])->name('customerAuth.resetPassword');
