<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomerBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    ) {}

    public function index()
    {
        $customer = Auth::guard('customer')->user() ?: Auth::user();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        $bookings = $this->bookingService->getCustomerBookings($customer->id);

        return Inertia::render('customer/Bookings', [
            'bookings' => $bookings,
            'customer' => $customer,
        ]);
    }

    public function show($id)
    {
        $customer = Auth::guard('customer')->user() ?: Auth::user();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        $booking = $this->bookingService->getBookingById((int) $id);
        if (! $booking || $booking->customer_id !== $customer->id) {
            abort(404);
        }

        return Inertia::render('customer/Bookings', [
            'bookings' => [$booking],
            'selectedBooking' => $booking,
            'customer' => $customer,
        ]);
    }

    public function cancelBooking(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user() ?: Auth::user();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        try {
            $this->bookingService->cancelBookingByCustomer((int) $id, $customer->id);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Booking canceled successfully.']);
            }

            return redirect()->route('customer.bookings')->with('success', 'Your booking has been canceled.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'This booking cannot be canceled.'], 422);
            }

            return redirect()->route('customer.bookings')->with('error', 'This booking cannot be canceled.');
        }
    }
}
