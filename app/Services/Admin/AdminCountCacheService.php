<?php

namespace App\Services\Admin;

use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Cms\Album\Album;
use App\Models\Cms\Blog\Blog;
use App\Models\Cms\Career\Career;
use App\Models\Cms\ContactUs\ContactUs;
use App\Models\Cms\Enquiry\Enquiry;
use App\Models\Cms\Faq\Faq;
use App\Models\Cms\Menu\Menu;
use App\Models\Cms\NewsAndUpdates\NewsAndUpdates;
use App\Models\Cms\Notice\Notice;
use App\Models\Cms\Page\Page;
use App\Models\Cms\Partner\Partner;
use App\Models\Cms\Popup\Popup;
use App\Models\Cms\Service\Services;
use App\Models\Cms\Slider\Slider;
use App\Models\Cms\Team\Team;
use App\Models\Cms\Testimonial\Testimonial;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\EmailTemplate\EmailTemplate;
use App\Models\Owner;
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
                'faqs' => Faq::count(),
                'blogs' => Blog::count(),
                'services' => Services::count(),
                'teams' => Team::count(),
                'testimonials' => Testimonial::count(),
                'notices' => Notice::count(),
                'sliders' => Slider::count(),
                'popups' => Popup::count(),
                'pages' => Page::count(),
                'partners' => Partner::count(),
                'careers' => Career::count(),
                'enquiries' => Enquiry::count(),
                'contacts' => ContactUs::count(),
                'albums' => Album::count(),
                'menus' => Menu::count(),
                'news' => NewsAndUpdates::count(),
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
