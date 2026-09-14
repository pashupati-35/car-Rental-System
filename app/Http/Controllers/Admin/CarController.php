<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Car\UpdateCarRequest;
use App\Services\BookingService;
use App\Services\CarService;
use App\Services\DriverService;
use App\Services\OwnerService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected OwnerService $ownerService,
        protected DriverService $driverService,
        protected BookingService $bookingService,
    ) {}

    /**
     * Get list of fleet cars with filters (JSON response).
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

        $cars = $this->carService->getAdminCars($filters, $perPage);
        $statusCounts = $this->carService->getCarStatusCounts();
        $owners = $this->ownerService->getOwnersDropdown();
        $drivers = $this->driverService->getAllDriversDropdown();

        return response()->json([
            'status' => 'success',
            'cars' => $cars,
            'counts' => $statusCounts,
            'owners' => $owners,
            'drivers' => $drivers,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Get car details (JSON response).
     */
    public function show($id)
    {
        $car = $this->carService->getCarDetails((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $car,
        ]);
    }

    /**
     * Approve vehicle and send email notification to owner.
     */
    public function verify($id)
    {
        $this->carService->verifyCarAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car has been verified and listed successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Car has been verified, listed, and an approval notification has been emailed to the owner.');
    }

    /**
     * Reject vehicle and send email notification to owner.
     */
    public function reject($id)
    {
        $this->carService->rejectCarAndNotify((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car has been rejected.',
            ]);
        }

        return redirect()->back()->with('success', 'Car has been rejected and an update email has been sent to the owner.');
    }

    /**
     * Update vehicle specifications & attributes.
     */
    public function update(UpdateCarRequest $request, $id)
    {
        $car = $this->carService->updateCarDetails(
            (int) $id,
            $request->validated(),
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car details updated successfully.',
                'data' => $car,
            ]);
        }

        return redirect()->back()->with('success', 'Car details updated successfully.');
    }

    /**
     * Delete car from system fleet.
     */
    public function destroy($id)
    {
        $this->carService->deleteCar((int) $id);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Vehicle removed from system fleet.',
            ]);
        }

        return redirect()->back()->with('success', 'Vehicle removed from system fleet.');
    }

    /**
     * Get calendar events for cars.
     */
    public function calendarEvents(Request $request)
    {
        $id = $request->input('car_id');
        $bookings = $this->bookingService->getCalendarBookings($id ? (int) $id : null);
        $cars = $this->carService->getCalendarCars();

        return response()->json([
            'status' => 'success',
            'bookings' => $bookings,
            'cars' => $cars,
        ]);
    }
}
