<?php

namespace App\Http\Controllers;

use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        // Retrieve only available cars
        $cars = Car::where('available', 'yes')->get();
        return view('dashboard', compact('cars'));
    }
    public function view($id)
    {
        $car = Car::findOrFail($id);
        return view('frontend.show', compact('car'));
    }
    public function showCalendar($id)
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

        return view('car-calendar', [
            'id' => $id,
            'dates' => $dates,
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
