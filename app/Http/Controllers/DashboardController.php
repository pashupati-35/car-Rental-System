<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Services\CarService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected BookingService $bookingService,
    ) {}

    public function index(?Request $request = null)
    {
        $request = $request ?? request();
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 12);

        $cacheKey = "homepage_featured_cars_page_{$page}_per_{$perPage}";
        $cars = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($perPage) {
            return $this->carService->paginateCars([], $perPage);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $cars,
            ]);
        }

        return Inertia::render('Home', [
            'featuredCars' => $cars,
        ]);
    }

    public function carsIndex(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $search = $request->input('search');
        $seats = $request->input('seats');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort_by', 'latest');

        $filters = [
            'search' => $search,
            'seats' => $seats,
            'max_price' => $maxPrice,
            'sort_by' => $sortBy,
        ];

        $cars = $this->carService->paginateCars($filters, $perPage);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $cars,
            ]);
        }

        return Inertia::render('cars/Index', [
            'cars' => $cars,
            'filters' => [
                'search' => $search ?? '',
                'seats' => $seats ?? '',
                'max_price' => $maxPrice ?? '',
                'sort_by' => $sortBy,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function view(Request $request, $id)
    {
        $car = $this->carService->getCarDetails((int) $id);
        $disabledDates = $this->bookingService->getDisabledDatesForCar((int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'car' => $car,
                    'disabled_dates' => $disabledDates,
                ],
            ]);
        }

        return Inertia::render('cars/Show', [
            'car' => $car,
            'disabledDates' => $disabledDates,
        ]);
    }

    public function showCalendar(Request $request, $id = null)
    {
        $bookings = $this->bookingService->getCalendarBookings($id ? (int) $id : null);
        $cars = $this->carService->getCalendarCars();

        return Inertia::render('Calendar', [
            'carId' => $id ? (int) $id : null,
            'cars' => $cars,
            'bookings' => $bookings,
        ]);
    }

    public function getBookingDates($id)
    {
        $dates = $this->bookingService->getActiveBookingDates((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $dates,
        ]);
    }

    public function getStats(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'totalCars' => $this->carService->getTotalCarsCount(),
                'totalBookings' => $this->bookingService->getTotalBookingsCount(),
            ],
        ]);
    }
}
