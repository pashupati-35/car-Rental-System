<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    ) {}

    /**
     * Paginated Booked Cars / Reservations (JSON response).
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

        $bookings = $this->bookingService->getAdminBookings($filters, $perPage);
        $bookingCounts = $this->bookingService->getBookingStatusCounts();

        return response()->json([
            'status' => 'success',
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
     * Get single booking details.
     */
    public function show($id)
    {
        $booking = $this->bookingService->getBookingById((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $booking,
        ]);
    }

    /**
     * Confirm booking and email customer.
     */
    public function confirm($id)
    {
        $booking = $this->bookingService->confirmAdminBookingAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking confirmed and notification dispatched.',
                'data' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Booking has been confirmed and a confirmation email has been dispatched to the customer.');
    }

    /**
     * Cancel booking and email customer.
     */
    public function cancel($id)
    {
        $booking = $this->bookingService->cancelAdminBookingAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled and notification dispatched.',
                'data' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Booking marked as cancelled and notification email has been dispatched.');
    }

    /**
     * Delete booking.
     */
    public function destroy($id)
    {
        $this->bookingService->deleteBooking((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking deleted.',
            ]);
        }

        return redirect()->route('admin.booked-cars')->with('success', 'Booking deleted successfully.');
    }
}
