<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CarService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FleetController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected BookingService $bookingService
    ) {}

    /**
     * Display Customer Portal Fleet Showroom.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search', ''),
            'category' => $request->input('category', 'all'),
            'seats' => $request->input('seats', ''),
            'min_price' => $request->input('min_price', ''),
            'max_price' => $request->input('max_price', ''),
        ];

        $perPage = (int) $request->input('per_page', 9);
        $cars = $this->carService->getCustomerFleetCars($filters, $perPage);

        return Inertia::render('customer/cars/Index', [
            'cars' => $cars,
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'stats' => [
                'totalAvailable' => $cars->total(),
                'avgRate' => 85,
            ],
        ]);
    }

    /**
     * Display Customer Portal Car Details & Booking Form.
     */
    public function show($id)
    {
        $car = $this->carService->getCustomerCarDetails((int) $id);
        $bookedRanges = $this->bookingService->getBookedRangesForCar((int) $id);

        return Inertia::render('customer/cars/Show', [
            'car' => $car,
            'bookedRanges' => $bookedRanges,
        ]);
    }

    /**
     * Display Customer Portal Availability Calendar.
     */
    public function calendar(Request $request)
    {
        $cars = $this->carService->getCustomerCalendarCars();
        $bookings = $this->bookingService->getCustomerCalendarBookings();

        return Inertia::render('customer/Calendar', [
            'cars' => $cars,
            'bookings' => $bookings,
        ]);
    }
}
