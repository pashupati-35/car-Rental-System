<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $cars = Car::with('owner')->get();
        return Inertia::render('admin/CarsList', [
            'cars' => $cars,
        ]);
    }

    public function show($id)
    {
        $car = Car::with('owner')->findOrFail($id);
        return Inertia::render('cars/Show', [
            'car' => $car,
        ]);
    }

    public function verifyCar(Car $car)
    {
        $car->update([
            'status' => 'verified',
            'available' => 'yes',
        ]);

        return redirect()->back()->with('success', 'Car has been verified.');
    }

    public function rejectCar(Car $car)
    {
        $car->update([
            'status' => 'rejected',
            'available' => 'no',
        ]);

        return redirect()->back()->with('success', 'Car has been rejected.');
    }

    public function viewCustomers()
    {
        $customers = Customer::all();
        return Inertia::render('admin/CustomersList', [
            'customers' => $customers,
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    public function viewBookings()
    {
        $bookings = BookingCar::with('car', 'customer')->get();
        return Inertia::render('admin/BookedCars', [
            'bookedCars' => $bookings,
        ]);
    }

    public function destroyBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }
}
