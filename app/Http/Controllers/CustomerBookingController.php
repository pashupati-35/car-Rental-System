<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;

class CustomerBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    ) {}

    public function index()
    {
        $customerId = Auth::guard('customer')->user()->id;
        $bookings = $this->bookingService->getCustomerBookings($customerId);

        return view('customer.view_booking', compact('bookings'));
    }

    public function show($id)
    {
        $customerId = Auth::guard('customer')->id();
        $booking = $this->bookingService->getBookingWithCar((int) $id);

        if (! $booking || $booking->customer_id !== $customerId) {
            abort(404);
        }

        return view('customer.show_booking', compact('booking'));
    }

    public function cancelBooking($id)
    {
        $customerId = Auth::guard('customer')->id();

        try {
            $this->bookingService->cancelBookingByCustomer((int) $id, $customerId);

            return redirect()->route('customer.bookings')->with('success', 'Your booking has been canceled.');
        } catch (\RuntimeException $e) {
            return redirect()->route('customer.bookings')->with('error', 'This booking cannot be canceled.');
        }
    }
}
