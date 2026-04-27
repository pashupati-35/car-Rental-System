<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    use AuthorizesRequests;
    use SoftDeletes;

    public function index()
    {
        $ownerId = auth('owner')->id();

        $cars = Car::where('owner_id', $ownerId)->get();

        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(Request $request)
    {
        // Concatenate car number parts
        $car_number = $request->car_number_part1 . '-' . $request->car_number_part2 . '-' . $request->car_number_part3 . '-' . $request->car_number_part4;

        // Validation rules
        $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number_part1' => 'required|string|max:255',
            'car_number_part2' => 'required|string|max:255',
            'car_number_part3' => 'required|string|max:255',
            'car_number_part4' => 'required|numeric|max:9999',
            'number_of_seats' => 'required|integer',
            'car_price_per_km' => 'required|numeric',
            'car_price_per_day' => 'required|numeric',
            'blue_book_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'driver_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'licence_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'driver_name' => 'nullable|string|max:255',
            'driver_number' => 'nullable|string|max:255',
            'driving_experience' => 'nullable|string|max:255',
        ]);

        // Check for existing car number
        $exists = Car::where('car_number', $car_number)->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['car_number' => 'The car number has already been taken.']);
        }
        Log::info('Concatenated car number: ' . $car_number);

        // Handle file uploads manually to public directory
        $blue_book_photo = null;
        $car_photo = null;
        $driver_photo = null;
        $licence_photo = null;

        if ($request->hasFile('blue_book_photo')) {
            $blue_book_photo = $request->file('blue_book_photo')->getClientOriginalName();
            $request->file('blue_book_photo')->move(public_path('owner/car/blue_book_photos'), $blue_book_photo);
        }

        if ($request->hasFile('car_photo')) {
            $car_photo = $request->file('car_photo')->getClientOriginalName();
            $request->file('car_photo')->move(public_path('owner/car/car_photos'), $car_photo);
        }

        if ($request->hasFile('driver_photo')) {
            $driver_photo = $request->file('driver_photo')->getClientOriginalName();
            $request->file('driver_photo')->move(public_path('owner/car/driver_photos'), $driver_photo);
        }

        if ($request->hasFile('licence_photo')) {
            $licence_photo = $request->file('licence_photo')->getClientOriginalName();
            $request->file('licence_photo')->move(public_path('owner/car/licence_photos'), $licence_photo);
        }

        Car::create([
            'car_name' => $request->car_name,
            'car_model' => $request->car_model,
            'car_number' => $car_number,
            'number_of_seats' => $request->number_of_seats,
            'car_price_per_km' => $request->car_price_per_km,
            'car_price_per_day' => $request->car_price_per_day,
            'blue_book_photo' => $blue_book_photo ? 'owner/car/blue_book_photos/' . $blue_book_photo : null,
            'car_photo' => $car_photo ? 'owner/car/car_photos/' . $car_photo : null,
            'driver_photo' => $driver_photo ? 'owner/car/driver_photos/' . $driver_photo : null,
            'licence_photo' => $licence_photo ? 'owner/car/licence_photos/' . $licence_photo : null,
            'driver_name' => $request->driver_name,
            'driver_number' => $request->driver_number,
            'driving_experience' => $request->driving_experience,
            'available' => $request->input('available', 'no'),
            'status' => $request->input('status', 'pending'),
            'owner_id' => auth('owner')->id(),
        ]);

        return redirect()->route('cars.index')->with('success', 'Car added successfully.');
    }

    public function show(Car $car)
    {
        $this->authorize('view', $car);

        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $this->authorize('update', $car);

        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        // Authorize the user to update the car
        $this->authorize('update', $car);

        // Concatenate car number parts
        $car_number = $request->car_number_part1 . '-' . $request->car_number_part2 . '-' . $request->car_number_part3 . '-' . $request->car_number_part4;

        // Validation rules
        $request->validate([
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number_part1' => 'required|string|max:255',
            'car_number_part2' => 'required|string|max:255',
            'car_number_part3' => 'required|string|max:255',
            'car_number_part4' => 'required|numeric|max:9999',
            'number_of_seats' => 'required|integer',
            'car_price_per_km' => 'required|numeric',
            'car_price_per_day' => 'required|numeric',
            'blue_book_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'driver_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'licence_photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'driver_name' => 'nullable|string|max:255',
            'driver_number' => 'nullable|string|max:255',
            'driving_experience' => 'nullable|string|max:255',
            'available' => 'required|string|in:yes,no',
            'status' => 'required|string|in:pending,available,booked,reserved',
        ]);

        // Handle file uploads
        $blue_book_photo = $car->blue_book_photo;
        $car_photo = $car->car_photo;
        $driver_photo = $car->driver_photo;
        $licence_photo = $car->licence_photo;

        if ($request->hasFile('blue_book_photo')) {
            // Delete old photo if exists
            if ($blue_book_photo && file_exists(public_path($blue_book_photo))) {
                unlink(public_path($blue_book_photo));
            }
            $blue_book_photo = $request->file('blue_book_photo')->getClientOriginalName();
            $request->file('blue_book_photo')->move(public_path('owner/car/blue_book_photos'), $blue_book_photo);
        }

        if ($request->hasFile('car_photo')) {
            // Delete old photo if exists
            if ($car_photo && file_exists(public_path($car_photo))) {
                unlink(public_path($car_photo));
            }
            $car_photo = $request->file('car_photo')->getClientOriginalName();
            $request->file('car_photo')->move(public_path('owner/car/car_photos'), $car_photo);
        }

        if ($request->hasFile('driver_photo')) {
            // Delete old photo if exists
            if ($driver_photo && file_exists(public_path($driver_photo))) {
                unlink(public_path($driver_photo));
            }
            $driver_photo = $request->file('driver_photo')->getClientOriginalName();
            $request->file('driver_photo')->move(public_path('owner/car/driver_photos'), $driver_photo);
        }

        if ($request->hasFile('licence_photo')) {
            // Delete old photo if exists
            if ($licence_photo && file_exists(public_path($licence_photo))) {
                unlink(public_path($licence_photo));
            }
            $licence_photo = $request->file('licence_photo')->getClientOriginalName();
            $request->file('licence_photo')->move(public_path('owner/car/licence_photos'), $licence_photo);
        }

        // Update the car with new data
        $car->update([
            'car_name' => $request->car_name,
            'car_model' => $request->car_model,
            'car_number' => $car_number,
            'number_of_seats' => $request->number_of_seats,
            'car_price_per_km' => $request->car_price_per_km,
            'car_price_per_day' => $request->car_price_per_day,
            'blue_book_photo' => $blue_book_photo ? 'owner/car/blue_book_photos/' . $blue_book_photo : null,
            'car_photo' => $car_photo ? 'owner/car/car_photos/' . $car_photo : null,
            'driver_photo' => $driver_photo ? 'owner/car/driver_photos/' . $driver_photo : null,
            'licence_photo' => $licence_photo ? 'owner/car/licence_photos/' . $licence_photo : null,
            'driver_name' => $request->driver_name,
            'driver_number' => $request->driver_number,
            'driving_experience' => $request->driving_experience,
            'available' => $request->available,
            'status' => $request->status,
        ]);

        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        // Authorize the user to delete the car
        $this->authorize('delete', $car);

        // Delete associated images if exist
        if ($car->blue_book_photo && file_exists(public_path($car->blue_book_photo))) {
            unlink(public_path($car->blue_book_photo));
        }
        if ($car->car_photo && file_exists(public_path($car->car_photo))) {
            unlink(public_path($car->car_photo));
        }
        if ($car->driver_photo && file_exists(public_path($car->driver_photo))) {
            unlink(public_path($car->driver_photo));
        }
        if ($car->licence_photo && file_exists(public_path($car->licence_photo))) {
            unlink(public_path($car->licence_photo));
        }

        // Soft delete the car
        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }

}
