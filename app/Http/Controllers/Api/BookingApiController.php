<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingApiController extends Controller
{
    /**
     * Scheduling algorithm: Check if date range is available without overlapping.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pick_up_date' => 'required|date|after_or_equal:today',
            'last_date' => 'required|date|after_or_equal:pick_up_date',
        ]);

        $carId = (int)$request->input('car_id');
        $startDate = Carbon::parse($request->input('pick_up_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('last_date'))->startOfDay();

        $overlap = BookingCar::where('car_id', $carId)
            ->whereIn('status', ['confirm', 'booked', 'reserved', 'pending'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('pick_up_date', '<=', $endDate->format('Y-m-d'))
                      ->where('last_date', '>=', $startDate->format('Y-m-d'));
            })
            ->exists();

        $car = Car::find($carId);
        $days = (int)$startDate->diffInDays($endDate) + 1;
        $pricePerDay = (float)($car->car_price_per_day ?? 0);
        $totalPrice = $days * $pricePerDay;

        return response()->json([
            'status' => 'success',
            'available' => !$overlap,
            'days' => $days,
            'price_per_day' => $pricePerDay,
            'total_price' => $totalPrice,
            'message' => $overlap
                ? 'These dates are already booked or reserved. Please choose different dates.'
                : 'Selected dates are available for booking!',
        ]);
    }

    /**
     * Create a new car booking with overlap prevention.
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please sign in to your customer account to complete the booking.',
            ], 401);
        }

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'pick_up_date' => 'required|date|after_or_equal:today',
            'last_date' => 'required|date|after_or_equal:pick_up_date',
            'purpose' => 'nullable|string|max:255',
        ]);

        $carId = (int)$validated['car_id'];
        $startDate = Carbon::parse($validated['pick_up_date'])->startOfDay();
        $endDate = Carbon::parse($validated['last_date'])->startOfDay();

        return DB::transaction(function () use ($validated, $carId, $startDate, $endDate, $customer) {
            // Lock and check overlap
            $hasOverlap = BookingCar::where('car_id', $carId)
                ->whereIn('status', ['confirm', 'booked', 'reserved'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('pick_up_date', '<=', $endDate->format('Y-m-d'))
                          ->where('last_date', '>=', $startDate->format('Y-m-d'));
                })
                ->lockForUpdate()
                ->exists();

            if ($hasOverlap) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking conflict: The car has just been booked for the selected date range.',
                ], 422);
            }

            $car = Car::findOrFail($carId);
            $days = (int)$startDate->diffInDays($endDate) + 1;
            $totalPrice = $days * (float)($car->car_price_per_day ?? 100);

            $booking = BookingCar::create([
                'car_id' => $carId,
                'customer_id' => $customer->id,
                'pickup_location' => $validated['pickup_location'],
                'drop_location' => $validated['drop_location'],
                'pick_up_date' => $startDate->format('Y-m-d'),
                'last_date' => $endDate->format('Y-m-d'),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'purpose' => $validated['purpose'] ?? 'Personal Trip',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully! Please proceed to payment.',
                'data' => [
                    'booking' => $booking->load(['car.owner', 'car.driver']),
                    'total_price' => $totalPrice,
                    'days' => $days,
                ],
            ], 201);
        });
    }

    /**
     * Process payment for a booking and confirm reservation.
     */
    public function processPayment(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'booking_id' => 'required|exists:booking_car,id',
            'card_number' => 'required|string|min:12|max:19',
            'expiry_date' => 'required|string|max:7',
            'cvv' => 'required|string|min:3|max:4',
            'payment_method' => 'nullable|string',
        ]);

        $booking = BookingCar::where('customer_id', $customer->id)->findOrFail($validated['booking_id']);

        // Mask card number for PCI compliance
        $maskedCard = '****-****-****-' . substr(preg_replace('/\D/', '', $validated['card_number']), -4);

        $payment = Payment::create([
            'customer_id' => $customer->id,
            'car_id' => $booking->car_id,
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'card_number' => $maskedCard,
            'expiry_date' => $validated['expiry_date'],
            'cvv' => '***',
        ]);

        $booking->update([
            'status' => 'confirm',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully! Your car rental booking is confirmed.',
            'data' => [
                'booking' => $booking->load(['car.owner', 'car.driver']),
                'payment' => $payment,
            ],
        ]);
    }

    /**
     * Get current customer's bookings.
     */
    public function myBookings(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $bookings = BookingCar::with(['car.owner', 'car.driver', 'payment'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ]);
    }
}
