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

    protected function prepareForValidation(): void
    {
        if ($this->has('pick_up_date') && ! $this->filled('rent_start_date')) {
            $this->merge(['rent_start_date' => $this->input('pick_up_date')]);
        }
        if ($this->has('last_date') && ! $this->filled('rent_end_date')) {
            $this->merge(['rent_end_date' => $this->input('last_date')]);
        }
        if ($this->has('rent_start_date') && ! $this->filled('pick_up_date')) {
            $this->merge(['pick_up_date' => $this->input('rent_start_date')]);
        }
        if ($this->has('rent_end_date') && ! $this->filled('last_date')) {
            $this->merge(['last_date' => $this->input('rent_end_date')]);
        }
    }

    public function rules(): array
    {
        return [
            'car_id' => 'required|exists:cars,id',
            'pickup_location' => 'required|string|max:255',
            'drop_location' => 'required|string|max:255',
            'rent_start_date' => 'required|date',
            'rent_end_date' => 'required|date|after_or_equal:rent_start_date',
            'pick_up_date' => 'nullable|date',
            'last_date' => 'nullable|date',
            'distance_traveled' => 'nullable|numeric|min:0',
            'purpose' => 'nullable|string|max:255',
            'other_purpose' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
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
            'rent_end_date.required' => 'Rent end date is required.',
            'rent_end_date.after_or_equal' => 'Rent end date must be on or after the start date.',
        ];
    }

    public function toDTO(): BookingDTO
    {
        $validated = $this->validated();
        $startDate = $validated['rent_start_date'] ?? $this->input('pick_up_date');
        $endDate = $validated['rent_end_date'] ?? $this->input('last_date');

        return BookingDTO::fromArray([
            'pickup_location' => $validated['pickup_location'],
            'drop_location' => $validated['drop_location'],
            'pick_up_date' => $startDate,
            'last_date' => $endDate,
            'car_id' => $validated['car_id'],
            'customer_id' => auth('customer')->id(),
            'purpose' => $validated['purpose'] ?? $this->input('note') ?? null,
            'other_purpose' => $validated['other_purpose'] ?? null,
            'distance_traveled' => $validated['distance_traveled'] ?? 0,
        ]);
    }
}
