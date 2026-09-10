<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\MFAController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredAdminController;
use App\Http\Controllers\Admin\Cms\Album\AlbumController;
use App\Http\Controllers\Admin\Cms\Album\Value\AlbumValueController;
use App\Http\Controllers\Admin\Cms\AttachmentTypeController;
use App\Http\Controllers\Admin\Cms\Blog\BlogController;
use App\Http\Controllers\Admin\Cms\Blog\Category\BlogCategoryController;
use App\Http\Controllers\Admin\Cms\Career\Application\CareerApplicationController;
use App\Http\Controllers\Admin\Cms\Career\CareerController;
use App\Http\Controllers\Admin\Cms\ContactUs\ContactUsController;
use App\Http\Controllers\Admin\Cms\DocumentTypeController;
use App\Http\Controllers\Admin\Cms\Download\DownloadController;
use App\Http\Controllers\Admin\Cms\Download\Type\DownloadTypeController;
use App\Http\Controllers\Admin\Cms\Enquiry\EnquiryController;
use App\Http\Controllers\Admin\Cms\Faq\Category\FaqCategoryController;
use App\Http\Controllers\Admin\Cms\Faq\FaqController;
use App\Http\Controllers\Admin\Cms\Media\MediaController;
use App\Http\Controllers\Admin\Cms\Menu\Item\MenuItemController;
use App\Http\Controllers\Admin\Cms\Menu\MenuController;
use App\Http\Controllers\Admin\Cms\NewsAndUpdates\NewsAndUpdatesController;
use App\Http\Controllers\Admin\Cms\Notice\NoticeController;
use App\Http\Controllers\Admin\Cms\Page\PageController;
use App\Http\Controllers\Admin\Cms\Partner\PartnerController;
use App\Http\Controllers\Admin\Cms\Popup\PopupController;
use App\Http\Controllers\Admin\Cms\Service\ServiceController;
use App\Http\Controllers\Admin\Cms\SiteSetting\PaymentGateway\PaymentGatewaySettingController;
use App\Http\Controllers\Admin\Cms\SiteSetting\SiteSettingController;
use App\Http\Controllers\Admin\Cms\Slider\SliderController;
use App\Http\Controllers\Admin\Cms\Slider\Type\SliderTypeController;
use App\Http\Controllers\Admin\Cms\Team\TeamController;
use App\Http\Controllers\Admin\Cms\Testimonial\TestimonialController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\Location\CityController;
use App\Http\Controllers\Admin\Location\CountryController;
use App\Http\Controllers\Admin\Location\StateController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Unauthenticated Admin Routes
Route::group(['prefix' => 'admin'], function ($route) {
    $route->post('check/verification-enabled', [MFAController::class, 'checkVerificationEnabled']);
    $route->post('reset/password', [LoginController::class, 'resetPassword']);
    $route->post('do-reset/password', [LoginController::class, 'doResetPassword']);
    $route->post('request/verification-code', [MFAController::class, 'requestEmailVerificationCode']);
    $route->post('verify/mfa-verification-code', [MFAController::class, 'verifyMfaVerificationCode']);
    $route->post('verify/email-verification-code', [MFAController::class, 'verifyEmailVerificationCode']);
    $route->post('login', [LoginController::class, 'login']);

    // Web views for Auth
    $route->get('register', [RegisteredAdminController::class, 'create'])->name('admin.register');
    $route->post('register', [RegisteredAdminController::class, 'store']);
    $route->get('login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    $route->get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('admin.password.request');
    $route->post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('admin.password.email');
    $route->get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('admin.password.reset');
    $route->post('reset-password', [NewPasswordController::class, 'store'])->name('admin.password.update.reset');
    $route->get('mfa/verify', [AuthenticatedSessionController::class, 'showMfa'])->name('admin.mfa.verify');
    $route->post('mfa/check-verification', [MFAController::class, 'checkVerification'])->name('admin.mfa.check');
    $route->post('mfa/verify-code', [MFAController::class, 'verifyCode'])->name('admin.mfa.verify-code');
    $route->post('mfa/resend-code', [MFAController::class, 'resendCode'])->name('admin.mfa.resend-code');
});

// Authenticated Admin Routes
Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function ($route) {
    $route->get('do-verify', [LoginController::class, 'verify']);
    $route->get('dashboard', [AuthenticatedSessionController::class, 'dashboard'])->name('admin.dashboard');
    $route->get('account/security', [ProfileController::class, 'security'])->name('admin.account-security');
    $route->post('theme-style', [ProfileController::class, 'updateThemeStyle'])->name('admin.theme-style');
    $route->post('dashboard/stats', [DashboardController::class, 'getStats']);
    $route->get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    // Activity Logs
    $route->get('activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    $route->get('activity-logs/list', [ActivityLogController::class, 'data'])->name('admin.activity-logs.list');
    $route->get('activity-logs/owner/{ownerId}', [ActivityLogController::class, 'getByOwner'])->name('admin.activity-logs.by-owner');
    $route->get('owner/{ownerId}/activity-logs', [ActivityLogController::class, 'getByOwner'])->name('admin.owner.activity-logs');
    $route->get('activity-logs/customer/{customerId}', [ActivityLogController::class, 'getByCustomer'])->name('admin.activity-logs.by-customer');
    $route->get('customer/{customerId}/activity-logs', [ActivityLogController::class, 'getByCustomer'])->name('admin.customer.activity-logs');

    // Email Logs
    $route->get('email-logs', [EmailLogController::class, 'index'])->name('admin.email-logs.index');
    $route->get('email-logs/list', [EmailLogController::class, 'data'])->name('admin.email-logs.list');
    $route->get('email-logs/owner/{ownerId}', [EmailLogController::class, 'getByOwner'])->name('admin.email-logs.by-owner');
    $route->get('owner/{ownerId}/email-logs', [EmailLogController::class, 'getByOwner'])->name('admin.owner.email-logs');
    $route->get('email-logs/customer/{customerId}', [EmailLogController::class, 'getByCustomer'])->name('admin.email-logs.by-customer');
    $route->get('customer/{customerId}/email-logs', [EmailLogController::class, 'getByCustomer'])->name('admin.customer.email-logs');
    $route->get('email-logs/{emailLog}', [EmailLogController::class, 'show'])->name('admin.email-logs.show');
    $route->get('email-logs/{emailLog}/preview', [EmailLogController::class, 'preview'])->name('admin.email-logs.preview');
    $route->delete('email-logs/{emailLog}', [EmailLogController::class, 'destroy'])->name('admin.email-logs.destroy');

    // Admin User & Profile
    $route->get('profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    $route->get('security', [ProfileController::class, 'security'])->name('admin.security');
    $route->patch('profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    $route->patch('password', [ProfileController::class, 'updatePassword'])->name('admin.password.update');
    $route->delete('profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
    $route->get('admin-user/get/profile', [AdminUserController::class, 'profile']);
    $route->post('admin-user/update/profile', [AdminUserController::class, 'updateProfile']);
    $route->resource('admin-user', AdminUserController::class);
    $route->get('admin-user/get/all/{userType}', [AdminUserController::class, 'getByUserType'])->name('admin-user.user-type');
    $route->post('admin-user/{id}/change-password', [AdminUserController::class, 'changePassword']);
    // MFA Management (Authenticated)
    $route->get('mfa-authenticator', [MFAController::class, 'getMfaAuthenticatorCode']);
    $route->post('mfa/generate', [MFAController::class, 'generate'])->name('admin.mfa.generate');
    $route->post('mfa/activate', [MFAController::class, 'activate'])->name('admin.mfa.activate');
    $route->post('mfa/deactivate', [MFAController::class, 'deactivate'])->name('admin.mfa.deactivate');
    $route->post('mfa/email/activate', [MFAController::class, 'activateEmailAuthenticator'])->name('admin.mfa.email.activate');
    $route->post('mfa/email/deactivate', [MFAController::class, 'deactivateEmailAuthenticator'])->name('admin.mfa.email.deactivate');
    $route->post('activate/email-authenticator', [MFAController::class, 'activateEmailAuthenticator']);
    $route->post('activate/mfa-authenticator', [MFAController::class, 'activateMfaAuthenticator']);
    $route->post('deactivate/mfa-authenticator', [MFAController::class, 'deactivateMfaAuthenticator']);
    $route->post('deactivate/email-authenticator', [MFAController::class, 'deactivateEmailAuthenticator']);

    // Email template
    $route->get('email-template/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email-template.preview');
    $route->get('email-template/preview', [EmailTemplateController::class, 'previewByType'])->name('admin.email-template.preview-by-type');
    $route->resource('email-template', EmailTemplateController::class);
    $route->resource('email-templates', EmailTemplateController::class);
    $route->post('email-template/clone', [EmailTemplateController::class, 'cloneEmailTemplate']);
    $route->get('email-template-roles', [EmailTemplateController::class, 'emailTemplateRoles']);

    // Master CMS Suite View Route
    $route->get('cms', function (\Illuminate\Http\Request $request) {
        return \Inertia\Inertia::render('admin/cms/Index', [
            'initialModule' => $request->query('module', 'faqs'),
        ]);
    })->name('admin.cms');

    // Album
    $route->post('album/sort', [AlbumController::class, 'sort']);
    $route->post('album/{id}/update', [AlbumController::class, 'update']);
    $route->get('album/active/all', [AlbumController::class, 'activeAll']);
    $route->post('album/bulk-store', [AlbumController::class, 'bulkStore']);
    $route->apiResource('album', AlbumController::class);

    // Album Tag / Value
    $route->post('album/{album_id}/value/{value_id}/update', [AlbumValueController::class, 'update']);
    $route->post('album/{album_id}/value/sort', [AlbumValueController::class, 'sort']);
    $route->apiResource('album.value', AlbumValueController::class);

    // Partner
    $route->post('partner/{id}', [PartnerController::class, 'update']);
    $route->apiResource('partner', PartnerController::class);

    // Contact-us
    $route->apiResource('contact', ContactUsController::class);

    // Page
    $route->apiResource('page', PageController::class);

    // Faq Category
    $route->get('faq-category/parent/all', [FaqCategoryController::class, 'getParent']);
    $route->post('faq-category/sort', [FaqCategoryController::class, 'sort']);
    $route->apiResource('faq-category', FaqCategoryController::class);

    // Faq
    $route->post('faq/sort', [FaqController::class, 'sort']);
    $route->apiResource('faq', FaqController::class);

    // Blog
    $route->post('blog/{id}/update', [BlogController::class, 'update']);
    $route->apiResource('blog', BlogController::class);

    // News and updates
    $route->post('news-and-update/{id}/update', [NewsAndUpdatesController::class, 'update']);
    $route->apiResource('news-and-update', NewsAndUpdatesController::class);

    // Blog Category
    $route->get('blog-category/get/parent', [BlogCategoryController::class, 'parent']);
    $route->post('blog-category/{id}/update', [BlogCategoryController::class, 'update']);
    $route->apiResource('blog-category', BlogCategoryController::class);

    // Slider
    $route->post('slider/{id}/update', [SliderController::class, 'update']);
    $route->post('slider/sort', [SliderController::class, 'sort']);
    $route->apiResource('slider', SliderController::class);

    // Slider Type
    $route->apiResource('slider-type', SliderTypeController::class);
    $route->get('slider-type-active', [SliderTypeController::class, 'getActive']);

    // Testimonial
    $route->get('testimonial/list/view', [TestimonialController::class, 'indexView'])->name('admin.testimonial.indexView');
    $route->get('testimonial/{id}/edit', [TestimonialController::class, 'edit']);
    $route->post('testimonial/{id}/update', [TestimonialController::class, 'update']);
    $route->post('testimonial/sort', [TestimonialController::class, 'sort']);
    $route->apiResource('testimonial', TestimonialController::class);

    // Enquiry
    $route->apiResource('enquiry', EnquiryController::class);

    // Popup
    $route->post('popup/{id}', [PopupController::class, 'update']);
    $route->apiResource('popup', PopupController::class);

    // Notice
    $route->post('notice/sort', [NoticeController::class, 'sort']);
    $route->apiResource('notice', NoticeController::class);

    // Team
    $route->post('team/{id}', [TeamController::class, 'update']);
    $route->post('team/get/sort', [TeamController::class, 'sort']);
    $route->apiResource('team', TeamController::class);

    // Download Type
    $route->get('download-type/get/all', [DownloadTypeController::class, 'getAll']);
    $route->post('download-type/sort', [DownloadTypeController::class, 'sort']);
    $route->get('download-type-active', [DownloadTypeController::class, 'getActive']);
    $route->apiResource('download-type', DownloadTypeController::class);

    // Download
    $route->post('download/sort', [DownloadController::class, 'sort']);
    $route->post('download/{id}', [DownloadController::class, 'update']);
    $route->apiResource('download', DownloadController::class);

    // Media
    $route->apiResource('media', MediaController::class);

    // Menu
    $route->post('menu/sort', [MenuController::class, 'sort']);
    $route->apiResource('menu', MenuController::class);

    // Menu Item
    $route->post('menu/{id}/menu-item/sort', [MenuItemController::class, 'sort']);
    $route->apiResource('menu.menu-item', MenuItemController::class);

    // Attachment Type
    $route->apiResource('attachment-type', AttachmentTypeController::class);

    // Document Type
    $route->apiResource('document-type', DocumentTypeController::class);

    // Site setting
    $route->get('site-setting/get/all', [SiteSettingController::class, 'all']);
    $route->resource('site-setting', SiteSettingController::class);
    $route->post('site-setting/{id}/update', [SiteSettingController::class, 'update']);
    $route->post('site-setting/s3/test', [SiteSettingController::class, 'testAwsUpload']);
    $route->get('site-setting/test/s3', [SiteSettingController::class, 'testS3']);
    $route->post('site-setting/test-email', [SiteSettingController::class, 'sendTestEmail']);
    $route->get('site-setting/get/colors', [SiteSettingController::class, 'getSettingColors']);

    // Career
    $route->get('career/{id}/edit', [CareerController::class, 'edit']);
    $route->post('career/sort', [CareerController::class, 'sort']);
    $route->apiResource('career', CareerController::class);

    // Career Application
    $route->get('career/application/get/all', [CareerApplicationController::class, 'all']);
    $route->apiResource('career.application', CareerApplicationController::class);

    // Payment Gateway Setting & Options
    $route->get('option/{key}', [OptionController::class, 'getOptionByKey']);
    $route->apiResource('setting/payment-gateway', PaymentGatewaySettingController::class);

    // Service
    $route->post('service/{id}/update', [ServiceController::class, 'update']);
    $route->post('service/sort', [ServiceController::class, 'sort']);
    $route->apiResource('service', ServiceController::class);

    // Country, State, City
    $route->post('country/{id}/update', [CountryController::class, 'update']);
    $route->apiResource('country', CountryController::class);
    $route->post('state/{id}/update', [StateController::class, 'update']);
    $route->apiResource('state', StateController::class);
    $route->post('city/{id}/update', [CityController::class, 'update']);
    $route->apiResource('city', CityController::class);

    // Managing owners & Admin creating owner
    $route->get('owners', [OwnerController::class, 'index'])->name('admin.owner.index');
    $route->get('owners-list', [OwnerController::class, 'index'])->name('admin.owners');
    $route->post('owners', [AdminController::class, 'createOwner'])->name('admin.owner.store');
    $route->get('owners/{id}', [OwnerController::class, 'show'])->name('admin.owner.show');
    $route->get('owner/edit/{id}', [OwnerController::class, 'edit'])->name('admin.owner.edit');
    $route->get('owner/view/{id}', [OwnerController::class, 'view'])->name('admin.owner.view');
    $route->delete('owner/delete/{id}', [OwnerController::class, 'destroy'])->name('admin.owner.delete');
    $route->delete('owners/{id}', [OwnerController::class, 'destroy'])->name('admin.owner.destroy');
    $route->patch('owner/update/{id}', [OwnerController::class, 'update'])->name('admin.owner.update');
    $route->patch('owners/{id}', [OwnerController::class, 'update'])->name('admin.owner.patch');
    $route->post('owners/{id}', [OwnerController::class, 'update'])->name('admin.owner.update.post');

    // Owner nested fleet cars & drivers
    $route->post('owners/{id}/cars', [OwnerController::class, 'storeCar'])->name('admin.owner.cars.store');
    $route->post('owners/{id}/cars/{carId}', [OwnerController::class, 'updateCar'])->name('admin.owner.cars.update');
    $route->patch('owners/{id}/cars/{carId}', [OwnerController::class, 'updateCar'])->name('admin.owner.cars.patch');
    $route->delete('owners/{id}/cars/{carId}', [OwnerController::class, 'destroyCar'])->name('admin.owner.cars.destroy');
    $route->post('owners/{id}/drivers', [OwnerController::class, 'storeDriver'])->name('admin.owner.drivers.store');
    $route->post('owners/{id}/drivers/{driverId}', [OwnerController::class, 'updateDriver'])->name('admin.owner.drivers.update');
    $route->patch('owners/{id}/drivers/{driverId}', [OwnerController::class, 'updateDriver'])->name('admin.owner.drivers.patch');
    $route->delete('owners/{id}/drivers/{driverId}', [OwnerController::class, 'destroyDriver'])->name('admin.owner.drivers.destroy');

    // Managing cars
    $route->get('cars', [AdminController::class, 'index'])->name('admin.cars.index');
    $route->get('cars-list', [AdminController::class, 'index'])->name('admin.cars-list');
    $route->get('cars/{id}', [AdminController::class, 'show'])->name('admin.cars.show');
    $route->patch('cars/{id}', [AdminController::class, 'updateCar'])->name('admin.cars.update');
    $route->post('cars/{id}', [AdminController::class, 'updateCar'])->name('admin.cars.update.post');
    $route->delete('cars/{id}', [AdminController::class, 'destroyCar'])->name('admin.cars.destroy');
    $route->patch('cars/{id}/verify', [AdminController::class, 'verifyCar'])->name('admin.cars.verify');
    $route->patch('cars/{id}/reject', [AdminController::class, 'rejectCar'])->name('admin.cars.reject');
    $route->get('booked-cars', [AdminController::class, 'viewBookedCars'])->name('admin.booked-cars');
    $route->get('calendar-events', [AdminController::class, 'getCalendarEvents'])->name('admin.calendar-events');

    // Managing drivers directory
    $route->get('drivers', [AdminController::class, 'viewDrivers'])->name('admin.drivers');
    $route->post('drivers', [AdminController::class, 'storeDriver'])->name('admin.drivers.store');
    $route->patch('drivers/{id}', [AdminController::class, 'updateDriver'])->name('admin.drivers.update');
    $route->post('drivers/{id}', [AdminController::class, 'updateDriver'])->name('admin.drivers.update.post');
    $route->delete('drivers/{id}', [AdminController::class, 'destroyDriver'])->name('admin.drivers.destroy');

    // Managing customers
    $route->get('customers', [AdminController::class, 'viewCustomers'])->name('admin.customers');
    $route->post('customers', [AdminController::class, 'createCustomer'])->name('admin.customer.store');
    $route->get('customers/{id}', [AdminController::class, 'showCustomer'])->name('admin.customer.show');
    $route->patch('customers/{id}', [AdminController::class, 'updateCustomer'])->name('admin.customer.update');
    $route->post('customers/{id}', [AdminController::class, 'updateCustomer'])->name('admin.customer.update.post');
    $route->delete('customers/{id}', [AdminController::class, 'destroy'])->name('admin.customer.destroy');

    // Customer nested bookings & payments
    $route->post('customers/{id}/bookings', [AdminController::class, 'storeCustomerBooking'])->name('admin.customer.bookings.store');
    $route->post('customers/{id}/bookings/{bookingId}', [AdminController::class, 'updateCustomerBooking'])->name('admin.customer.bookings.update');
    $route->patch('customers/{id}/bookings/{bookingId}', [AdminController::class, 'updateCustomerBooking'])->name('admin.customer.bookings.patch');
    $route->delete('customers/{id}/bookings/{bookingId}', [AdminController::class, 'destroyCustomerBooking'])->name('admin.customer.bookings.destroy');
    $route->post('customers/{id}/payments', [AdminController::class, 'storeCustomerPayment'])->name('admin.customer.payments.store');
    $route->delete('customers/{id}/payments/{paymentId}', [AdminController::class, 'destroyCustomerPayment'])->name('admin.customer.payments.destroy');

    // Managing bookings
    $route->get('booked-cars', [AdminController::class, 'viewBookings'])->name('admin.booked-cars');
    $route->get('bookings', [AdminController::class, 'viewBookings'])->name('admin.bookings');
    $route->post('bookings/{id}/confirm', [AdminController::class, 'confirmBooking'])->name('admin.booking.confirm');
    $route->post('bookings/{id}/cancel', [AdminController::class, 'cancelBooking'])->name('admin.booking.cancel');
    $route->delete('bookings/{id}', [AdminController::class, 'destroyBooking'])->name('admin.booking.destroy');
});
