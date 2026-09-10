<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Booking\StoreCustomerBookingRequest;
use App\Http\Requests\Admin\Booking\UpdateCustomerBookingRequest;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Http\Requests\Admin\Payment\StoreCustomerPaymentRequest;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\CustomerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
        protected BookingService $bookingService,
        protected PaymentService $paymentService,
        protected CarService $carService,
    ) {}

    /**
     * Paginated customer directory (JSON response).
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $customers = $this->customerService->getAdminCustomers(['search' => $search], $perPage);

        return response()->json([
            'status' => 'success',
            'customers' => $customers,
            'filters' => [
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Create a customer and send password setup email.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();
        $result = $this->customerService->createCustomerWithPasswordSetup(
            $validated,
            Auth::guard('admin')->id(),
            $request->file('image')
        );

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

    /**
     * Get customer details and stats (JSON response).
     */
    public function show(Request $request, $id)
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

        return response()->json([
            'status' => 'success',
            'customer' => $customer,
            'bookings' => $bookings,
            'payments' => $payments,
            'availableCars' => $availableCars,
            'stats' => $stats,
        ]);
    }

    /**
     * Update customer profile.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = $this->customerService->updateCustomer(
            (int) $id,
            $request->validated(),
            $request->file('image')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated.',
                'data' => $customer,
            ]);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer details updated.');
    }

    /**
     * Delete customer.
     */
    public function destroy($id)
    {
        $this->customerService->deleteCustomer((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted.',
            ]);
        }

        return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Nested customer bookings.
     */
    public function storeBooking(StoreCustomerBookingRequest $request, $id)
    {
        $booking = $this->bookingService->createCustomerBooking((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking created for customer.',
                'data' => $booking,
            ], 201);
        }

        return redirect()->back()->with('success', 'Booking created successfully.');
    }

    public function updateBooking(UpdateCustomerBookingRequest $request, $id, $bookingId)
    {
        $booking = $this->bookingService->updateCustomerBooking((int) $id, (int) $bookingId, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking updated.',
                'data' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroyBooking($id, $bookingId)
    {
        $this->bookingService->deleteCustomerBooking((int) $id, (int) $bookingId);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking deleted.',
            ]);
        }

        return redirect()->back()->with('success', 'Booking deleted successfully.');
    }

    /**
     * Nested customer payments.
     */
    public function storePayment(StoreCustomerPaymentRequest $request, $id)
    {
        $payment = $this->paymentService->recordCustomerPayment((int) $id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Payment recorded.',
                'data' => $payment,
            ], 201);
        }

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function destroyPayment($id, $paymentId)
    {
        $this->paymentService->deleteCustomerPayment((int) $id, (int) $paymentId);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Payment record deleted.',
            ]);
        }

        return redirect()->back()->with('success', 'Payment deleted.');
    }
}
