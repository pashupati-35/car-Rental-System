<?php

namespace App\Http\Requests\Admin\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_id' => 'nullable|exists:cars,id',
            'pick_up_date' => 'nullable|date',
            'last_date' => 'nullable|date',
            'pickup_location' => 'nullable|string|max:255',
            'drop_location' => 'nullable|string|max:255',
            'total_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'purpose' => 'nullable|string',
        ];
    }
}
