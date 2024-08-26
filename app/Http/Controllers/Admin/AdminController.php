<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $cars = Car::all();
        return view('admin.cars_list', compact('cars'));
    }

    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('admin.car_details', compact('car'));
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
        $customers = Customer::all(); // Fetch all customers
        return view('admin.customers_list', compact('customers'));
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    public function viewBookings()
    {
        $bookings = BookingCar::with('car', 'customer')->get(); // Fetch all bookings with associated car and customer
        return view('admin.booked_car', compact('bookings'));
    }
    public function destroyBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }




}
