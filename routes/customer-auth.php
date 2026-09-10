<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\Auth\MFAController;
use App\Http\Controllers\Customer\Auth\NewPasswordController;
use App\Http\Controllers\Customer\Auth\PasswordController;
use App\Http\Controllers\Customer\Auth\PasswordResetLinkController;
use App\Http\Controllers\Customer\Auth\RegisteredCustomerController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\CustomerBookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->name('customer.')->group(function () {
    // API / Unified Auth endpoints matching Admin structure
    Route::post('check/verification-enabled', [MFAController::class, 'checkVerificationEnabled']);
    Route::post('reset/password', [LoginController::class, 'resetPassword']);
    Route::post('do-reset/password', [LoginController::class, 'doResetPassword']);
    Route::post('request/verification-code', [MFAController::class, 'requestEmailVerificationCode']);
    Route::post('verify/mfa-verification-code', [MFAController::class, 'verifyMfaVerificationCode']);
    Route::post('verify/email-verification-code', [MFAController::class, 'verifyEmailVerificationCode']);

    // Guest Customer routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('register', [RegisteredCustomerController::class, 'create'])->name('register');
        Route::post('register', [RegisteredCustomerController::class, 'store']);
        
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

    // Authenticated Customer routes
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [AuthenticatedSessionController::class, 'dashboard'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('/password', [PasswordController::class, 'update'])->name('password.update');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // MFA Configuration (Authenticated)
        Route::post('mfa/generate', [MFAController::class, 'generate'])->name('mfa.generate');
        Route::post('mfa/get-mfa-code', [MFAController::class, 'getMfaAuthenticatorCode'])->name('mfa.get-code');
        Route::post('mfa/activate', [MFAController::class, 'activate'])->name('mfa.activate');
        Route::post('mfa/activate-mfa', [MFAController::class, 'activateMfaAuthenticator'])->name('mfa.activate-mfa');
        Route::post('mfa/deactivate', [MFAController::class, 'deactivate'])->name('mfa.deactivate');
        Route::post('mfa/deactivate-mfa', [MFAController::class, 'deactivateMfaAuthenticator'])->name('mfa.deactivate-mfa');
        Route::post('mfa/email/activate', [MFAController::class, 'activateEmailAuthenticator'])->name('mfa.email.activate');
        Route::post('mfa/email/deactivate', [MFAController::class, 'deactivateEmailAuthenticator'])->name('mfa.email.deactivate');

        // Fleet Showroom & Availability within Customer Portal
        Route::get('/cars', [\App\Http\Controllers\Customer\FleetController::class, 'index'])->name('cars');
        Route::get('/cars/{id}', [\App\Http\Controllers\Customer\FleetController::class, 'show'])->name('cars.show');
        Route::get('/calendar', [\App\Http\Controllers\Customer\FleetController::class, 'calendar'])->name('calendar');

        // Bookings
        Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/{id}', [CustomerBookingController::class, 'show'])->name('booking.detail');
        Route::post('/bookings/{id}/cancel', [CustomerBookingController::class, 'cancelBooking'])->name('booking.cancel');
        Route::get('/booking/{id}/pdf', [BookingController::class, 'downloadPdf'])->name('booking.pdf');
    });
});

// Generic Customer Bookings & Payment
Route::middleware(['auth:customer'])->group(function () {
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/payment/{booking}', [BookingController::class, 'showPaymentForm'])->name('payment.show');
    Route::post('/payment/process', [BookingController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('payment.confirmation');
});