<?php

use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Owner\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Owner\Auth\PasswordController;
use App\Http\Controllers\Owner\Auth\RegisteredOwnerController;
use App\Http\Controllers\Owner\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:owner')->prefix('owner')->name('owner.')->group(function () {
    Route::get('register', [RegisteredOwnerController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredOwnerController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});
Route::middleware('auth:owner')->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.dashboard');
    })->middleware(['verified'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Owner routes
Route::middleware('auth:owner')->group(function () {
    // Resource route for cars management
    Route::resource('cars', CarController::class);
    Route::get('/owner/bookings', [BookingController::class, 'index'])->name('owner.bookings.index');
    Route::post('/owner/bookings/{id}', [BookingController::class, 'confirmBooking'])->name('owner.bookings.confirm');
    Route::post('/owner/bookings/{id}/cancel', [BookingController::class, 'cancelBooking'])->name('owner.bookings.cancel');

    Route::get('/auth/verified-cars', [OwnerController::class, 'VerifiedCars'])->name('cars.verified');
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    Route::post('/search', [OwnerController::class, 'search'])->name('owner.search');


});
