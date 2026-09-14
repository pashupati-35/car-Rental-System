<?php

namespace App\Http\Requests\Admin\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'nullable|exists:booking_car,id',
            'car_id' => 'nullable|exists:cars,id',
            'amount' => 'nullable|numeric|min:0',
            'card_number' => 'nullable|string',
            'expiry_date' => 'nullable|string',
        ];
    }
}
