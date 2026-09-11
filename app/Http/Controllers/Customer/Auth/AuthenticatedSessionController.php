<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the Customer login view.
     */
    public function create()
    {
        return Inertia::render('customer/auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle incoming Customer authentication request.
     */
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

        if (\App\Models\Owner::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This account is registered as a Fleet Owner. Please sign in through the Fleet Owner Portal.',
            ]);
        }

        if (\App\Models\Admin::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This account is registered as an Admin. Please sign in through the Admin Portal.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Show MFA verification page for Customer.
     */
    public function showMfa(Request $request)
    {
        return Inertia::render('customer/auth/MFAVerification', [
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Destroy Customer authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    /**
     * Customer Dashboard view.
     */
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            $bookings = BookingCar::with(['car.owner', 'car.driver', 'payment'])
                ->where('customer_id', $customer->id)
                ->orderByDesc('created_at')
                ->get();

            $featuredCars = Car::with(['owner', 'driver'])
                ->where(function ($q) {
                    $q->whereIn('status', ['verified', 'available', 'active', 'approved', 'pending'])
                        ->orWhere('available', 'yes')
                        ->orWhereNull('status');
                })
                ->orderByDesc('id')
                ->take(6)
                ->get();

            return Inertia::render('customer/Dashboard', [
                'customer' => $customer,
                'activeBookings' => $bookings,
                'featuredCars' => $featuredCars,
                'totalRentedCars' => $bookings->where('status', 'confirm')->count(),
                'totalSpent' => $bookings->where('status', 'confirm')->sum('total_price') ?: 0,
            ]);
        }

        return redirect(route('customer.login'))->with('error', 'Please login to access the dashboard.');
    }
}
