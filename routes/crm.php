<?php

use App\Http\Controllers\Admin\Crm\CorporateAccountController;
use App\Http\Controllers\Admin\Crm\CrmDashboardController;
use App\Http\Controllers\Admin\Crm\CustomerCrmController;
use App\Http\Controllers\Admin\Crm\DealPipelineController;
use App\Http\Controllers\Admin\Crm\LeadController;
use App\Http\Controllers\Admin\Crm\QuotationController;
use App\Http\Controllers\Admin\Crm\SupportTicketController;
use App\Http\Controllers\Crm\Auth\CrmAuthController;
use Illuminate\Support\Facades\Route;

// 1. CRM Portal Public & Authentication Routes
Route::prefix('crm')->group(function () {
    Route::get('login', [CrmAuthController::class, 'create'])->name('crm.login');
    Route::post('login', [CrmAuthController::class, 'store']);
    Route::match(['get', 'post'], 'logout', [CrmAuthController::class, 'destroy'])->name('crm.logout');

    // Pre-auth MFA endpoints
    Route::post('mfa/check-verification', [CrmAuthController::class, 'checkVerification'])->name('crm.mfa.check');
    Route::post('mfa/resend-code', [CrmAuthController::class, 'resendCode'])->name('crm.mfa.resend-code');
    Route::post('mfa/verify-code', [CrmAuthController::class, 'verifyCode'])->name('crm.mfa.verify-code');
});

// Helper definition function for CRM operations
$registerCrmRoutes = function ($route) {
    // CRM Overview Dashboard
    $route->get('dashboard', [CrmDashboardController::class, 'index'])->name('dashboard');

    // Leads & Inquiries
    $route->get('leads', [LeadController::class, 'index'])->name('leads.index');
    $route->post('leads', [LeadController::class, 'store'])->name('leads.store');
    $route->get('leads/{id}', [LeadController::class, 'show'])->name('leads.show');
    $route->put('leads/{id}', [LeadController::class, 'update'])->name('leads.update');
    $route->delete('leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');
    $route->post('leads/{id}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Deals & Sales Pipeline
    $route->get('deals', [DealPipelineController::class, 'index'])->name('deals.index');
    $route->post('deals', [DealPipelineController::class, 'store'])->name('deals.store');
    $route->patch('deals/{id}/stage', [DealPipelineController::class, 'updateStage'])->name('deals.stage');
    $route->put('deals/{id}', [DealPipelineController::class, 'update'])->name('deals.update');
    $route->delete('deals/{id}', [DealPipelineController::class, 'destroy'])->name('deals.destroy');

    // Customer 360, Interactions & CRM Tasks
    $route->get('customers', [CustomerCrmController::class, 'index'])->name('customers.index');
    $route->get('customers/{id}/timeline', [CustomerCrmController::class, 'timeline'])->name('customers.timeline');
    $route->post('customers/{id}/interactions', [CustomerCrmController::class, 'logInteraction'])->name('customers.interactions');
    $route->post('customers/{id}/preferences', [CustomerCrmController::class, 'updatePreferences'])->name('customers.preferences');
    $route->post('customers/{id}/tasks', [CustomerCrmController::class, 'addTask'])->name('customers.tasks');
    $route->patch('tasks/{id}/complete', [CustomerCrmController::class, 'completeTask'])->name('tasks.complete');

    // Quotations & Proposals (CPQ)
    $route->get('quotations', [QuotationController::class, 'index'])->name('quotations.index');
    $route->get('quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    $route->post('quotations', [QuotationController::class, 'store'])->name('quotations.store');
    $route->patch('quotations/{id}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');
    $route->delete('quotations/{id}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

    // Support Tickets & Service Desk
    $route->get('tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    $route->post('tickets', [SupportTicketController::class, 'store'])->name('tickets.store');
    $route->get('tickets/{id}', [SupportTicketController::class, 'show'])->name('tickets.show');
    $route->post('tickets/{id}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
    $route->patch('tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.status');

    // Corporate Accounts (B2B)
    $route->get('corporate-accounts', [CorporateAccountController::class, 'index'])->name('corporate-accounts.index');
    $route->post('corporate-accounts', [CorporateAccountController::class, 'store'])->name('corporate-accounts.store');
    $route->put('corporate-accounts/{id}', [CorporateAccountController::class, 'update'])->name('corporate-accounts.update');
    $route->delete('corporate-accounts/{id}', [CorporateAccountController::class, 'destroy'])->name('corporate-accounts.destroy');
};

// 2. Register with /crm prefix (Primary CRM Portal routes)
Route::group(['middleware' => ['admin'], 'prefix' => 'crm', 'as' => 'crm.'], $registerCrmRoutes);

// 3. Register with /admin/crm prefix (Backwards compatibility & internal linking)
Route::group(['middleware' => ['admin'], 'prefix' => 'admin/crm', 'as' => 'admin.crm.'], $registerCrmRoutes);
