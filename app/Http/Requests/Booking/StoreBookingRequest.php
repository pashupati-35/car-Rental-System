<?php

namespace App\Http\Requests\Booking;

use App\DTOs\BookingDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check();
    }

    public function rules(): array
    {
        return [
            'car_id' => 'required|exists:cars,id',
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'rent_start_date' => 'required|date|after_or_equal:today',
            'rent_end_date' => 'required|date|after_or_equal:rent_start_date',
            'distance_traveled' => 'nullable|numeric|min:0',
            'purpose' => 'nullable|string|max:255',
            'other_purpose' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'car_id.required' => 'Car selection is required.',
            'car_id.exists' => 'The selected car does not exist.',
            'pickup_location.required' => 'Pickup location is required.',
            'drop_location.required' => 'Drop location is required.',
            'rent_start_date.required' => 'Rent start date is required.',
            'rent_start_date.after_or_equal' => 'Rent start date must be today or a future date.',
            'rent_end_date.required' => 'Rent end date is required.',
            'rent_end_date.after_or_equal' => 'Rent end date must be on or after the start date.',
        ];
    }

    public function toDTO(): BookingDTO
    {
        $validated = $this->validated();

        return BookingDTO::fromArray([
            'pickup_location' => $validated['pickup_location'],
            'drop_location' => $validated['drop_location'],
            'pick_up_date' => $validated['rent_start_date'],
            'last_date' => $validated['rent_end_date'],
            'car_id' => $validated['car_id'],
            'customer_id' => auth('customer')->id(),
            'purpose' => $validated['purpose'] ?? null,
            'other_purpose' => $validated['other_purpose'] ?? null,
            'distance_traveled' => $validated['distance_traveled'] ?? 0,
        ]);
    }
}
