<?php

namespace App\Http\Controllers;

use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $cars = Car::where('available', 'yes')->orWhereNull('available')->get();
        return Inertia::render('Home', [
            'featuredCars' => $cars,
        ]);
    }

    public function carsIndex()
    {
        $cars = Car::all();
        return Inertia::render('cars/Index', [
            'cars' => $cars,
        ]);
    }

    public function view($id)
    {
        $car = Car::findOrFail($id);
        return Inertia::render('cars/Show', [
            'car' => $car,
        ]);
    }

    public function showCalendar($id = null)
    {
        $bookings = BookingCar::select('pick_up_date', 'last_date', 'status')
            ->when($id, fn($q) => $q->where('car_id', $id))
            ->get();

        return Inertia::render('Calendar', [
            'carId' => $id,
            'bookings' => $bookings,
        ]);
    }

    public function getBookingDates($id)
    {
        $bookings = BookingCar::where('car_id', $id)
            ->select('pick_up_date', 'last_date', 'status')
            ->get();

        $dates = [
            'booked' => [],
            'reserved' => []
        ];

        foreach ($bookings as $booking) {
            $currentDate = $booking->pick_up_date;
            while (strtotime($currentDate) <= strtotime($booking->last_date)) {
                if ($booking->status === 'booked') {
                    $dates['booked'][] = $currentDate;
                } elseif ($booking->status === 'reserved') {
                    $dates['reserved'][] = $currentDate;
                }
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        return response()->json($dates);
    }
}
