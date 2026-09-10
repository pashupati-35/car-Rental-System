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
     * Admin Dashboard view with rich data.
     */
    public function dashboard()
    {
        if (Auth::guard('admin')->check()) {
            $totalCars = Car::count();
            $pendingCarsCount = Car::where('status', 'pending')->count();
            $verifiedCarsCount = Car::where('status', 'verified')->count();
            $rejectedCarsCount = Car::where('status', 'rejected')->count();

            $totalOwners = Owner::count();
            $totalCustomers = Customer::count();
            $totalDrivers = Driver::count();
            $totalBookings = BookingCar::count();
            $confirmedBookings = BookingCar::where('status', 'confirm')->orWhere('status', 'confirmed')->count();
            $pendingBookings = BookingCar::where('status', 'pending')->count();
            $totalRevenue = BookingCar::whereIn('status', ['confirm', 'confirmed'])->sum('total_price') ?: 0;

            $pendingCars = Car::with('owner')->where('status', 'pending')->latest()->take(6)->get();
            $recentCars = Car::with('owner')->latest()->take(5)->get();
            $recentBookings = BookingCar::with(['car', 'customer', 'payment'])->latest()->take(8)->get();
            $recentOwners = Owner::withCount('cars')->latest()->take(5)->get();
            $recentCustomers = Customer::withCount('bookings')->latest()->take(5)->get();

            // CMS stats
            $cmsStats = [
                'faqs' => \App\Models\Cms\Faq\Faq::count(),
                'blogs' => \App\Models\Cms\Blog\Blog::count(),
                'services' => \App\Models\Cms\Service\Services::count(),
                'teams' => \App\Models\Cms\Team\Team::count(),
                'testimonials' => \App\Models\Cms\Testimonial\Testimonial::count(),
                'notices' => \App\Models\Cms\Notice\Notice::count(),
                'sliders' => \App\Models\Cms\Slider\Slider::count(),
                'popups' => \App\Models\Cms\Popup\Popup::count(),
                'pages' => \App\Models\Cms\Page\Page::count(),
                'partners' => \App\Models\Cms\Partner\Partner::count(),
                'careers' => \App\Models\Cms\Career\Career::count(),
                'enquiries' => \App\Models\Cms\Enquiry\Enquiry::count(),
                'contacts' => \App\Models\Cms\ContactUs\ContactUs::count(),
                'albums' => \App\Models\Cms\Album\Album::count(),
                'menus' => \App\Models\Cms\Menu\Menu::count(),
                'news' => \App\Models\Cms\NewsAndUpdates\NewsAndUpdates::count(),
            ];

            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'totalCars' => $totalCars,
                    'pendingCarsCount' => $pendingCarsCount,
                    'verifiedCarsCount' => $verifiedCarsCount,
                    'rejectedCarsCount' => $rejectedCarsCount,
                    'totalOwners' => $totalOwners,
                    'totalCustomers' => $totalCustomers,
                    'totalDrivers' => $totalDrivers,
                    'totalBookings' => $totalBookings,
                    'confirmedBookings' => $confirmedBookings,
                    'pendingBookings' => $pendingBookings,
                    'totalRevenue' => $totalRevenue,
                ],
                'pendingCars' => $pendingCars,
                'recentCars' => $recentCars,
                'recentBookings' => $recentBookings,
                'recentOwners' => $recentOwners,
                'recentCustomers' => $recentCustomers,
                'cmsStats' => $cmsStats,
            ]);
        }

        return redirect(route('admin.login'))->with('error', 'Please login to access the dashboard.');
    }
}
