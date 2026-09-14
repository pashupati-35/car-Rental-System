<?php

namespace App\Http\Requests\Owner\Car;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('owner')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
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
        ];
    }
}
