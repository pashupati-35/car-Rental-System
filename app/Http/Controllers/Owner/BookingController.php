<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display a listing of bookings for the authenticated owner's vehicles.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $statusCounts = $this->bookingService->getOwnerBookingStatusCounts($ownerId);
        $bookings = $this->bookingService->getOwnerPaginatedBookings($ownerId, [
            'status' => $status,
            'search' => $search,
        ], 15);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $bookings,
                'status_counts' => $statusCounts,
            ]);
        }

        return Inertia::render('owner/Bookings/Index', [
            'bookings' => $bookings,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
        ]);
    }

    /**
     * Confirm a booking.
     */
    public function confirm(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $booking = $this->bookingService->confirmOwnerBooking($ownerId, (int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking confirmed successfully.',
                'data' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $booking = $this->bookingService->cancelOwnerBooking($ownerId, (int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully.',
                'data' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}
