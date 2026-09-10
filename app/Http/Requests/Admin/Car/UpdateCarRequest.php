<?php

namespace App\Http\Requests\Admin\Car;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_name' => 'nullable|string|max:255',
            'car_model' => 'nullable|string|max:255',
            'car_number' => 'nullable|string|max:100',
            'number_of_seats' => 'nullable|numeric|min:1',
            'car_price_per_day' => 'nullable|numeric|min:0',
            'car_price_per_km' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'owner_id' => 'nullable|exists:owners,id',
            'status' => 'nullable|string',
            'available' => 'nullable|string',
            'fuel_type' => 'nullable|string|max:100',
            'transmission' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'car_photo' => 'nullable|file|max:10240',
            'blue_book_photo' => 'nullable|file|max:10240',
        ];
    }
}
