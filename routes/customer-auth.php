<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\Auth\PasswordController;
use App\Http\Controllers\Customer\Auth\RegisteredCustomerController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\CustomerBookingController;

use Illuminate\Support\Facades\Route;

Route::middleware('guest:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('register', [RegisteredCustomerController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredCustomerController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [AuthenticatedSessionController::class, 'dashboard'])
        ->middleware(['verified'])
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['auth:customer'])->group(function () {
    Route::get('/car/{id}', [BookingController::class, 'showCar'])->name('car.show');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/customer/bookings', [CustomerBookingController::class, 'index'])->name('customer.bookings');
    Route::get('/customer/bookings/{id}', [CustomerBookingController::class, 'show'])->name('customer.booking.detail');

});
Route::post('/my-bookings/{id}/cancel', [CustomerBookingController::class, 'cancelBooking'])->name('customer.booking.cancel');
Route::get('/booking/available_dates', [BookingController::class, 'availableDates'])->name('booking.available_dates');
Route::get('/booking/{id}/pdf', [BookingController::class, 'downloadPdf'])->name('booking.pdf');
Route::get('/payment/{booking}', [BookingController::class, 'showPaymentForm'])->name('payment.show');
Route::post('/payment/process', [BookingController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('payment.confirmation');
// Show the return booking form
Route::get('/booking/return/{id}', [BookingController::class, 'showReturnForm'])->name('customer.booking.return');

// Handle the return booking request
Route::post('/booking/return/{id}', [BookingController::class, 'returnBooking']);