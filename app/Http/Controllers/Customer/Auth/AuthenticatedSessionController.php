<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('auth/Login', [
            'guard' => 'customer',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            $bookings = BookingCar::with('car')->where('customer_id', $customer->id)->get();
            return Inertia::render('customer/Dashboard', [
                'activeBookings' => $bookings,
                'totalRentedCars' => $bookings->count(),
            ]);
        }

        return redirect(route('customer.login'))->with('error', 'Please login to access the dashboard.');
    }
}
