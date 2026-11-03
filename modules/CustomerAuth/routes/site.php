<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerAuth\Http\Controllers\AuthenticatedSessionController;
use Modules\CustomerAuth\Http\Controllers\NewPasswordController;
use Modules\CustomerAuth\Http\Controllers\PasswordResetLinkController;
use Modules\CustomerAuth\Http\Controllers\PhoneVerificationController;

Route::get('verify-phone', [PhoneVerificationController::class, 'show'])->name('customerAuth.phone.show');
Route::post('verify-phone', [PhoneVerificationController::class, 'verify'])->middleware('throttle:10,1')->name('customerAuth.phone.verify');
Route::post('verify-phone/resend', [PhoneVerificationController::class, 'resend'])->middleware('throttle:3,1')->name('customerAuth.phone.resend');
Route::post('verify-phone/change', [PhoneVerificationController::class, 'changePhone'])->middleware('throttle:3,1')->name('customerAuth.phone.change');
Route::post('verify-phone/cancel', [PhoneVerificationController::class, 'cancel'])->name('customerAuth.phone.cancel');

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
])->middleware('throttle:5,1')->name('customerAuth.signup');

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
