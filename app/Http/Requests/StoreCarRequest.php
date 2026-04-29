<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('owner')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'car_name' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_number' => 'required|string|unique:cars,car_number|max:50',
            'number_of_seats' => 'required|integer|min:1|max:10',
            'car_price_per_day' => 'required|numeric|min:0',
            'car_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'car_name.required' => 'Car name is required',
            'car_model.required' => 'Car model is required',
            'car_number.required' => 'Car number/plate is required',
            'car_number.unique' => 'This car number is already registered',
            'number_of_seats.required' => 'Number of seats is required',
            'car_price_per_day.required' => 'Price per day is required',
        ];
    }
}
