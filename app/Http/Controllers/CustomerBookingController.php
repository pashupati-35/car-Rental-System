<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingCar;
use Illuminate\Support\Facades\Auth;

class CustomerBookingController extends Controller
{
    /**
     * Display a list of the customer's booked cars.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $bookings = BookingCar::where('customer_id', $customer->id)->with('car')->get();

        return view('customer.view_booking', compact('bookings'));
    }

    /**
     * Show the details of a specific booking.
     */
    public function show($id)
    {
        $booking = BookingCar::with('car')->where('id', $id)->first();

        if (!$booking || $booking->customer_id !== Auth::guard('customer')->id()) {
            abort(404);
        }

        return view('customer.show_booking', compact('booking'));
    }
    public function cancelBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
    
        // Check if the booking can be canceled (e.g., status is not already 'cancel')
        if ($booking->status !== 'cancel') {
            $booking->status = 'cancel';
            $booking->save();
    
            return redirect()->route('customer.bookings')->with('success', 'Your booking has been canceled.');
        }
    
        return redirect()->route('customer.bookings')->with('error', 'This booking cannot be canceled.');
    }
}
