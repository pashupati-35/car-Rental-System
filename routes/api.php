<?php

use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\CarApiController;
use App\Http\Controllers\Owner\DriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Cars API
Route::get('/cars', [CarApiController::class, 'index']);
Route::get('/cars/{id}', [CarApiController::class, 'show']);
Route::get('/cars/{id}/booked-dates', [CarApiController::class, 'bookedDates']);

// Calendar Availability Check Algorithm
Route::post('/bookings/check-availability', [BookingApiController::class, 'checkAvailability']);

// Customer Booking & Payment Endpoints
Route::post('/bookings/create', [BookingApiController::class, 'store']);
Route::post('/bookings/payment', [BookingApiController::class, 'processPayment']);
Route::get('/customer/my-bookings', [BookingApiController::class, 'myBookings']);

// Owner Driver API endpoints
Route::middleware('auth:owner')->prefix('owner')->group(function () {
    Route::get('/drivers', [DriverController::class, 'index']);
    Route::post('/drivers', [DriverController::class, 'store']);
    Route::patch('/drivers/{id}', [DriverController::class, 'update']);
    Route::delete('/drivers/{id}', [DriverController::class, 'destroy']);
});

// Admin System APIs
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/stats', [AdminApiController::class, 'stats']);
    Route::get('/cars', [AdminApiController::class, 'cars']);
    Route::get('/drivers', [AdminApiController::class, 'drivers']);
    Route::get('/owners', [AdminApiController::class, 'owners']);
    Route::get('/customers', [AdminApiController::class, 'customers']);
    Route::get('/bookings', [AdminApiController::class, 'bookings']);
});

// AI Chatbot
Route::post('/ask-ai', [AIController::class, 'ask']);
