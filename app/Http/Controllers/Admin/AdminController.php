<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Owner;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $customers = Customer::withCount('bookings')->latest()->get();
        return Inertia::render('admin/CustomersList', [
            'customers' => $customers,
        ]);
    }

    /**
     * Admin creates a customer and sends a password reset/setup email.
     */
    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'nullable|string',
        ]);

        $randomPassword = Str::random(16);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'] ?? 'male',
            'password' => Hash::make($randomPassword),
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        // Send reset password / setup email
        $mailResult = PasswordResetService::sendResetLink($customer->email, 'customer');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully. A password setup email has been dispatched.',
                'data' => $customer,
                'reset_url' => $mailResult['reset_url'] ?? null,
            ], 201);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer created successfully! A password setup email has been sent.');
    }

    /**
     * Admin creates an owner and sends a password reset/setup email.
     */
    public function createOwner(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'nullable|string',
        ]);

        $randomPassword = Str::random(16);

        $owner = Owner::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
            'gender' => $validated['gender'] ?? 'male',
            'password' => Hash::make($randomPassword),
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        // Send reset password / setup email
        $mailResult = PasswordResetService::sendResetLink($owner->email, 'owner');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Fleet Owner registered. A password setup email has been dispatched.',
                'data' => $owner,
                'reset_url' => $mailResult['reset_url'] ?? null,
            ], 201);
        }

        return redirect()->back()->with('success', 'Fleet Owner registered! A password setup email has been sent.');
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
