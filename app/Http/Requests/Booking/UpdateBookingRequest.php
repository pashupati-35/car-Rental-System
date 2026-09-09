<?php

namespace App\Http\Requests\Booking;

use App\DTOs\BookingDTO;
use App\Models\BookingCar;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking') ?? $this->route('id');

        if (auth('owner')->check()) {
            return true;
        }

        if (auth('admin')->check()) {
            return true;
        }

        if (auth('customer')->check()) {
            $bookingId = $booking instanceof BookingCar ? $booking->id : $booking;
            $model = BookingCar::findOrFail($bookingId);

            return $model->customer_id === auth('customer')->id();
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'pickup_location' => 'sometimes|required|string|max:255',
            'drop_location' => 'sometimes|required|string|max:255',
            'pick_up_date' => 'sometimes|required|date|after_or_equal:today',
            'last_date' => 'sometimes|required|date|after_or_equal:pick_up_date',
            'total_price' => 'sometimes|nullable|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,confirmed,cancelled,booked,reserved,returned',
            'purpose' => 'sometimes|nullable|string|max:255',
            'other_purpose' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function toDTO(): BookingDTO
    {
        $validated = $this->validated();
        $bookingId = $this->route('booking') instanceof BookingCar
            ? $this->route('booking')->id
            : $this->route('id');

        return BookingDTO::fromArray(array_merge($validated, ['id' => $bookingId]));
    }
}
