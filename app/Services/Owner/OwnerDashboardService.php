<?php

namespace App\Services\Owner;

use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerDashboardService
{
    /**
     * Get aggregate KPI statistics strictly scoped to the specified owner.
     */
    public static function getDashboardStats(int $ownerId): array
    {
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();

        $totalCars = Car::where('owner_id', $ownerId)->count();
        $verifiedCars = Car::where('owner_id', $ownerId)->whereIn('status', ['approved', 'verified', 'active'])->count();
        $pendingCars = Car::where('owner_id', $ownerId)->where(function ($q) {
            $q->whereIn('status', ['pending', 'pending_verification', 'under_review'])
                ->orWhereNull('status')
                ->orWhere('status', '');
        })->count();
        $rejectedCars = Car::where('owner_id', $ownerId)->whereIn('status', ['rejected', 'declined', 'disapproved'])->count();

        $totalDrivers = Driver::where('owner_id', $ownerId)->count();
        $activeDrivers = Driver::where('owner_id', $ownerId)->where('status', 'active')->count();

        $totalBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->count();
        $pendingBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'pending')->count();
        $confirmedBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'confirmed')->count();
        $completedBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'completed')->count();
        $cancelledBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'cancelled')->count();

        $totalRevenue = empty($carIds) ? 0.0 : (float) BookingCar::whereIn('car_id', $carIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        $thisMonthRevenue = empty($carIds) ? 0.0 : (float) BookingCar::whereIn('car_id', $carIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        return [
            'totalCars' => $totalCars,
            'verifiedCars' => $verifiedCars,
            'pendingCars' => $pendingCars,
            'rejectedCars' => $rejectedCars,
            'totalDrivers' => $totalDrivers,
            'activeDrivers' => $activeDrivers,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'confirmedBookings' => $confirmedBookings,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings,
            'totalRevenue' => round($totalRevenue, 2),
            'thisMonthRevenue' => round($thisMonthRevenue, 2),
        ];
    }

    /**
     * Get recent reservations for this owner's vehicles.
     */
    public static function getRecentBookings(int $ownerId, int $limit = 6)
    {
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        if (empty($carIds)) {
            return collect();
        }

        return BookingCar::with([
            'car:id,car_name,car_model,car_number,car_photo,car_price_per_day,owner_id',
            'customer:id,first_name,last_name,name,email,mobile,phone_number',
        ])
            ->whereIn('car_id', $carIds)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get recent cars owned by this owner.
     */
    public static function getRecentCars(int $ownerId, int $limit = 6)
    {
        return Car::with(['driver:id,name,phone,license_number,experience_years,status'])
            ->where('owner_id', $ownerId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get driver roster for this owner.
     */
    public static function getRecentDrivers(int $ownerId, int $limit = 6)
    {
        return Driver::where('owner_id', $ownerId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Monthly revenue & booking trend for the past 6 months.
     */
    public static function getRevenueTrends(int $ownerId): array
    {
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        $months = [];
        $revenueData = [];
        $bookingData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('M Y');
            $months[] = $monthKey;

            if (empty($carIds)) {
                $revenueData[] = 0;
                $bookingData[] = 0;
                continue;
            }

            $revenue = (float) BookingCar::whereIn('car_id', $carIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_price');

            $count = BookingCar::whereIn('car_id', $carIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $revenueData[] = round($revenue, 2);
            $bookingData[] = $count;
        }

        return [
            'labels' => $months,
            'revenue' => $revenueData,
            'bookings' => $bookingData,
        ];
    }
}
