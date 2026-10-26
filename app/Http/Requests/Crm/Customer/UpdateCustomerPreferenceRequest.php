<?php

namespace App\Http\Requests\Crm\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_car_type' => 'required|string|max:50',
            'preferred_transmission' => 'required|string|in:Automatic,Manual',
            'preferred_fuel_type' => 'nullable|string|in:Petrol,Diesel,Electric,Hybrid',
            'needs_child_seat' => 'boolean',
            'needs_chauffeur' => 'boolean',
            'vip_status' => 'boolean',
            'loyalty_tier' => 'required|string|in:Standard,Silver,Gold,Platinum',
            'special_requests' => 'nullable|string',
        ];
    }
}
