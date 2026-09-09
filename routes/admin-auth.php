<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\RegisteredAdminController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('register', [RegisteredAdminController::class, 'create'])->name('register');
    Route::post('register', [RegisteredAdminController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AuthenticatedSessionController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email templates management
    Route::resource('email-templates', EmailTemplateController::class);

    // Routes for managing owners
    Route::get('/owners', [OwnerController::class, 'index'])->name('owner.index');
    Route::get('/owner/edit/{id}', [OwnerController::class, 'edit'])->name('owner.edit');
    Route::get('/owner/view/{id}', [OwnerController::class, 'view'])->name('owner.view');
    Route::delete('/owner/delete/{id}', [OwnerController::class, 'destroy'])->name('owner.delete');
    Route::patch('/owner/update/{id}', [OwnerController::class, 'update'])->name('owner.update');

    // Routes for managing cars
    Route::get('cars', [AdminController::class, 'index'])->name('cars.index');
    Route::get('cars-list', [AdminController::class, 'index'])->name('cars-list');
    Route::get('cars/{id}', [AdminController::class, 'show'])->name('cars.show');

    Route::get('/customers', [AdminController::class, 'viewCustomers'])->name('customers');
    Route::delete('/customers/{id}', [AdminController::class, 'destroy'])->name('customer.destroy');
    Route::get('/booked-cars', [AdminController::class, 'viewBookings'])->name('booked-cars');
    Route::get('/bookings', [AdminController::class, 'viewBookings'])->name('bookings');
    Route::delete('/bookings/{id}', [AdminController::class, 'destroyBooking'])->name('booking.destroy');
});

Route::patch('/cars/{car}/verify', [AdminController::class, 'verifyCar'])->name('cars.verify');
Route::patch('/cars/{car}/reject', [AdminController::class, 'rejectCar'])->name('cars.reject');
