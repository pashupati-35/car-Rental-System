<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;


// Authentication routes
require __DIR__ . '/admin-auth.php';
require __DIR__ . '/owner-auth.php';
require __DIR__ . '/customer-auth.php';

Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);



// Guest dashboard
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/view/{id}', [DashboardController::class, 'view'])->name('view.show');
Route::get('/car/{id}/dates', [DashboardController::class, 'getBookingDates'])->name('car.dates');
Route::get('/car-calendar/{id}', [DashboardController::class, 'showCalendar'])->name('car.calendar');

Route::get('/map', function () {
    return view('map');
});
