<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Booking\StoreCustomerBookingRequest;
use App\Http\Requests\Admin\Booking\UpdateCustomerBookingRequest;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Http\Requests\Admin\Owner\StoreOwnerRequest;
use App\Http\Requests\Admin\Payment\StoreCustomerPaymentRequest;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\CustomerService;
use App\Services\DriverService;
use App\Services\OwnerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected BookingService $bookingService,
        protected DriverService $driverService,
        protected OwnerService $ownerService,
        protected CustomerService $customerService,
        protected PaymentService $paymentService,
    ) {}

    /**
     * Paginated Fleet Cars list with filters.
     */
    public function index(Request $request)
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

        return Inertia::render('admin/CarsList', [
            'cars' => $cars,
            'counts' => $statusCounts,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show($id)
    {
        $car = $this->carService->getCarDetails((int) $id);
        return Inertia::render('cars/Show', [
            'car' => $car,
        ]);
    }

    /**
     * Approve vehicle and send email notification to owner.
     */
    public function verifyCar($id)
    {
        $this->carService->verifyCarAndNotify((int) $id);

        return redirect()->back()->with('success', 'Car has been verified, listed, and an approval notification has been emailed to the owner.');
    }

    /**
     * Reject vehicle and send email notification to owner.
     */
    public function rejectCar($id)
    {
        $this->carService->rejectCarAndNotify((int) $id);

        return redirect()->back()->with('success', 'Car has been rejected and an update email has been sent to the owner.');
    }

    public function destroyCar($id)
    {
        $this->carService->deleteCar((int) $id);

        return redirect()->back()->with('success', 'Vehicle removed from system fleet.');
    }

    /**
     * Driver directory & CRUD for Admin with pagination.
     */
    public function viewDrivers(Request $request)
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

    public function storeDriver(StoreDriverRequest $request)
    {
        $driver = $this->driverService->createDriver($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver added successfully.', 'data' => $driver]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver added to global directory.');
    }

    public function updateDriver(UpdateDriverRequest $request, $id)
    {
        $driver = $this->driverService->updateDriver((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver updated successfully.', 'data' => $driver]);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver record updated.');
    }

    public function destroyDriver($id)
    {
        $this->driverService->deleteDriver((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Driver deleted.']);
        }

        return redirect()->route('admin.drivers')->with('success', 'Driver deleted successfully.');
    }

    /**
     * Paginated Customer directory for Admin.
     */
    public function viewCustomers(Request $request)
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

    /**
     * Admin creates a customer and sends a password reset/setup email.
     */
    public function createCustomer(StoreCustomerRequest $request)
    {
        $result = $this->customerService->createCustomerWithPasswordSetup($request->validated(), Auth::guard('admin')->id());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully. A password setup email has been dispatched.',
                'data' => $result['customer'],
                'reset_url' => $result['reset_url'],
            ], 201);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer created successfully! A password setup email has been sent.');
    }

    public function updateCustomer(UpdateCustomerRequest $request, $id)
    {
        $customer = $this->customerService->updateCustomer((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Customer updated.', 'data' => $customer]);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer details updated.');
    }

    /**
     * Admin creates an owner and sends a password reset/setup email.
     */
    public function createOwner(StoreOwnerRequest $request)
    {
        $validated = $request->validated();
        $owner = $this->ownerService->createOwner([
            'full_name' => $validated['full_name'] ?? '',
            'email' => $validated['email'] ?? '',
            'contact_number' => $validated['contact_number'] ?? '',
            'address' => $validated['address'] ?? '',
            'gender' => $validated['gender'] ?? 'male',
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        $mailResult = \App\Services\Auth\PasswordResetService::sendResetLink($owner->email, 'owner');

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
        $this->customerService->deleteCustomer((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Customer deleted.']);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Dedicated Customer Hub / Dashboard for Admin.
     */
    public function showCustomer(Request $request, $id)
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

    public function storeCustomerBooking(StoreCustomerBookingRequest $request, $id)
    {
        $booking = $this->bookingService->createCustomerBooking((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking created for customer.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking created successfully.');
    }

    public function updateCustomerBooking(UpdateCustomerBookingRequest $request, $id, $bookingId)
    {
        $booking = $this->bookingService->updateCustomerBooking((int) $id, (int) $bookingId, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking updated.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroyCustomerBooking($id, $bookingId)
    {
        $this->bookingService->deleteCustomerBooking((int) $id, (int) $bookingId);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking deleted.']);
        }

        return redirect()->back()->with('success', 'Booking deleted successfully.');
    }

    public function storeCustomerPayment(StoreCustomerPaymentRequest $request, $id)
    {
        $payment = $this->paymentService->recordCustomerPayment((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Payment recorded.', 'data' => $payment]);
        }

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function destroyCustomerPayment($id, $paymentId)
    {
        $this->paymentService->deleteCustomerPayment((int) $id, (int) $paymentId);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Payment record deleted.']);
        }

        return redirect()->back()->with('success', 'Payment deleted.');
    }

    /**
     * Paginated Booked Cars / Reservations for Admin.
     */
    public function viewBookings(Request $request)
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

    /**
     * Confirm booking and email customer.
     */
    public function confirmBooking($id)
    {
        $booking = $this->bookingService->confirmAdminBookingAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking confirmed and notification dispatched.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking has been confirmed and a confirmation email has been dispatched to the customer.');
    }

    /**
     * Cancel booking and email customer.
     */
    public function cancelBooking($id)
    {
        $booking = $this->bookingService->cancelAdminBookingAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking cancelled and notification dispatched.', 'data' => $booking]);
        }

        return redirect()->back()->with('success', 'Booking marked as cancelled and notification email has been dispatched.');
    }

    public function destroyBooking($id)
    {
        $this->bookingService->deleteBooking((int) $id);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Booking deleted.']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }
}
