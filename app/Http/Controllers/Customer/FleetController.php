<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\BookingCar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FleetController extends Controller
{
    /**
     * Display Customer Portal Fleet Showroom.
     */
    public function index(Request $request)
    {
        $query = Car::with([
            'owner',
            'driver',
            'booking' => function ($q) {
                $q->whereIn('status', ['confirm', 'booked', 'pending', 'reserved'])
                  ->select('id', 'car_id', 'pick_up_date', 'last_date', 'status');
            },
        ])
        ->where(function ($q) {
            $q->whereIn('status', ['verified', 'available', 'active', 'approved', 'pending'])
              ->orWhere('available', 'yes')
              ->orWhereNull('status');
        });

        // Search query
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                  ->orWhere('car_model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where(function ($q) use ($category) {
                    $q->where('car_name', 'like', "%{$category}%")
                      ->orWhere('car_model', 'like', "%{$category}%")
                      ->orWhere('brand', 'like', "%{$category}%")
                      ->orWhere('model', 'like', "%{$category}%")
                      ->orWhere('description', 'like', "%{$category}%");
                });
            }
        }

        // Filter by min / max price
        if ($minPrice = $request->input('min_price')) {
            $query->where('car_price_per_day', '>=', (float)$minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('car_price_per_day', '<=', (float)$maxPrice);
        }

        // Filter by seats
        if ($seats = $request->input('seats')) {
            $query->where('number_of_seats', '>=', (int)$seats);
        }

        $perPage = (int) $request->input('per_page', 9);
        $cars = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return Inertia::render('customer/cars/Index', [
            'cars' => $cars,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', 'all'),
                'seats' => $request->input('seats', ''),
                'min_price' => $request->input('min_price', ''),
                'max_price' => $request->input('max_price', ''),
                'per_page' => $perPage,
            ],
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
        $car = Car::with(['owner', 'driver'])->findOrFail($id);

        $bookedRanges = BookingCar::where('car_id', $car->id)
            ->whereIn('status', ['confirm', 'booked', 'pending', 'reserved'])
            ->get(['pick_up_date', 'last_date', 'status'])
            ->map(function ($b) {
                return [
                    'start' => substr($b->pick_up_date, 0, 10),
                    'end' => substr($b->last_date, 0, 10),
                    'status' => $b->status,
                ];
            });

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
        $cars = Car::with(['owner', 'driver'])
            ->where(function ($q) {
                $q->whereIn('status', ['verified', 'available', 'active', 'approved', 'pending'])
                  ->orWhere('available', 'yes')
                  ->orWhereNull('status');
            })
            ->get();

        $bookings = BookingCar::with(['car.driver', 'car.owner'])
            ->whereIn('status', ['confirm', 'booked', 'pending'])
            ->orderBy('pick_up_date', 'asc')
            ->get();

        return Inertia::render('customer/Calendar', [
            'cars' => $cars,
            'bookings' => $bookings,
        ]);
    }
}
