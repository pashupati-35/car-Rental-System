<?php

namespace App\Http\Controllers;

use App\Models\BookingCar;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::with(['owner:id,full_name,contact_number', 'driver:id,name,phone,license_number,experience_years,photo,status'])
            ->where('status', 'verified')
            ->where('available', 'yes')
            ->latest()
            ->get();

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
        $perPage = (int) $request->input('per_page', 9);
        $search = $request->input('search');
        $seats = $request->input('seats');
        $maxPrice = $request->input('max_price');
        $sortBy = $request->input('sort_by', 'latest');

        $query = Car::with(['owner:id,full_name,contact_number', 'driver:id,name,phone,license_number,experience_years,photo,status'])
            ->where('status', 'verified');

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('car_model', 'like', "%{$search}%")
                  ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        if (filled($seats)) {
            $query->where('number_of_seats', '>=', (int) $seats);
        }

        if (filled($maxPrice)) {
            $query->where('car_price_per_day', '<=', (float) $maxPrice);
        }

        if ($sortBy === 'price-low') {
            $query->orderBy('car_price_per_day', 'asc');
        } elseif ($sortBy === 'price-high') {
            $query->orderBy('car_price_per_day', 'desc');
        } else {
            $query->latest();
        }

        $cars = $query->paginate($perPage)->withQueryString();

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
        $car = Car::with([
            'owner:id,full_name,contact_number,email,address',
            'driver:id,name,phone,email,license_number,experience_years,photo,license_photo,status'
        ])->findOrFail($id);

        $bookings = BookingCar::where('car_id', $id)
            ->whereIn('status', ['confirm', 'booked', 'reserved'])
            ->where('last_date', '>=', Carbon::today()->format('Y-m-d'))
            ->get(['pick_up_date', 'last_date', 'status']);

        $disabledDates = [];
        foreach ($bookings as $b) {
            $curr = Carbon::parse($b->pick_up_date);
            $end = Carbon::parse($b->last_date);
            while ($curr <= $end) {
                $disabledDates[] = $curr->format('Y-m-d');
                $curr->addDay();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'car' => $car,
                    'disabled_dates' => array_values(array_unique($disabledDates)),
                ],
            ]);
        }

        return Inertia::render('cars/Show', [
            'car' => $car,
            'disabledDates' => array_values(array_unique($disabledDates)),
        ]);
    }

    public function showCalendar(Request $request, $id = null)
    {
        $bookings = BookingCar::with(['car:id,car_name,brand,car_model,car_number', 'customer:id,name,email,contact_number'])
            ->select('id', 'booking_id', 'car_id', 'customer_id', 'name', 'pick_up_date', 'last_date', 'status', 'total_price')
            ->when($id, fn($q) => $q->where('car_id', $id))
            ->whereIn('status', ['confirm', 'confirmed', 'booked', 'reserved', 'pending'])
            ->get();

        $cars = Car::select('id', 'car_name', 'brand', 'car_model', 'car_number', 'car_price_per_day', 'image')
            ->where('status', 'verified')
            ->get();

        return Inertia::render('Calendar', [
            'carId' => $id ? (int) $id : null,
            'cars' => $cars,
            'bookings' => $bookings,
        ]);
    }

    public function getBookingDates($id)
    {
        $bookings = BookingCar::where('car_id', $id)
            ->select('pick_up_date', 'last_date', 'status')
            ->whereIn('status', ['confirm', 'booked', 'reserved', 'pending'])
            ->get();

        $dates = [
            'booked' => [],
            'reserved' => []
        ];

        foreach ($bookings as $booking) {
            $currentDate = $booking->pick_up_date;
            while (strtotime($currentDate) <= strtotime($booking->last_date)) {
                if (in_array($booking->status, ['booked', 'confirm'])) {
                    $dates['booked'][] = $currentDate;
                } else {
                    $dates['reserved'][] = $currentDate;
                }
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $dates,
        ]);
    }

    public function getStats(Request $request)
    {
        $totalCars = Car::count();
        $totalBookings = BookingCar::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'totalCars' => $totalCars,
                'totalBookings' => $totalBookings,
            ],
        ]);
    }
}
