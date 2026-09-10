<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminCountCacheService;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\CustomerService;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected ?CarService $carService = null,
        protected ?BookingService $bookingService = null,
        protected ?OwnerService $ownerService = null,
        protected ?CustomerService $customerService = null,
    ) {}

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
     * Admin Dashboard view with rich data.
     */
    public function dashboard(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            AdminCountCacheService::clear();
            $stats = AdminCountCacheService::getDashboardStats();
            $cmsStats = AdminCountCacheService::getCmsStats();

            $pendingCars = $this->carService ? $this->carService->getPendingCars(6) : collect();
            $recentCars = $this->carService ? $this->carService->getRecentCars(5) : collect();
            $perPage = (int) $request->input('per_page', 8);
            $recentBookings = $this->bookingService ? $this->bookingService->getRecentBookings($perPage) : [];
            $bookingTrends = $this->bookingService ? $this->bookingService->getBookingTrends() : [];
            $recentOwners = $this->ownerService ? $this->ownerService->getRecentOwners(5) : collect();
            $recentCustomers = $this->customerService ? $this->customerService->getRecentCustomers(5) : collect();

            return Inertia::render('admin/Dashboard', [
                'stats' => $stats,
                'pendingCars' => $pendingCars,
                'recentCars' => $recentCars,
                'recentBookings' => $recentBookings,
                'bookingTrends' => $bookingTrends,
                'recentOwners' => $recentOwners,
                'recentCustomers' => $recentCustomers,
                'cmsStats' => $cmsStats,
            ]);
        }

        return redirect(route('admin.login'))->with('error', 'Please login to access the dashboard.');
    }
}
