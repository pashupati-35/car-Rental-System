<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\Car;
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
            'guard' => 'owner',
        ]);
    }

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

    public function destroy(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    public function dashboard()
    {
        $owner = Auth::guard('owner')->user();
        if ($owner) {
            $myCars = Car::where('owner_id', $owner->id)->get();
            $carIds = $myCars->pluck('id');
            $activeRentals = BookingCar::whereIn('car_id', $carIds)->where('status', 'confirmed')->count();
            $earnings = BookingCar::whereIn('car_id', $carIds)->sum('total_price') ?: 0;

            return Inertia::render('owner/Dashboard', [
                'myCarsCount' => $myCars->count(),
                'activeRentals' => $activeRentals,
                'earnings' => $earnings,
                'recentCars' => $myCars->take(5),
            ]);
        }

        return redirect(route('owner.login'))->with('error', 'Please login to access the dashboard.');
    }
}
