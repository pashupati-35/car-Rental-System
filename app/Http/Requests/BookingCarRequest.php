<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'pick_up_date' => 'required|date|after_or_equal:today',
            'last_date' => 'required|date|after_or_equal:pick_up_date',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirm,cancel',
            'car_id' => 'required|exists:cars,id',
            'customer_id' => 'required|exists:customers,id',
        ];
    }
}
