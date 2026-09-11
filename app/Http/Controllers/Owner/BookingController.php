<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for the authenticated owner's vehicles.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        // Status counts for owner bookings
        $statusCounts = [
            'all' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->count(),
            'pending' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'pending')->count(),
            'confirmed' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'confirmed')->count(),
            'completed' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'completed')->count(),
            'cancelled' => empty($carIds) ? 0 : BookingCar::whereIn('car_id', $carIds)->where('status', 'cancelled')->count(),
        ];

        $query = BookingCar::with([
            'car:id,car_name,car_model,car_number,car_photo,car_price_per_day,owner_id',
            'customer:id,first_name,last_name,name,email,mobile,phone_number,address',
            'payment',
        ])
            ->whereIn('car_id', $carIds);

        if (! empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pickup_location', 'like', "%{$search}%")
                    ->orWhere('drop_location', 'like', "%{$search}%")
                    ->orWhereHas('car', function ($cq) use ($search) {
                        $cq->where('car_name', 'like', "%{$search}%")
                            ->orWhere('car_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

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
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();

        $booking = BookingCar::whereIn('car_id', $carIds)->findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();

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
        $carIds = Car::where('owner_id', $ownerId)->pluck('id')->toArray();

        $booking = BookingCar::whereIn('car_id', $carIds)->findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();

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
