<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. Dedicated Admin Portal Subdomain Routes (e.g. portal.carrental.local)
Route::domain('portal.{domain}')->group(function () {
    Route::get('/', function () {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    });

    Route::get('/login', [AdminAuthController::class, 'create'])->name('portal.admin.login');
    Route::post('/login', [AdminAuthController::class, 'store']);
    Route::match(['get', 'post'], '/logout', [AdminAuthController::class, 'destroy'])->name('portal.admin.logout');
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/cms', function (Request $request) {
        return redirect('/admin/cms'.($request->getQueryString() ? '?'.$request->getQueryString() : ''));
    });
});

Route::get('/cms', function (Request $request) {
    return redirect('/admin/cms'.($request->getQueryString() ? '?'.$request->getQueryString() : ''));
});

// Admin Inertia Vue Page routes
require __DIR__.'/admin-vue.php';

// Authentication routes
require __DIR__.'/admin-auth.php';
require __DIR__.'/owner-auth.php';
require __DIR__.'/customer-auth.php';

// Social Auth routes
Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Public & Guest routes
Route::get('/', function () {
    $host = request()->getHost();
    if (str_starts_with($host, 'portal.')) {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    }

    return app(DashboardController::class)->index();
})->name('home');

Route::get('/cars', [DashboardController::class, 'carsIndex'])->name('cars.index.public');
Route::get('/cars/{id}', [DashboardController::class, 'view'])->name('cars.show.public');
Route::get('/view/{id}', [DashboardController::class, 'view'])->name('view.show');
Route::get('/car/{id}/dates', [DashboardController::class, 'getBookingDates'])->name('car.dates');
Route::get('/car-calendar', [DashboardController::class, 'showCalendar'])->name('car.calendar.all');
Route::get('/car-calendar/{id}', [DashboardController::class, 'showCalendar'])->name('car.calendar');

// Main domain /login: Directly routes to Customer Login (or Admin if host is portal.*)
Route::get('/login', function () {
    $host = request()->getHost();
    if (str_starts_with($host, 'portal.')) {
        return redirect()->route('admin.login');
    }

    return redirect()->route('customer.login');
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
