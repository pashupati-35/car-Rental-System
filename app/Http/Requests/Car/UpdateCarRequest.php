<?php

namespace App\Http\Requests\Car;

use App\DTOs\CarDTO;
use App\Models\Car;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        $car = $this->route('car');

        if ($car instanceof Car) {
            return auth('owner')->check() && $car->owner_id === auth('owner')->id();
        }

        $model = Car::findOrFail($car);

        return auth('owner')->check() && $model->owner_id === auth('owner')->id();
    }

    public function rules(): array
    {
        return [
            'car_name' => 'sometimes|required|string|max:255',
            'car_model' => 'sometimes|required|string|max:255',
            'car_number_part1' => 'sometimes|required|string|max:50',
            'car_number_part2' => 'sometimes|required|string|max:50',
            'car_number_part3' => 'sometimes|required|string|max:50',
            'car_number_part4' => 'sometimes|required|numeric|max:9999',
            'number_of_seats' => 'sometimes|required|integer|min:1|max:10',
            'car_price_per_km' => 'sometimes|required|numeric|min:0',
            'car_price_per_day' => 'sometimes|required|numeric|min:0',
            'blue_book_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'driver_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'licence_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'driver_name' => 'sometimes|nullable|string|max:255',
            'driver_number' => 'sometimes|nullable|string|max:255',
            'driving_experience' => 'sometimes|nullable|string|max:255',
            'available' => 'sometimes|required|in:yes,no',
            'status' => 'sometimes|required|in:pending,available,verified,booked,reserved,rejected',
            'description' => 'sometimes|nullable|string|max:1000',
            'fuel_type' => 'sometimes|nullable|string|max:50',
            'transmission' => 'sometimes|nullable|string|max:50',
        ];
    }

    public function data(): CarDTO
    {
        $validated = $this->validated();
        $car = $this->route('car') instanceof Car
            ? $this->route('car')
            : Car::findOrFail($this->route('car'));

        $photoDirs = [
            'blue_book_photo' => 'owner/car/blue_book_photos',
            'car_photo' => 'owner/car/car_photos',
            'driver_photo' => 'owner/car/driver_photos',
            'licence_photo' => 'owner/car/licence_photos',
        ];

        $photoPaths = [];

        foreach ($photoDirs as $field => $dir) {
            $photoPaths[$field] = $car->{$field};

            if ($this->hasFile($field)) {
                $oldPath = $car->{$field};
                if ($oldPath && file_exists(public_path($oldPath))) {
                    unlink(public_path($oldPath));
                }
                $filename = $this->file($field)->getClientOriginalName();
                $this->file($field)->move(public_path($dir), $filename);
                $photoPaths[$field] = $dir.'/'.$filename;
            }
        }

        $carNumber = null;
        if (isset($validated['car_number_part1'])) {
            $carNumber = implode('-', [
                $validated['car_number_part1'],
                $validated['car_number_part2'],
                $validated['car_number_part3'],
                $validated['car_number_part4'],
            ]);
        }

        return CarDTO::fromArray([
            'id' => $car->id,
            'car_name' => $validated['car_name'] ?? $car->car_name,
            'car_model' => $validated['car_model'] ?? $car->car_model,
            'car_number' => $carNumber ?? $car->car_number,
            'number_of_seats' => $validated['number_of_seats'] ?? $car->number_of_seats,
            'car_price_per_km' => $validated['car_price_per_km'] ?? $car->car_price_per_km,
            'car_price_per_day' => $validated['car_price_per_day'] ?? $car->car_price_per_day,
            'blue_book_photo' => $photoPaths['blue_book_photo'],
            'car_photo' => $photoPaths['car_photo'],
            'driver_photo' => $photoPaths['driver_photo'],
            'licence_photo' => $photoPaths['licence_photo'],
            'driver_name' => $validated['driver_name'] ?? $car->driver_name,
            'driver_number' => $validated['driver_number'] ?? $car->driver_number,
            'driving_experience' => $validated['driving_experience'] ?? $car->driving_experience,
            'available' => $validated['available'] ?? $car->available,
            'description' => $validated['description'] ?? $car->description,
            'status' => $validated['status'] ?? $car->status,
            'fuel_type' => $validated['fuel_type'] ?? $car->fuel_type,
            'transmission' => $validated['transmission'] ?? $car->transmission,
            'owner_id' => $car->owner_id,
        ]);
    }
}
