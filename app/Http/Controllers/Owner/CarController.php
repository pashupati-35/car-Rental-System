<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Car\StoreCarRequest;
use App\Http\Requests\Owner\Car\UpdateCarRequest;
use App\Services\CarService;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CarController extends Controller
{
    public function __construct(
        protected CarService $carService,
        protected DriverService $driverService
    ) {}

    /**
     * Display a listing of cars for the authenticated owner.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $statusCounts = $this->carService->getOwnerCarStatusCounts($ownerId);
        $cars = $this->carService->getOwnerCars($ownerId, [
            'status' => $status,
            'search' => $search,
        ], 12);

        $availableDrivers = $this->driverService->getAvailableDriversForOwner($ownerId);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $cars,
                'status_counts' => $statusCounts,
            ]);
        }

        return Inertia::render('owner/Cars/Index', [
            'cars' => $cars,
            'drivers' => $availableDrivers,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        $ownerId = Auth::guard('owner')->id();
        $drivers = $this->driverService->getAvailableDriversForOwner($ownerId);

        return Inertia::render('owner/Cars/Create', [
            'drivers' => $drivers,
        ]);
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(StoreCarRequest $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $validated = $request->validated();

        $car = $this->carService->createOwnerCar(
            $ownerId,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car submitted successfully for admin verification.',
                'data' => $car,
            ], 201);
        }

        return redirect()->route('owner.cars.index')->with('success', 'Car submitted successfully for admin verification.');
    }

    /**
     * Display the specified car.
     */
    public function show(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $car = $this->carService->getOwnerCar($ownerId, (int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $car,
            ]);
        }

        return Inertia::render('owner/Cars/Show', [
            'car' => $car,
        ]);
    }

    /**
     * Show the form for editing the car.
     */
    public function edit($id)
    {
        $ownerId = Auth::guard('owner')->id();
        $car = $this->carService->getOwnerCar($ownerId, (int) $id);
        $drivers = $this->driverService->getAvailableDriversForOwner($ownerId);

        return Inertia::render('owner/Cars/Edit', [
            'car' => $car,
            'drivers' => $drivers,
        ]);
    }

    /**
     * Update the specified car in storage.
     */
    public function update(UpdateCarRequest $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $validated = $request->validated();

        $car = $this->carService->updateOwnerCar(
            $ownerId,
            (int) $id,
            $validated,
            $request->file('car_photo'),
            $request->file('blue_book_photo')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car updated successfully.',
                'data' => $car,
            ]);
        }

        return redirect()->route('owner.cars.index')->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $this->carService->deleteOwnerCar($ownerId, (int) $id);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car deleted successfully.',
            ]);
        }

        return redirect()->route('owner.cars.index')->with('success', 'Car deleted successfully.');
    }
}
