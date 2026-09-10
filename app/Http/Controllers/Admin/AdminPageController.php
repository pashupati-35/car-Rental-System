<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\CustomerService;
use App\Services\DriverService;
use App\Services\EmailTemplate\EmailTemplateService;
use App\Services\OwnerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminPageController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected BookingService $bookingService,
        protected DriverService $driverService,
        protected OwnerService $ownerService,
        protected CustomerService $customerService,
        protected PaymentService $paymentService,
        protected EmailTemplateService $emailTemplateService,
    ) {}

    public function dashboard(Request $request)
    {
        return Inertia::render('admin/Dashboard');
    }

    public function cars(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $filters = [
            'status' => $status,
            'search' => $search,
        ];

        $cars = $this->carService->getAdminCars($filters, $perPage);
        $statusCounts = $this->carService->getCarStatusCounts();
        $owners = $this->ownerService->getOwnersDropdown();
        $drivers = $this->driverService->getAllDriversDropdown();

        return Inertia::render('admin/CarsList', [
            'cars' => $cars,
            'counts' => $statusCounts,
            'owners' => $owners,
            'drivers' => $drivers,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function carDetails(Request $request, $id)
    {
        $car = $this->carService->getCarDetails((int) $id);

        return Inertia::render('cars/Show', [
            'car' => $car,
        ]);
    }

    public function drivers(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');
        $ownerId = $request->input('owner_id');
        $status = $request->input('status');

        $filters = [
            'search' => $search,
            'owner_id' => $ownerId,
            'status' => $status,
        ];

        $drivers = $this->driverService->getAdminDrivers($filters, $perPage);
        $owners = $this->ownerService->getOwnersDropdown();
        $driverCounts = $this->driverService->getDriverStatusCounts();

        return Inertia::render('admin/DriversList', [
            'drivers' => $drivers,
            'owners' => $owners,
            'counts' => $driverCounts,
            'filters' => [
                'search' => $search ?? '',
                'owner_id' => $ownerId ?? '',
                'status' => $status ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function bookings(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $filters = [
            'status' => $status,
            'search' => $search,
        ];

        $bookings = $this->bookingService->getAdminBookings($filters, $perPage);
        $bookingCounts = $this->bookingService->getBookingStatusCounts();

        return Inertia::render('admin/BookedCars', [
            'bookedCars' => $bookings,
            'counts' => $bookingCounts,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function owners(Request $request)
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);

        $owners = $this->ownerService->getAdminOwners(['search' => $search], $perPage);

        return Inertia::render('admin/OwnersList', [
            'owners' => $owners,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function ownerDetails(Request $request, $id)
    {
        $data = $this->ownerService->getOwnerHubDetails((int) $id);

        return Inertia::render('admin/OwnerDetails', $data);
    }

    public function customers(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $customers = $this->customerService->getAdminCustomers(['search' => $search], $perPage);

        return Inertia::render('admin/CustomersList', [
            'customers' => $customers,
            'filters' => [
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function customerDetails(Request $request, $id)
    {
        $customer = $this->customerService->getCustomerDetails((int) $id);
        $bookings = $this->bookingService->getCustomerBookings((int) $id);
        $payments = $this->paymentService->getCustomerPayments((int) $id);
        $availableCars = $this->carService->getAvailableVerifiedCars();

        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->whereIn('status', ['confirm', 'confirmed'])->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->whereIn('status', ['cancel', 'cancelled'])->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'total_spent' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->sum('total_price'),
            'total_payments' => $payments->sum('amount'),
        ];

        return Inertia::render('admin/CustomerDetails', [
            'customer' => $customer,
            'bookings' => $bookings,
            'payments' => $payments,
            'availableCars' => $availableCars,
            'stats' => $stats,
        ]);
    }

    public function emailTemplates(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('per_pages', 15));

        $templates = $this->emailTemplateService->paginate($perPage, $request);
        $roleCounts = $this->emailTemplateService->getRoleCounts();

        return Inertia::render('admin/email-templates/Index', [
            'templates' => $templates,
            'counts' => $roleCounts,
            'filters' => $request->only(['title', 'role', 'per_page']),
        ]);
    }

    public function emailTemplateEdit(Request $request, $id)
    {
        $template = $this->emailTemplateService->findRaw($id);

        return Inertia::render('admin/email-templates/Edit', [
            'template' => $template,
        ]);
    }

    public function activityLogs(Request $request)
    {
        return Inertia::render('admin/activity-logs/Index');
    }

    public function emailLogs(Request $request)
    {
        return Inertia::render('admin/email-logs/Index');
    }

    public function profile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        return Inertia::render('admin/Profile', [
            'user' => $admin,
        ]);
    }

    public function security(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        return Inertia::render('admin/Security', [
            'user' => $admin,
        ]);
    }

    public function cms(Request $request)
    {
        return Inertia::render('admin/cms/Index', [
            'initialModule' => $request->query('module', 'faqs'),
        ]);
    }
}
