<?php

namespace App\Providers;

use App\Repositories\Admin\ActivityLogRepository;
use App\Repositories\Admin\ActivityLogRepositoryInterface;
use App\Repositories\Admin\AdminUserRepository;
use App\Repositories\Admin\AdminUserRepositoryInterface;
use App\Repositories\Admin\EmailLogRepository;
use App\Repositories\Admin\EmailLogRepositoryInterface;
use App\Repositories\BookingRepository;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\CarRepository;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\Cms\AlbumRepository;
use App\Repositories\Cms\AlbumRepositoryInterface;
use App\Repositories\Cms\BlogRepository;
use App\Repositories\Cms\BlogRepositoryInterface;
use App\Repositories\Cms\CareerRepository;
use App\Repositories\Cms\CareerRepositoryInterface;
use App\Repositories\Cms\ContactUsRepository;
use App\Repositories\Cms\ContactUsRepositoryInterface;
use App\Repositories\Cms\DownloadRepository;
use App\Repositories\Cms\DownloadRepositoryInterface;
use App\Repositories\Cms\EnquiryRepository;
use App\Repositories\Cms\EnquiryRepositoryInterface;
use App\Repositories\Cms\FaqRepository;
use App\Repositories\Cms\FaqRepositoryInterface;
use App\Repositories\Cms\MediaRepository;
use App\Repositories\Cms\MediaRepositoryInterface;
use App\Repositories\Cms\MenuRepository;
use App\Repositories\Cms\MenuRepositoryInterface;
use App\Repositories\Cms\NewsAndUpdatesRepository;
use App\Repositories\Cms\NewsAndUpdatesRepositoryInterface;
use App\Repositories\Cms\NoticeRepository;
use App\Repositories\Cms\NoticeRepositoryInterface;
use App\Repositories\Cms\PageRepository;
use App\Repositories\Cms\PageRepositoryInterface;
use App\Repositories\Cms\PartnerRepository;
use App\Repositories\Cms\PartnerRepositoryInterface;
use App\Repositories\Cms\PopupRepository;
use App\Repositories\Cms\PopupRepositoryInterface;
use App\Repositories\Cms\ServiceRepository;
use App\Repositories\Cms\ServiceRepositoryInterface;
use App\Repositories\Cms\SiteSettingRepository;
use App\Repositories\Cms\SiteSettingRepositoryInterface;
use App\Repositories\Cms\SliderRepository;
use App\Repositories\Cms\SliderRepositoryInterface;
use App\Repositories\Cms\TeamRepository;
use App\Repositories\Cms\TeamRepositoryInterface;
use App\Repositories\Cms\TestimonialRepository;
use App\Repositories\Cms\TestimonialRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\DriverRepository;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\Option\OptionRepository;
use App\Repositories\Option\OptionRepositoryInterface;
use App\Repositories\OwnerRepository;
use App\Repositories\OwnerRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryInterface;
use App\Services\AI\GroqService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Core Fleet & Booking Repositories
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(OwnerRepositoryInterface::class, OwnerRepository::class);

        // CMS Repositories
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(CareerRepositoryInterface::class, CareerRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
        $this->app->bind(TestimonialRepositoryInterface::class, TestimonialRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(NoticeRepositoryInterface::class, NoticeRepository::class);
        $this->app->bind(NewsAndUpdatesRepositoryInterface::class, NewsAndUpdatesRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(PopupRepositoryInterface::class, PopupRepository::class);
        $this->app->bind(AlbumRepositoryInterface::class, AlbumRepository::class);
        $this->app->bind(ContactUsRepositoryInterface::class, ContactUsRepository::class);
        $this->app->bind(EnquiryRepositoryInterface::class, EnquiryRepository::class);
        $this->app->bind(DownloadRepositoryInterface::class, DownloadRepository::class);
        $this->app->bind(SiteSettingRepositoryInterface::class, SiteSettingRepository::class);

        // Admin & System Repositories
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(EmailLogRepositoryInterface::class, EmailLogRepository::class);
        $this->app->bind(AdminUserRepositoryInterface::class, AdminUserRepository::class);
        $this->app->bind(OptionRepositoryInterface::class, OptionRepository::class);

        $this->app->singleton(GroqService::class, function () {
            return new GroqService;
        });
    }

    public function boot(): void
    {
        //
    }
}
