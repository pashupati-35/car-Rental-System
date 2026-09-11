<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the Owner login view.
     */
    public function create()
    {
        return Inertia::render('owner/auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle incoming Owner authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('owner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('owner.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Show MFA verification page for Owner.
     */
    public function showMfa(Request $request)
    {
        return Inertia::render('owner/auth/MFAVerification', [
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Destroy Owner authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('owner.login'));
    }

    /**
     * Owner Dashboard view.
     */
    public function dashboard()
    {
        $owner = Auth::guard('owner')->user();
        if ($owner) {
            $myCars = Car::with('driver')->where('owner_id', $owner->id)->get();
            $myDrivers = Driver::where('owner_id', $owner->id)->get();
            $carIds = $myCars->pluck('id');
            $activeRentals = BookingCar::whereIn('car_id', $carIds)->where('status', 'confirm')->count();
            $earnings = BookingCar::whereIn('car_id', $carIds)->where('status', 'confirm')->sum('total_price') ?: 0;
            $pendingBookings = BookingCar::whereIn('car_id', $carIds)->where('status', 'pending')->count();

            return Inertia::render('owner/Dashboard', [
                'owner' => $owner,
                'stats' => [
                    'myCarsCount' => $myCars->count(),
                    'myDriversCount' => $myDrivers->count(),
                    'activeRentals' => $activeRentals,
                    'pendingBookings' => $pendingBookings,
                    'earnings' => $earnings,
                ],
                'recentCars' => $myCars->take(6),
                'recentDrivers' => $myDrivers->take(6),
            ]);
        }

        return redirect(route('owner.login'))->with('error', 'Please login to access the dashboard.');
    }
}
