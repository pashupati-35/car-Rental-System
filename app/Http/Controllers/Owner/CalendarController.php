<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display the Owner's vehicle reservation and availability calendar.
     */
    public function index(Request $request, $carId = null)
    {
        $ownerId = Auth::guard('owner')->id();
        $calendarData = $this->bookingService->getOwnerCalendarData($ownerId, $carId ? (int) $carId : null);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $calendarData,
            ]);
        }

        return Inertia::render('owner/Calendar', $calendarData);
    }
}
