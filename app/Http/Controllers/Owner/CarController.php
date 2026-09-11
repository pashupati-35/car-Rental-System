<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CarController extends Controller
{
    /**
     * Display a listing of cars for the authenticated owner.
     */
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Car::with(['driver:id,name,phone,license_number,experience_years,status'])
            ->where('owner_id', $ownerId);

        if (! empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        $cars = $query->latest()->paginate(12)->withQueryString();
        $availableDrivers = Driver::where('owner_id', $ownerId)->where('status', 'active')->get(['id', 'name', 'phone']);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $cars,
            ]);
        }

        return Inertia::render('owner/Cars/Index', [
            'cars' => $cars,
            'drivers' => $availableDrivers,
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
        $drivers = Driver::where('owner_id', $ownerId)->where('status', 'active')->get(['id', 'name', 'phone']);

        return Inertia::render('owner/Cars/Create', [
            'drivers' => $drivers,
        ]);
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();

        $validated = $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number' => 'required|string|max:50|unique:cars,car_number',
            'number_of_seats' => 'required|integer|min:1|max:100',
            'car_price_per_day' => 'required|numeric|min:0',
            'car_price_per_km' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'available' => 'nullable|boolean',
            'car_photo' => 'nullable|image|max:3072',
            'blue_book_photo' => 'nullable|file|max:5120',
        ]);

        $carPhotoPath = null;
        if ($request->hasFile('car_photo')) {
            $file = $request->file('car_photo');
            $fileName = time().'_car_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/cars'), $fileName);
            $carPhotoPath = 'uploads/cars/'.$fileName;
        }

        $blueBookPath = null;
        if ($request->hasFile('blue_book_photo')) {
            $file = $request->file('blue_book_photo');
            $fileName = time().'_bluebook_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/bluebooks'), $fileName);
            $blueBookPath = 'uploads/bluebooks/'.$fileName;
        }

        $driver = null;
        if (! empty($validated['driver_id'])) {
            $driver = Driver::where('owner_id', $ownerId)->find($validated['driver_id']);
        }

        $car = Car::create([
            'owner_id' => $ownerId,
            'driver_id' => $driver ? $driver->id : null,
            'driver_name' => $driver ? $driver->name : null,
            'driver_number' => $driver ? $driver->phone : null,
            'car_name' => $validated['car_name'],
            'car_model' => $validated['car_model'],
            'car_number' => $validated['car_number'],
            'number_of_seats' => $validated['number_of_seats'],
            'car_price_per_day' => $validated['car_price_per_day'],
            'car_price_per_km' => $validated['car_price_per_km'] ?? 0,
            'available' => $validated['available'] ?? true,
            'status' => 'pending', // Requires admin verification
            'car_photo' => $carPhotoPath,
            'blue_book_photo' => $blueBookPath,
        ]);

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
        $car = Car::with(['driver', 'booking.customer'])
            ->where('owner_id', $ownerId)
            ->findOrFail($id);

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
        $car = Car::where('owner_id', $ownerId)->findOrFail($id);
        $drivers = Driver::where('owner_id', $ownerId)->where('status', 'active')->get(['id', 'name', 'phone']);

        return Inertia::render('owner/Cars/Edit', [
            'car' => $car,
            'drivers' => $drivers,
        ]);
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        $car = Car::where('owner_id', $ownerId)->findOrFail($id);

        $validated = $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number' => 'required|string|max:50|unique:cars,car_number,'.$car->id,
            'number_of_seats' => 'required|integer|min:1|max:100',
            'car_price_per_day' => 'required|numeric|min:0',
            'car_price_per_km' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'available' => 'nullable|boolean',
            'car_photo' => 'nullable|image|max:3072',
            'blue_book_photo' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('car_photo')) {
            $file = $request->file('car_photo');
            $fileName = time().'_car_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/cars'), $fileName);
            $validated['car_photo'] = 'uploads/cars/'.$fileName;
        }

        if ($request->hasFile('blue_book_photo')) {
            $file = $request->file('blue_book_photo');
            $fileName = time().'_bluebook_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/bluebooks'), $fileName);
            $validated['blue_book_photo'] = 'uploads/bluebooks/'.$fileName;
        }

        if (! empty($validated['driver_id'])) {
            $driver = Driver::where('owner_id', $ownerId)->find($validated['driver_id']);
            $validated['driver_name'] = $driver ? $driver->name : null;
            $validated['driver_number'] = $driver ? $driver->phone : null;
        }

        $car->update($validated);

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
        $car = Car::where('owner_id', $ownerId)->findOrFail($id);
        $car->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Car deleted successfully.',
            ]);
        }

        return redirect()->route('owner.cars.index')->with('success', 'Car deleted successfully.');
    }
}
