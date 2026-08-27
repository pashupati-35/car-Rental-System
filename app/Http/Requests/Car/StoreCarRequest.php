<?php

namespace App\Http\Requests\Car;

use App\DTOs\CarDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('owner')->check();
    }

    public function rules(): array
    {
        return [
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number_part1' => 'required|string|max:50',
            'car_number_part2' => 'required|string|max:50',
            'car_number_part3' => 'required|string|max:50',
            'car_number_part4' => 'required|numeric|max:9999',
            'number_of_seats' => 'required|integer|min:1|max:10',
            'car_price_per_km' => 'required|numeric|min:0',
            'car_price_per_day' => 'required|numeric|min:0',
            'blue_book_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'driver_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'licence_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'driver_name' => 'nullable|string|max:255',
            'driver_number' => 'nullable|string|max:255',
            'driving_experience' => 'nullable|string|max:255',
            'available' => 'nullable|in:yes,no',
            'status' => 'nullable|in:pending,available,verified,booked,reserved,rejected',
            'description' => 'nullable|string|max:1000',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'car_name.required' => 'Car name is required',
            'car_model.required' => 'Car model is required',
            'number_of_seats.required' => 'Number of seats is required',
            'car_price_per_day.required' => 'Price per day is required',
            'car_price_per_km.required' => 'Price per km is required',
        ];
    }

    public function data(): CarDTO
    {
        $validated = $this->validated();

        $carNumber = implode('-', [
            $validated['car_number_part1'],
            $validated['car_number_part2'],
            $validated['car_number_part3'],
            $validated['car_number_part4'],
        ]);

        $photoDirs = [
            'blue_book_photo' => 'owner/car/blue_book_photos',
            'car_photo' => 'owner/car/car_photos',
            'driver_photo' => 'owner/car/driver_photos',
            'licence_photo' => 'owner/car/licence_photos',
        ];

        $photoPaths = [];

        foreach ($photoDirs as $field => $dir) {
            $photoPaths[$field] = null;

            if ($this->hasFile($field)) {
                $filename = $this->file($field)->getClientOriginalName();
                $this->file($field)->move(public_path($dir), $filename);
                $photoPaths[$field] = $dir.'/'.$filename;
            }
        }

        return CarDTO::fromArray([
            'car_name' => $validated['car_name'],
            'car_model' => $validated['car_model'],
            'car_number' => $carNumber,
            'number_of_seats' => $validated['number_of_seats'],
            'car_price_per_km' => $validated['car_price_per_km'],
            'car_price_per_day' => $validated['car_price_per_day'],
            'blue_book_photo' => $photoPaths['blue_book_photo'],
            'car_photo' => $photoPaths['car_photo'],
            'driver_photo' => $photoPaths['driver_photo'],
            'licence_photo' => $photoPaths['licence_photo'],
            'driver_name' => $validated['driver_name'] ?? null,
            'driver_number' => $validated['driver_number'] ?? null,
            'driving_experience' => $validated['driving_experience'] ?? null,
            'available' => $validated['available'] ?? 'no',
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'pending',
            'owner_id' => auth('owner')->id(),
        ]);
    }
}
