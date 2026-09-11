<?php

use App\Http\Controllers\Owner\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Owner\Auth\LoginController;
use App\Http\Controllers\Owner\Auth\MFAController;
use App\Http\Controllers\Owner\Auth\NewPasswordController;
use App\Http\Controllers\Owner\Auth\PasswordResetLinkController;
use App\Http\Controllers\Owner\Auth\RegisteredOwnerController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\CalendarController;
use App\Http\Controllers\Owner\CarController;
use App\Http\Controllers\Owner\DriverController;
use App\Http\Controllers\Owner\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('owner')->name('owner.')->group(function () {
    // API / Unified Auth endpoints
    Route::post('check/verification-enabled', [MFAController::class, 'checkVerificationEnabled']);
    Route::post('reset/password', [LoginController::class, 'resetPassword']);
    Route::post('do-reset/password', [LoginController::class, 'doResetPassword']);
    Route::post('request/verification-code', [MFAController::class, 'requestEmailVerificationCode']);
    Route::post('verify/mfa-verification-code', [MFAController::class, 'verifyMfaVerificationCode']);
    Route::post('verify/email-verification-code', [MFAController::class, 'verifyEmailVerificationCode']);

    // Guest Owner routes
    Route::middleware('guest:owner')->group(function () {
        Route::get('register', [RegisteredOwnerController::class, 'create'])->name('register');
        Route::post('register', [RegisteredOwnerController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        // Password Reset
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update.reset');

        // MFA Verification
        Route::get('mfa/verify', [AuthenticatedSessionController::class, 'showMfa'])->name('mfa.verify');
        Route::post('mfa/check-verification', [MFAController::class, 'checkVerification'])->name('mfa.check');
        Route::post('mfa/resend-code', [MFAController::class, 'resendCode'])->name('mfa.resend-code');
        Route::post('mfa/verify-code', [MFAController::class, 'verifyCode'])->name('mfa.verify-code');
    });

    // Authenticated Owner routes
    Route::middleware('auth:owner')->group(function () {
        Route::get('/dashboard', [AuthenticatedSessionController::class, 'dashboard'])->name('dashboard');
        Route::post('/theme-style', [ProfileController::class, 'updateThemeStyle'])->name('theme-style');

        // Profile & Security
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::match(['get', 'post'], 'logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // MFA Configuration (Authenticated)
        Route::post('mfa/generate', [MFAController::class, 'generate'])->name('mfa.generate');
        Route::post('mfa/get-mfa-code', [MFAController::class, 'getMfaAuthenticatorCode'])->name('mfa.get-code');
        Route::post('mfa/activate', [MFAController::class, 'activate'])->name('mfa.activate');
        Route::post('mfa/activate-mfa', [MFAController::class, 'activateMfaAuthenticator'])->name('mfa.activate-mfa');
        Route::post('mfa/deactivate', [MFAController::class, 'deactivate'])->name('mfa.deactivate');
        Route::post('mfa/deactivate-mfa', [MFAController::class, 'deactivateMfaAuthenticator'])->name('mfa.deactivate-mfa');
        Route::post('mfa/email/activate', [MFAController::class, 'activateEmailAuthenticator'])->name('mfa.email.activate');
        Route::post('mfa/email/deactivate', [MFAController::class, 'deactivateEmailAuthenticator'])->name('mfa.email.deactivate');

        // Resource route for cars management
        Route::resource('cars', CarController::class);

        // Drivers management
        Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::match(['put', 'patch', 'post'], '/drivers/{id}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('drivers.destroy');

        // Owner Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        // Owner Calendar Schedule
        Route::get('/calendar/{carId?}', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/car-calendar/{carId?}', [CalendarController::class, 'index'])->name('car-calendar');
    });
});
