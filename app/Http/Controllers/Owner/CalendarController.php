<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Display the Owner's vehicle reservation and availability calendar.
     */
    public function index(Request $request, $carId = null)
    {
        $ownerId = Auth::guard('owner')->id();

        // Retrieve only vehicles owned by this owner
        $cars = Car::where('owner_id', $ownerId)
            ->select(['id', 'car_name', 'car_model', 'car_number', 'car_price_per_day', 'car_photo', 'status', 'available'])
            ->get();

        $carIds = $cars->pluck('id')->toArray();

        // Selected car validation
        $selectedCarId = null;
        if ($carId && in_array((int) $carId, $carIds)) {
            $selectedCarId = (int) $carId;
        }

        // Query bookings strictly for this owner's cars
        $bookingsQuery = BookingCar::with([
            'car:id,car_name,car_model,car_number,car_photo,car_price_per_day,owner_id',
            'customer:id,first_name,last_name,name,email,mobile,phone_number',
        ])
            ->whereIn('car_id', $carIds)
            ->whereIn('status', ['confirmed', 'pending', 'completed']);

        if ($selectedCarId) {
            $bookingsQuery->where('car_id', $selectedCarId);
        }

        $bookings = $bookingsQuery->get();

        // Monthly stats scoped to this owner
        $thisMonthBookings = empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $stats = [
            'totalCars' => $cars->count(),
            'thisMonthBookings' => $thisMonthBookings,
            'activeBookings' => $bookings->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'carId' => $selectedCarId,
                    'cars' => $cars,
                    'bookings' => $bookings,
                    'stats' => $stats,
                ],
            ]);
        }

        return Inertia::render('owner/Calendar', [
            'carId' => $selectedCarId,
            'cars' => $cars,
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }
}
