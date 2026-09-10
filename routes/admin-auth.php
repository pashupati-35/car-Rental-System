<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\MFAController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredAdminController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CarController;
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
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DriverController;
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

// Authenticated Admin Action & API Routes
Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function ($route) {
    $route->get('do-verify', [LoginController::class, 'verify']);
    $route->post('theme-style', [ProfileController::class, 'updateThemeStyle'])->name('admin.theme-style');
    $route->post('dashboard/stats', [DashboardController::class, 'getStats']);
    $route->get('logout', [LoginController::class, 'logout'])->name('admin.logout');

    // Activity Logs API
    $route->get('activity-logs/list', [ActivityLogController::class, 'data'])->name('admin.activity-logs.list');
    $route->get('activity-logs/owner/{ownerId}', [ActivityLogController::class, 'getByOwner'])->name('admin.activity-logs.by-owner');
    $route->get('activity-logs/customer/{customerId}', [ActivityLogController::class, 'getByCustomer'])->name('admin.activity-logs.by-customer');

    // Email Logs API
    $route->get('email-logs/list', [EmailLogController::class, 'data'])->name('admin.email-logs.list');
    $route->get('email-logs/owner/{ownerId}', [EmailLogController::class, 'getByOwner'])->name('admin.email-logs.by-owner');
    $route->get('email-logs/customer/{customerId}', [EmailLogController::class, 'getByCustomer'])->name('admin.email-logs.by-customer');
    $route->get('email-logs/{emailLog}', [EmailLogController::class, 'show'])->name('admin.email-logs.show');
    $route->get('email-logs/{emailLog}/preview', [EmailLogController::class, 'preview'])->name('admin.email-logs.preview');
    $route->delete('email-logs/{emailLog}', [EmailLogController::class, 'destroy'])->name('admin.email-logs.destroy');

    // Admin User & Profile Actions
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

    // Email template
    $route->get('email-template/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email-template.preview');
    $route->get('email-template/preview', [EmailTemplateController::class, 'previewByType'])->name('admin.email-template.preview-by-type');
    $route->resource('email-templates', EmailTemplateController::class)->except(['index', 'edit']);
    $route->post('email-template/clone', [EmailTemplateController::class, 'cloneEmailTemplate']);
    $route->get('email-template-roles', [EmailTemplateController::class, 'emailTemplateRoles']);

    // CMS - Album & Value
    $route->post('album/sort', [AlbumController::class, 'sort']);
    $route->post('album/{id}/update', [AlbumController::class, 'update']);
    $route->get('album/active/all', [AlbumController::class, 'activeAll']);
    $route->post('album/bulk-store', [AlbumController::class, 'bulkStore']);
    $route->apiResource('album', AlbumController::class);
    $route->post('album/{album_id}/value/{value_id}/update', [AlbumValueController::class, 'update']);
    $route->post('album/{album_id}/value/sort', [AlbumValueController::class, 'sort']);
    $route->apiResource('album.value', AlbumValueController::class);

    // CMS - Partner, Contact, Page
    $route->post('partner/{id}', [PartnerController::class, 'update']);
    $route->apiResource('partner', PartnerController::class);
    $route->apiResource('contact', ContactUsController::class);
    $route->apiResource('page', PageController::class);

    // CMS - Faq & Category
    $route->get('faq-category/parent/all', [FaqCategoryController::class, 'getParent']);
    $route->post('faq-category/sort', [FaqCategoryController::class, 'sort']);
    $route->apiResource('faq-category', FaqCategoryController::class);
    $route->post('faq/sort', [FaqController::class, 'sort']);
    $route->apiResource('faq', FaqController::class);

    // CMS - Blog & Category
    $route->post('blog/{id}/update', [BlogController::class, 'update']);
    $route->apiResource('blog', BlogController::class);
    $route->get('blog-category/get/parent', [BlogCategoryController::class, 'parent']);
    $route->post('blog-category/{id}/update', [BlogCategoryController::class, 'update']);
    $route->apiResource('blog-category', BlogCategoryController::class);

    // CMS - News and updates
    $route->post('news-and-update/{id}/update', [NewsAndUpdatesController::class, 'update']);
    $route->apiResource('news-and-update', NewsAndUpdatesController::class);

    // CMS - Slider & Type
    $route->post('slider/{id}/update', [SliderController::class, 'update']);
    $route->post('slider/sort', [SliderController::class, 'sort']);
    $route->apiResource('slider', SliderController::class);
    $route->apiResource('slider-type', SliderTypeController::class);
    $route->get('slider-type-active', [SliderTypeController::class, 'getActive']);

    // CMS - Testimonial & Enquiry
    $route->get('testimonial/list/view', [TestimonialController::class, 'indexView'])->name('admin.testimonial.indexView');
    $route->get('testimonial/{id}/edit', [TestimonialController::class, 'edit']);
    $route->post('testimonial/{id}/update', [TestimonialController::class, 'update']);
    $route->post('testimonial/sort', [TestimonialController::class, 'sort']);
    $route->apiResource('testimonial', TestimonialController::class);
    $route->apiResource('enquiry', EnquiryController::class);

    // CMS - Popup, Notice, Team
    $route->post('popup/{id}', [PopupController::class, 'update']);
    $route->apiResource('popup', PopupController::class);
    $route->post('notice/sort', [NoticeController::class, 'sort']);
    $route->apiResource('notice', NoticeController::class);
    $route->post('team/{id}', [TeamController::class, 'update']);
    $route->post('team/get/sort', [TeamController::class, 'sort']);
    $route->apiResource('team', TeamController::class);

    // CMS - Download & Type
    $route->get('download-type/get/all', [DownloadTypeController::class, 'getAll']);
    $route->post('download-type/sort', [DownloadTypeController::class, 'sort']);
    $route->get('download-type-active', [DownloadTypeController::class, 'getActive']);
    $route->apiResource('download-type', DownloadTypeController::class);
    $route->post('download/sort', [DownloadController::class, 'sort']);
    $route->post('download/{id}', [DownloadController::class, 'update']);
    $route->apiResource('download', DownloadController::class);

    // CMS - Media, Menu
    $route->apiResource('media', MediaController::class);
    $route->post('menu/sort', [MenuController::class, 'sort']);
    $route->apiResource('menu', MenuController::class);
    $route->post('menu/{id}/menu-item/sort', [MenuItemController::class, 'sort']);
    $route->apiResource('menu.menu-item', MenuItemController::class);

    // CMS - Attachments & Documents
    $route->apiResource('attachment-type', AttachmentTypeController::class);
    $route->apiResource('document-type', DocumentTypeController::class);

    // CMS - Site setting
    $route->get('site-setting/get/all', [SiteSettingController::class, 'all']);
    $route->resource('site-setting', SiteSettingController::class);
    $route->post('site-setting/{id}/update', [SiteSettingController::class, 'update']);
    $route->post('site-setting/s3/test', [SiteSettingController::class, 'testAwsUpload']);
    $route->get('site-setting/test/s3', [SiteSettingController::class, 'testS3']);
    $route->post('site-setting/test-email', [SiteSettingController::class, 'sendTestEmail']);
    $route->get('site-setting/get/colors', [SiteSettingController::class, 'getSettingColors']);

    // CMS - Career & Applications
    $route->get('career/{id}/edit', [CareerController::class, 'edit']);
    $route->post('career/sort', [CareerController::class, 'sort']);
    $route->apiResource('career', CareerController::class);
    $route->get('career/application/get/all', [CareerApplicationController::class, 'all']);
    $route->apiResource('career.application', CareerApplicationController::class);

    // CMS - Payment Gateway & Options
    $route->get('option/{key}', [OptionController::class, 'getOptionByKey']);
    $route->apiResource('setting/payment-gateway', PaymentGatewaySettingController::class);

    // CMS - Service
    $route->post('service/{id}/update', [ServiceController::class, 'update']);
    $route->post('service/sort', [ServiceController::class, 'sort']);
    $route->apiResource('service', ServiceController::class);

    // Location - Country, State, City
    $route->post('country/{id}/update', [CountryController::class, 'update']);
    $route->apiResource('country', CountryController::class);
    $route->post('state/{id}/update', [StateController::class, 'update']);
    $route->apiResource('state', StateController::class);
    $route->post('city/{id}/update', [CityController::class, 'update']);
    $route->apiResource('city', CityController::class);

    // ==========================================
    // Fleet Owners Management (OwnerController)
    // ==========================================
    $route->get('api/owners', [OwnerController::class, 'index'])->name('admin.api.owners');
    $route->post('owners', [OwnerController::class, 'store'])->name('admin.owner.store');
    $route->get('api/owners/{id}', [OwnerController::class, 'show'])->name('admin.api.owner.show');
    $route->match(['put', 'patch', 'post'], 'owners/{id}', [OwnerController::class, 'update'])->name('admin.owner.update');
    $route->delete('owners/{id}', [OwnerController::class, 'destroy'])->name('admin.owner.destroy');
    $route->match(['get', 'post'], 'owners/{id}/login-as', [OwnerController::class, 'loginAs'])->name('admin.owner.login-as');

    // Owner Nested Fleet Cars & Drivers
    $route->post('owners/{id}/cars', [OwnerController::class, 'storeCar'])->name('admin.owner.cars.store');
    $route->match(['put', 'patch', 'post'], 'owners/{id}/cars/{carId}', [OwnerController::class, 'updateCar'])->name('admin.owner.cars.update');
    $route->delete('owners/{id}/cars/{carId}', [OwnerController::class, 'destroyCar'])->name('admin.owner.cars.destroy');

    $route->post('owners/{id}/drivers', [OwnerController::class, 'storeDriver'])->name('admin.owner.drivers.store');
    $route->match(['put', 'patch', 'post'], 'owners/{id}/drivers/{driverId}', [OwnerController::class, 'updateDriver'])->name('admin.owner.drivers.update');
    $route->delete('owners/{id}/drivers/{driverId}', [OwnerController::class, 'destroyDriver'])->name('admin.owner.drivers.destroy');

    // ==========================================
    // Fleet Cars Management (CarController)
    // ==========================================
    $route->get('api/cars', [CarController::class, 'index'])->name('admin.api.cars');
    $route->get('api/cars/{id}', [CarController::class, 'show'])->name('admin.api.car.show');
    $route->match(['put', 'patch', 'post'], 'cars/{id}', [CarController::class, 'update'])->name('admin.cars.update');
    $route->delete('cars/{id}', [CarController::class, 'destroy'])->name('admin.cars.destroy');
    $route->patch('cars/{id}/verify', [CarController::class, 'verify'])->name('admin.cars.verify');
    $route->patch('cars/{id}/reject', [CarController::class, 'reject'])->name('admin.cars.reject');
    $route->get('calendar-events', [CarController::class, 'calendarEvents'])->name('admin.calendar-events');

    // ==========================================
    // Drivers Directory (DriverController)
    // ==========================================
    $route->get('api/drivers', [DriverController::class, 'index'])->name('admin.api.drivers');
    $route->post('drivers', [DriverController::class, 'store'])->name('admin.drivers.store');
    $route->get('api/drivers/{id}', [DriverController::class, 'show'])->name('admin.api.driver.show');
    $route->match(['put', 'patch', 'post'], 'drivers/{id}', [DriverController::class, 'update'])->name('admin.drivers.update');
    $route->delete('drivers/{id}', [DriverController::class, 'destroy'])->name('admin.drivers.destroy');

    // ==========================================
    // Customers Management (CustomerController)
    // ==========================================
    $route->get('api/customers', [CustomerController::class, 'index'])->name('admin.api.customers');
    $route->post('customers', [CustomerController::class, 'store'])->name('admin.customer.store');
    $route->get('api/customers/{id}', [CustomerController::class, 'show'])->name('admin.api.customer.show');
    $route->match(['put', 'patch', 'post'], 'customers/{id}', [CustomerController::class, 'update'])->name('admin.customer.update');
    $route->delete('customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customer.destroy');
    $route->match(['get', 'post'], 'customers/{id}/login-as', [CustomerController::class, 'loginAs'])->name('admin.customer.login-as');

    // Customer Nested Bookings & Payments
    $route->post('customers/{id}/bookings', [CustomerController::class, 'storeBooking'])->name('admin.customer.bookings.store');
    $route->match(['put', 'patch', 'post'], 'customers/{id}/bookings/{bookingId}', [CustomerController::class, 'updateBooking'])->name('admin.customer.bookings.update');
    $route->delete('customers/{id}/bookings/{bookingId}', [CustomerController::class, 'destroyBooking'])->name('admin.customer.bookings.destroy');
    $route->post('customers/{id}/payments', [CustomerController::class, 'storePayment'])->name('admin.customer.payments.store');
    $route->delete('customers/{id}/payments/{paymentId}', [CustomerController::class, 'destroyPayment'])->name('admin.customer.payments.destroy');

    // ==========================================
    // Bookings Management (BookingController)
    // ==========================================
    $route->get('api/bookings', [BookingController::class, 'index'])->name('admin.api.bookings');
    $route->post('bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('admin.booking.confirm');
    $route->post('bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('admin.booking.cancel');
    $route->delete('bookings/{id}', [BookingController::class, 'destroy'])->name('admin.booking.destroy');
});
