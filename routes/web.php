<?php

use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Authentication routes
require __DIR__ . '/admin-auth.php';
require __DIR__ . '/owner-auth.php';
require __DIR__ . '/customer-auth.php';

// Social Auth routes
Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Public & Guest routes
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/cars', [DashboardController::class, 'carsIndex'])->name('cars.index.public');
Route::get('/cars/{id}', [DashboardController::class, 'view'])->name('cars.show.public');
Route::get('/view/{id}', [DashboardController::class, 'view'])->name('view.show');
Route::get('/car/{id}/dates', [DashboardController::class, 'getBookingDates'])->name('car.dates');
Route::get('/car-calendar', [DashboardController::class, 'showCalendar'])->name('car.calendar.all');
Route::get('/car-calendar/{id}', [DashboardController::class, 'showCalendar'])->name('car.calendar');

Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');

// AI Chatbot
Route::get('/ai-chat', function () {
    return Inertia::render('ai/AIChat');
})->name('ai.chat');
Route::get('/chat/ask-ai', function () {
    return Inertia::render('ai/AIChat');
})->name('ask-ai');
Route::post('/ai-chat', [AIController::class, 'ask'])->name('ai-chat.post');
Route::get('/ask-ai/history', [AIController::class, 'history'])->name('ask-ai.history');
Route::post('/ask-ai', [AIController::class, 'ask'])->name('ask-ai.submit');
