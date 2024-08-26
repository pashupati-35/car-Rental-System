<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Car;
use App\Models\BookingCar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function showCar($id)
    {
        $car = Car::findOrFail($id);

        $bookings = BookingCar::where('car_id', $id)->get();
        $bookedDates = [];
        $reservedDates = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->pick_up_date);
            $end = Carbon::parse($booking->last_date);

            while ($start <= $end) {
                if ($booking->status === 'booked') {
                    $bookedDates[] = $start->format('Y-m-d');
                } elseif ($booking->status === 'reserved') {
                    $reservedDates[] = $start->format('Y-m-d');
                }
                $start->addDay();
            }
        }

        $bookedDates = array_unique($bookedDates);
        $reservedDates = array_unique($reservedDates);

        return view('customer.show', [
            'car' => $car,
            'carId' => $car->id,
            'bookedDates' => $bookedDates,
            'reservedDates' => $reservedDates
        ]);
    }
public function store(Request $request)
{
    $validated = $this->validateBookingRequest($request);
    $carId = $validated['car_id'];
    $requestedStartDate = Carbon::parse($validated['rent_start_date']);
    $requestedEndDate = Carbon::parse($validated['rent_end_date']);
    $existingBookings = $this->getBookingsForCar($carId);
    if ($this->hasDateConflict($existingBookings, $requestedStartDate, $requestedEndDate)) {
        return back()->withErrors('The selected dates are not available.');
    }
    $totalPrice = $this->calculateTotalPrice($carId, $requestedStartDate, $requestedEndDate, $validated['distance_traveled'] ?? 0);
    $booking = $this->reserveCar($validated, $totalPrice);

    return redirect()->route('payment.show', ['booking' => $booking->id])->with('success', 'Reserved successfully. Please confirm the booking within 2 hours.');
}

protected function validateBookingRequest(Request $request)
{
    return $request->validate([
        'car_id' => 'required|exists:cars,id',
        'pickup_location' => 'required|string|max:255',
        'drop_location' => 'required|string|max:255',
        'rent_start_date' => 'required|date|after_or_equal:today',
        'rent_end_date' => 'required|date|after_or_equal:rent_start_date',
        'distance_traveled' => 'nullable|numeric|min:0',
        'purpose' => 'nullable|string|max:255',
        'other_purpose' => 'nullable|string|max:255',
    ]);
}

protected function getBookingsForCar($carId)
{
    return BookingCar::where('car_id', $carId)
        ->whereIn('status', ['reserved', 'booked'])
        ->get(['pick_up_date', 'last_date']);
}

protected function hasDateConflict($existingBookings, $requestedStartDate, $requestedEndDate)
{
    foreach ($existingBookings as $booking) {
        if ($this->datesOverlap($requestedStartDate, $requestedEndDate, Carbon::parse($booking->pick_up_date), Carbon::parse($booking->last_date))) {
            return true;
        }
    }
    return false;
}

protected function datesOverlap($start1, $end1, $start2, $end2)
{
    return $start1 <= $end2 && $end1 >= $start2;
}

protected function calculateTotalPrice($carId, $startDate, $endDate, $distanceTraveled)
{
    $car = Car::findOrFail($carId);
    
    $days = $startDate->diffInDays($endDate)+1;

    $totalPrice = $days > 0
        ? ($days) * $car->car_price_per_day 
        : ($distanceTraveled > 0 ? $distanceTraveled * $car->car_price_per_km : 0);

    return $totalPrice;
}

protected function reserveCar(array $validated, $totalPrice)
{
    return BookingCar::create([
        'pickup_location' => $validated['pickup_location'],
        'drop_location' => $validated['drop_location'],
        'pick_up_date' => $validated['rent_start_date'],
        'last_date' => $validated['rent_end_date'],
        'total_price' => $totalPrice,
        'status' => 'reserved',
        'car_id' => $validated['car_id'],
        'customer_id' => auth()->id(),
        'purpose' => $validated['purpose'] ?? null,
        'other_purpose' => $validated['other_purpose'] ?? null,
    ]);
}
   


    public function index()
    {
        $ownerId = Auth::guard('owner')->id();
        $cars = Car::where('owner_id', $ownerId)->pluck('id');
        $bookings = BookingCar::whereIn('car_id', $cars)
            ->whereIn('status', ['reserved', 'booked','canceled'])
            ->with('car', 'customer')
            ->get();
        return view('owner.auth.booked_car', compact('bookings'));
    }

    /**
     * Confirm a booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
        $ownerId = Auth::guard('owner')->id();
        if ($booking->car->owner_id !== $ownerId) {
            return redirect()->route('owner.bookings.index')->with('error', 'Unauthorized action.');
        }
        $booking->status = 'booked';
        $booking->save();
        return redirect()->route('owner.bookings.index')->with('success', 'Booking confirmed successfully.');
    }

    public function cancelBooking($id)
    {
        $booking = BookingCar::findOrFail($id);
        $ownerId = Auth::guard('owner')->id();
        if ($booking->car->owner_id !== $ownerId) {
            return redirect()->route('owner.bookings.index')->with('error', 'Unauthorized action.');
        }
        $booking->status = 'canceled';
        $booking->save();
        return redirect()->route('owner.bookings.index')->with('success', 'Booking canceled successfully.');
    }
    public function downloadPdf($id)
    {
        $booking = BookingCar::with('car')->findOrFail($id);
        $pdf = PDF::loadView('customer.pdf', compact('booking'));
        return $pdf->download('booking-details.pdf');
    }
    public function processPayment(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'cvv' => 'required|string',
        ]);
        $booking = BookingCar::findOrFail($request->booking_id);
        $booking->status = 'booked';
        $booking->save();
        return response()->json(['success' => true, 'message' => 'Payment successful!']);
    }
    public function showPaymentForm($id)
    {
        $booking = BookingCar::findOrFail($id);
        return view('customer.payment', compact('booking'));
    }
    public function confirmation($id)
    {
        $booking = BookingCar::findOrFail($id);
        return view('customer.confirmation', compact('booking'));
    }

    // public function showReturnForm($id)
    // {
    //     $booking = BookingCar::findOrFail($id);

    //     return view('customer.return', compact('booking'));
    // }

    // public function returnBooking(Request $request, $id)
    // {
    //     $booking = BookingCar::findOrFail($id);

    //     $request->validate([
    //         'extra_km' => 'required|numeric|min:0',
    //     ]);

    //     $extraKm = $request->input('extra_km');
    //     $extraPrice = $extraKm * $booking->car->car_price_per_km;

    //     $totalPriceParts = explode('+', $booking->total_price);
    //     $originalPrice = array_sum($totalPriceParts);
    //     $updatedPrice = $originalPrice + $extraPrice;
    //     $booking->total_price = $originalPrice . '+' . $extraPrice;

    //     $booking->status = 'returned';
    //     $booking->save();

    //     return redirect()->route('customer.bookings')->with('success', 'Booking returned successfully with updated pricing.');
    // }

}
