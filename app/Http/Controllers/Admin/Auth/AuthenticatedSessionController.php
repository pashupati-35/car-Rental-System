<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create()
    {
        return Inertia::render('admin/auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Show MFA verification page for Admin.
     */
    public function showMfa(Request $request)
    {
        return Inertia::render('admin/auth/MFAVerification', [
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('admin.login'));
    }

    /**
     * Admin Dashboard view.
     */
    public function dashboard()
    {
        if (Auth::guard('admin')->check()) {
            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'totalCars' => Car::count(),
                    'totalOwners' => Owner::count(),
                    'totalCustomers' => Customer::count(),
                    'totalDrivers' => Driver::count(),
                    'totalBookings' => BookingCar::count(),
                    'totalRevenue' => BookingCar::sum('total_price') ?: 0,
                ],
            ]);
        }

        return redirect(route('admin.login'))->with('error', 'Please login to access the dashboard.');
    }
}
