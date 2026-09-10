<?php

use App\Http\Controllers\Admin\AdminPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Inertia Vue Rendering Routes
|--------------------------------------------------------------------------
|
| Here is where all Inertia Vue pages for the admin dashboard and
| management modules are rendered via the AdminPageController.
|
*/

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function ($route) {
    // Dashboard
    $route->get('dashboard', [AdminPageController::class, 'dashboard'])->name('admin.dashboard');

    // Fleet Cars
    $route->get('cars', [AdminPageController::class, 'cars'])->name('admin.cars.index');
    $route->get('cars/{id}', [AdminPageController::class, 'carDetails'])->name('admin.cars.show');

    // Driver Directory
    $route->get('drivers', [AdminPageController::class, 'drivers'])->name('admin.drivers');

    // Booked Cars / Reservations
    $route->get('booked-cars', [AdminPageController::class, 'bookings'])->name('admin.booked-cars');

    // Fleet Owners
    $route->get('owners', [AdminPageController::class, 'owners'])->name('admin.owners');
    $route->get('owners/{id}', [AdminPageController::class, 'ownerDetails'])->name('admin.owner.show');

    // Customers
    $route->get('customers', [AdminPageController::class, 'customers'])->name('admin.customers');
    $route->get('customers/{id}', [AdminPageController::class, 'customerDetails'])->name('admin.customer.show');

    // Master CMS Suite
    $route->get('cms', [AdminPageController::class, 'cms'])->name('admin.cms');

    // Email Templates
    $route->get('email-templates', [AdminPageController::class, 'emailTemplates'])->name('admin.email-templates.index');
    $route->get('email-templates/{id}/edit', [AdminPageController::class, 'emailTemplateEdit'])->name('admin.email-templates.edit');

    // Activity Logs Views (Global & Per Owner / Customer)
    $route->get('activity-logs', [AdminPageController::class, 'activityLogs'])->name('admin.activity-logs.index');
    $route->get('activity-logs/owner/{owner_id}', function ($owner_id) {
        request()->merge(['owner_id' => $owner_id]);
        return app(AdminPageController::class)->activityLogs(request());
    })->name('admin.activity-logs.owner');
    $route->get('activity-logs/customer/{customer_id}', function ($customer_id) {
        request()->merge(['customer_id' => $customer_id]);
        return app(AdminPageController::class)->activityLogs(request());
    })->name('admin.activity-logs.customer');

    // Email Logs Views (Global & Per Owner / Customer)
    $route->get('email-logs', [AdminPageController::class, 'emailLogs'])->name('admin.email-logs.index');
    $route->get('email-logs/owner/{owner_id}', function ($owner_id) {
        request()->merge(['owner_id' => $owner_id]);
        return app(AdminPageController::class)->emailLogs(request());
    })->name('admin.email-logs.owner');
    $route->get('email-logs/customer/{customer_id}', function ($customer_id) {
        request()->merge(['customer_id' => $customer_id]);
        return app(AdminPageController::class)->emailLogs(request());
    })->name('admin.email-logs.customer');

    // Profile & Security
    $route->get('profile', [AdminPageController::class, 'profile'])->name('admin.profile.edit');
    $route->get('security', [AdminPageController::class, 'security'])->name('admin.security');
});
