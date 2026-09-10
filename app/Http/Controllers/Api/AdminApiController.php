<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Owner;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    /**
     * Get system-wide stats.
     */
    public function stats()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'totalCars' => Car::count(),
                'totalOwners' => Owner::count(),
                'totalCustomers' => Customer::count(),
                'totalDrivers' => Driver::count(),
                'totalBookings' => BookingCar::count(),
                'totalRevenue' => BookingCar::where('status', 'confirm')->sum('total_price') ?: 0,
                'pendingVerifications' => Car::where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * Get list of all cars.
     */
    public function cars()
    {
        $cars = Car::with(['owner', 'driver'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $cars]);
    }

    /**
     * Get list of all drivers.
     */
    public function drivers()
    {
        $drivers = Driver::with(['owner', 'cars'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $drivers]);
    }

    /**
     * Get list of all owners.
     */
    public function owners()
    {
        $owners = Owner::withCount(['cars', 'drivers'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $owners]);
    }

    /**
     * Get list of all customers.
     */
    public function customers()
    {
        $customers = Customer::withCount('bookings')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $customers]);
    }

    /**
     * Get list of all bookings.
     */
    public function bookings()
    {
        $bookings = BookingCar::with(['car.owner', 'customer', 'payment'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $bookings]);
    }
}
