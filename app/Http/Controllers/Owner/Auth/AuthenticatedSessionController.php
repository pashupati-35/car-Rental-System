<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Driver;
use App\Services\Owner\OwnerDashboardService;
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

        if (\App\Models\Customer::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This account is registered as a Customer. Please sign in through the Customer Portal.',
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
     * Owner Dashboard view with isolated analytics and recent activity.
     */
    public function dashboard(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        if ($owner) {
            $stats = OwnerDashboardService::getDashboardStats($owner->id);
            $recentCars = OwnerDashboardService::getRecentCars($owner->id, 6);
            $recentBookings = OwnerDashboardService::getRecentBookings($owner->id, 6);
            $recentDrivers = OwnerDashboardService::getRecentDrivers($owner->id, 6);
            $revenueTrends = OwnerDashboardService::getRevenueTrends($owner->id);

            return Inertia::render('owner/Dashboard', [
                'owner' => $owner,
                'stats' => $stats,
                'recentCars' => $recentCars,
                'recentBookings' => $recentBookings,
                'recentDrivers' => $recentDrivers,
                'revenueTrends' => $revenueTrends,
            ]);
        }

        return redirect(route('owner.login'))->with('error', 'Please login to access the dashboard.');
    }
}
