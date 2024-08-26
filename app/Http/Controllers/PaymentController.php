<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\BookingCar;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show the payment form.
     */
    public function show($bookingId)
    {
        $booking = BookingCar::findOrFail($bookingId);
        return view('customer.payment', compact('booking'));
    }

    /**
     * Process the payment and store payment details.
     */
    public function process(Request $request)
    {
        // Validate payment details
        $validated = $request->validate([
            'booking_id' => 'required|exists:booking_cars,id',
            'customer_name' => 'required|string|max:255',
            'cvv' => 'required|string',
        ]);

        // Find the booking
        $booking = BookingCar::findOrFail($validated['booking_id']);

        // Store payment details
        Payment::create([
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'car_id' => $booking->car_id,
            'amount' => $booking->total_price,
            'cvv' => $validated['cvv'],
            'status' => 'completed', // Assume payment is completed for this example
        ]);
        $bookingId = $request->input('booking_id');

        // Redirect to a confirmation page or any other page
        return redirect()->route('payment.confirmation', ['booking' => $bookingId])
            ->with('success', 'Payment successful!');
    }

    public function confirmation($bookingId)
    {
        $booking = BookingCar::findOrFail($bookingId);
        return view('customer.confirmation', compact('booking'));
    }

}
