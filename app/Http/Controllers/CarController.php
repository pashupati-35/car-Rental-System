<?php

namespace App\Http\Controllers;

use App\Http\Requests\Car\StoreCarRequest;
use App\Http\Requests\Car\UpdateCarRequest;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function __construct(
        protected CarService $carService,
    ) {}

    public function index()
    {
        $ownerId = auth('owner')->id();
        $cars = $this->carService->getCarsByOwner($ownerId);

        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(StoreCarRequest $request)
    {
        $validated = $request->validated();
        $carNumber = implode('-', [
            $validated['car_number_part1'],
            $validated['car_number_part2'],
            $validated['car_number_part3'],
            $validated['car_number_part4'],
        ]);

        if ($this->carService->carNumberExists($carNumber)) {
            return redirect()->back()->withErrors(['car_number' => 'The car number has already been taken.']);
        }

        $this->carService->createCar($request->toDTO());

        return redirect()->route('cars.index')->with('success', 'Car added successfully.');
    }

    public function show(Request $request, Car $car)
    {
        $this->authorize('view', $car);

        return view('cars.show', compact('car'));
    }

    public function edit(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        return view('cars.edit', compact('car'));
    }

    public function update(UpdateCarRequest $request, Car $car)
    {
        $this->authorize('update', $car);

        $this->carService->updateCar($car->id, $request->toDTO());

        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Request $request, Car $car)
    {
        $this->authorize('delete', $car);

        $photoFields = ['blue_book_photo', 'car_photo', 'driver_photo', 'licence_photo'];

        foreach ($photoFields as $field) {
            if ($car->{$field} && file_exists(public_path($car->{$field}))) {
                unlink(public_path($car->{$field}));
            }
        }

        $this->carService->deleteCar($car->id);

        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }
}
