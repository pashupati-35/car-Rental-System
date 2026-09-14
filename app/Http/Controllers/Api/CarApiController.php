<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CarApiController extends Controller
{
    /**
     * Get list of public cars with owner & driver details and filters.
     */
    public function index(Request $request)
    {
        $query = Car::with(['owner:id,full_name,contact_number,email', 'driver:id,name,phone,email,license_number,experience_years,photo,status'])
            ->where('status', 'verified')
            ->where('available', 'yes');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('seats')) {
            $query->where('number_of_seats', '>=', (int) $request->input('seats'));
        }

        if ($request->filled('max_price')) {
            $query->where('car_price_per_day', '<=', (float) $request->input('max_price'));
        }

        $cars = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $cars,
        ]);
    }

    /**
     * Get detailed car information with owner, driver, and booked dates.
     */
    public function show($id)
    {
        $car = Car::with([
            'owner:id,full_name,contact_number,email,address',
            'driver:id,name,phone,email,license_number,experience_years,photo,license_photo,status',
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'car' => $car,
                'disabled_dates' => array_values(array_unique($disabledDates)),
                'existing_bookings' => $bookings,
            ],
        ]);
    }

    /**
     * Get booked and reserved dates for calendar picker.
     */
    public function bookedDates($id)
    {
        $bookings = BookingCar::where('car_id', $id)
            ->whereIn('status', ['confirm', 'booked', 'reserved', 'pending'])
            ->where('last_date', '>=', Carbon::today()->format('Y-m-d'))
            ->get(['pick_up_date', 'last_date', 'status']);

        $booked = [];
        $reserved = [];

        foreach ($bookings as $b) {
            $curr = Carbon::parse($b->pick_up_date);
            $end = Carbon::parse($b->last_date);
            while ($curr <= $end) {
                $formatted = $curr->format('Y-m-d');
                if (in_array($b->status, ['confirm', 'booked'])) {
                    $booked[] = $formatted;
                } else {
                    $reserved[] = $formatted;
                }
                $curr->addDay();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'booked' => array_values(array_unique($booked)),
                'reserved' => array_values(array_unique($reserved)),
            ],
        ]);
    }
}
