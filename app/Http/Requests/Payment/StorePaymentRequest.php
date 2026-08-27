<?php

namespace App\Http\Requests\Payment;

use App\DTOs\PaymentDTO;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check();
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:booking_car,id',
            'customer_name' => 'required|string|max:255',
            'cvv' => 'required|string|max:4',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking reference is required.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'customer_name.required' => 'Customer name is required.',
            'cvv.required' => 'CVV is required.',
        ];
    }

    public function data(): PaymentDTO
    {
        $validated = $this->validated();

        return PaymentDTO::fromArray([
            'booking_id' => $validated['booking_id'],
            'customer_id' => auth('customer')->id(),
            'cvv' => $validated['cvv'],
            'status' => 'completed',
        ]);
    }
}
