<?php

namespace App\Services\Admin;

use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Owner;
use App\Models\EmailTemplate\EmailTemplate;
use Illuminate\Support\Facades\Cache;

class AdminCountCacheService
{
    public const CACHE_TTL_SECONDS = 300; // 5 minutes

    public const KEY_DASHBOARD_STATS = 'admin_dashboard_counts';
    public const KEY_SHARED_COUNTS = 'admin_shared_counts';
    public const KEY_CMS_STATS = 'admin_cms_stats_counts';

    /**
     * Get shared admin counts for sidebar/header (cached for 5 minutes).
     */
    public static function getSharedCounts(): array
    {
        return Cache::remember(self::KEY_SHARED_COUNTS, self::CACHE_TTL_SECONDS, function () {
            return [
                'totalCars' => Car::count(),
                'pendingCars' => Car::where('status', 'pending')->orWhereNull('status')->count(),
                'totalDrivers' => Driver::count(),
                'totalBookings' => BookingCar::count(),
                'pendingBookings' => BookingCar::where('status', 'pending')->count(),
                'totalOwners' => Owner::count(),
                'totalCustomers' => Customer::count(),
                'totalCms' => 16,
                'totalEmailTemplates' => EmailTemplate::count(),
            ];
        });
    }

    /**
     * Get admin dashboard summary statistics (cached for 5 minutes).
     */
    public static function getDashboardStats(): array
    {
        return Cache::remember(self::KEY_DASHBOARD_STATS, self::CACHE_TTL_SECONDS, function () {
            return [
                'totalCars' => Car::count(),
                'pendingCarsCount' => Car::where('status', 'pending')->orWhereNull('status')->count(),
                'verifiedCarsCount' => Car::whereIn('status', ['verified', 'available'])->count(),
                'rejectedCarsCount' => Car::where('status', 'rejected')->count(),
                'totalOwners' => Owner::count(),
                'totalCustomers' => Customer::count(),
                'totalDrivers' => Driver::count(),
                'totalBookings' => BookingCar::count(),
                'confirmedBookings' => BookingCar::whereIn('status', ['confirm', 'confirmed'])->count(),
                'pendingBookings' => BookingCar::where('status', 'pending')->count(),
                'totalRevenue' => BookingCar::whereIn('status', ['confirm', 'confirmed'])->sum('total_price') ?: 0,
            ];
        });
    }

    /**
     * Get CMS module row counts (cached for 5 minutes).
     */
    public static function getCmsStats(): array
    {
        return Cache::remember(self::KEY_CMS_STATS, self::CACHE_TTL_SECONDS, function () {
            return [
                'faqs' => \App\Models\Cms\Faq\Faq::count(),
                'blogs' => \App\Models\Cms\Blog\Blog::count(),
                'services' => \App\Models\Cms\Service\Services::count(),
                'teams' => \App\Models\Cms\Team\Team::count(),
                'testimonials' => \App\Models\Cms\Testimonial\Testimonial::count(),
                'notices' => \App\Models\Cms\Notice\Notice::count(),
                'sliders' => \App\Models\Cms\Slider\Slider::count(),
                'popups' => \App\Models\Cms\Popup\Popup::count(),
                'pages' => \App\Models\Cms\Page\Page::count(),
                'partners' => \App\Models\Cms\Partner\Partner::count(),
                'careers' => \App\Models\Cms\Career\Career::count(),
                'enquiries' => \App\Models\Cms\Enquiry\Enquiry::count(),
                'contacts' => \App\Models\Cms\ContactUs\ContactUs::count(),
                'albums' => \App\Models\Cms\Album\Album::count(),
                'menus' => \App\Models\Cms\Menu\Menu::count(),
                'news' => \App\Models\Cms\NewsAndUpdates\NewsAndUpdates::count(),
            ];
        });
    }

    /**
     * Flush all cached counts immediately.
     */
    public static function clear(): void
    {
        Cache::forget(self::KEY_DASHBOARD_STATS);
        Cache::forget(self::KEY_SHARED_COUNTS);
        Cache::forget(self::KEY_CMS_STATS);
        Cache::forget('site_settings');
        Cache::forget('site_setting_cache');
    }
}
