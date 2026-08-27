<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\BookingCar;
use App\Services\BookingService;
use App\Services\CarService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected CarService $carService,
    ) {}

    public function showCar($id)
    {
        $car = $this->carService->getCarById((int) $id);

        $dates = $this->bookingService->getBookedAndReservedDates((int) $id);

        return view('customer.show', [
            'car' => $car,
            'carId' => $car->id,
            'bookedDates' => $dates['booked'],
            'reservedDates' => $dates['reserved'],
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $dto = $request->data();
        $distanceTraveled = $request->validated('distance_traveled', 0);

        $booking = $this->bookingService->reserveCar($dto, $distanceTraveled);

        if ($booking === null) {
            return back()->withErrors('The selected dates are not available.');
        }

        return redirect()
            ->route('payment.show', ['booking' => $booking->id])
            ->with('success', 'Reserved successfully. Please confirm the booking within 2 hours.');
    }

    public function index()
    {
        $ownerId = Auth::guard('owner')->id();
        $bookings = $this->bookingService->getOwnerBookings($ownerId);

        return view('owner.auth.booked_car', compact('bookings'));
    }

    public function confirmBooking($id)
    {
        $ownerId = Auth::guard('owner')->id();

        $this->bookingService->confirmBooking((int) $id, $ownerId);

        return redirect()->route('owner.bookings.index')->with('success', 'Booking confirmed successfully.');
    }

    public function cancelBooking($id)
    {
        $ownerId = Auth::guard('owner')->id();

        $this->bookingService->cancelBookingByOwner((int) $id, $ownerId);

        return redirect()->route('owner.bookings.index')->with('success', 'Booking canceled successfully.');
    }

    public function downloadPdf($id)
    {
        $booking = BookingCar::with('car')->findOrFail($id);
        $pdf = Pdf::loadView('customer.pdf', compact('booking'));

        return $pdf->download('booking-details.pdf');
    }

    public function showPaymentForm($id)
    {
        $booking = $this->bookingService->getBookingWithCar((int) $id);

        return view('customer.payment', compact('booking'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $bookingId = $request->input('booking_id');
        $this->bookingService->processPayment((int) $bookingId);

        return response()->json(['success' => true, 'message' => 'Payment successful!']);
    }

    public function confirmation($id)
    {
        $booking = $this->bookingService->getBookingById((int) $id);

        return view('customer.confirmation', compact('booking'));
    }

    public function availableDates($id)
    {
        $dates = $this->bookingService->getBookedAndReservedDates((int) $id);

        return response()->json($dates);
    }
}
