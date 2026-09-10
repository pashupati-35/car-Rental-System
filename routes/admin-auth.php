<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\MFAController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredAdminController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('register', [RegisteredAdminController::class, 'create'])->name('register');
        Route::post('register', [RegisteredAdminController::class, 'store']);
        
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
        Route::post('mfa/verify-code', [MFAController::class, 'verifyCode'])->name('mfa.verify-code');
        Route::post('mfa/resend-code', [MFAController::class, 'resendCode'])->name('mfa.resend-code');
    });

    // Authenticated Admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AuthenticatedSessionController::class, 'dashboard'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

        // Email templates management
        Route::resource('email-templates', EmailTemplateController::class);

        // Managing owners & Admin creating owner with reset password email
        Route::get('/owners', [OwnerController::class, 'index'])->name('owner.index');
        Route::get('/owners-list', [OwnerController::class, 'index'])->name('owners');
        Route::post('/owners', [AdminController::class, 'createOwner'])->name('owner.store');
        Route::get('/owners/{id}', [OwnerController::class, 'show'])->name('owner.show');
        Route::get('/owner/edit/{id}', [OwnerController::class, 'edit'])->name('owner.edit');
        Route::get('/owner/view/{id}', [OwnerController::class, 'view'])->name('owner.view');
        Route::delete('/owner/delete/{id}', [OwnerController::class, 'destroy'])->name('owner.delete');
        Route::delete('/owners/{id}', [OwnerController::class, 'destroy'])->name('owner.destroy');
        Route::patch('/owner/update/{id}', [OwnerController::class, 'update'])->name('owner.update');
        Route::patch('/owners/{id}', [OwnerController::class, 'update'])->name('owner.patch');

        // Owner nested fleet cars & drivers management
        Route::post('/owners/{id}/cars', [OwnerController::class, 'storeCar'])->name('owner.cars.store');
        Route::post('/owners/{id}/cars/{carId}', [OwnerController::class, 'updateCar'])->name('owner.cars.update');
        Route::patch('/owners/{id}/cars/{carId}', [OwnerController::class, 'updateCar'])->name('owner.cars.patch');
        Route::delete('/owners/{id}/cars/{carId}', [OwnerController::class, 'destroyCar'])->name('owner.cars.destroy');
        Route::post('/owners/{id}/drivers', [OwnerController::class, 'storeDriver'])->name('owner.drivers.store');
        Route::post('/owners/{id}/drivers/{driverId}', [OwnerController::class, 'updateDriver'])->name('owner.drivers.update');
        Route::patch('/owners/{id}/drivers/{driverId}', [OwnerController::class, 'updateDriver'])->name('owner.drivers.patch');
        Route::delete('/owners/{id}/drivers/{driverId}', [OwnerController::class, 'destroyDriver'])->name('owner.drivers.destroy');

        // Managing cars
        Route::get('cars', [AdminController::class, 'index'])->name('cars.index');
        Route::get('cars-list', [AdminController::class, 'index'])->name('cars-list');
        Route::get('cars/{id}', [AdminController::class, 'show'])->name('cars.show');
        Route::patch('/cars/{car}/verify', [AdminController::class, 'verifyCar'])->name('cars.verify');
        Route::patch('/cars/{car}/reject', [AdminController::class, 'rejectCar'])->name('cars.reject');
        Route::delete('/cars/{id}', [AdminController::class, 'destroyCar'])->name('cars.destroy');

        // Managing drivers directory
        Route::get('/drivers', [AdminController::class, 'viewDrivers'])->name('drivers');
        Route::post('/drivers', [AdminController::class, 'storeDriver'])->name('drivers.store');
        Route::patch('/drivers/{id}', [AdminController::class, 'updateDriver'])->name('drivers.update');
        Route::delete('/drivers/{id}', [AdminController::class, 'destroyDriver'])->name('drivers.destroy');

        // Managing customers & Admin creating customer with reset password email
        Route::get('/customers', [AdminController::class, 'viewCustomers'])->name('customers');
        Route::post('/customers', [AdminController::class, 'createCustomer'])->name('customer.store');
        Route::get('/customers/{id}', [AdminController::class, 'showCustomer'])->name('customer.show');
        Route::patch('/customers/{id}', [AdminController::class, 'updateCustomer'])->name('customer.update');
        Route::delete('/customers/{id}', [AdminController::class, 'destroy'])->name('customer.destroy');

        // Customer nested bookings & payment CRUD
        Route::post('/customers/{id}/bookings', [AdminController::class, 'storeCustomerBooking'])->name('customer.bookings.store');
        Route::post('/customers/{id}/bookings/{bookingId}', [AdminController::class, 'updateCustomerBooking'])->name('customer.bookings.update');
        Route::patch('/customers/{id}/bookings/{bookingId}', [AdminController::class, 'updateCustomerBooking'])->name('customer.bookings.patch');
        Route::delete('/customers/{id}/bookings/{bookingId}', [AdminController::class, 'destroyCustomerBooking'])->name('customer.bookings.destroy');
        Route::post('/customers/{id}/payments', [AdminController::class, 'storeCustomerPayment'])->name('customer.payments.store');
        Route::delete('/customers/{id}/payments/{paymentId}', [AdminController::class, 'destroyCustomerPayment'])->name('customer.payments.destroy');
        
        // Managing bookings
        Route::get('/booked-cars', [AdminController::class, 'viewBookings'])->name('booked-cars');
        Route::get('/bookings', [AdminController::class, 'viewBookings'])->name('bookings');
        Route::post('/bookings/{id}/confirm', [AdminController::class, 'confirmBooking'])->name('booking.confirm');
        Route::post('/bookings/{id}/cancel', [AdminController::class, 'cancelBooking'])->name('booking.cancel');
        Route::delete('/bookings/{id}', [AdminController::class, 'destroyBooking'])->name('booking.destroy');

        // Master CMS Management Dashboard & API
        Route::get('/cms', [\App\Http\Controllers\Admin\AdminCmsController::class, 'index'])->name('cms.index');
        Route::get('/cms/data/{module}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'getData'])->name('cms.data');
        Route::post('/cms/data/{module}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'storeItem'])->name('cms.store');
        Route::post('/cms/data/{module}/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateItem'])->name('cms.update');
        Route::delete('/cms/data/{module}/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'deleteItem'])->name('cms.delete');
        Route::post('/cms/data/{module}/{id}/toggle-status', [\App\Http\Controllers\Admin\AdminCmsController::class, 'toggleStatus'])->name('cms.toggle');
    });
});
