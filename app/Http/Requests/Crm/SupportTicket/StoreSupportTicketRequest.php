<?php

namespace App\Http\Requests\Crm\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'car_id' => 'nullable|exists:cars,id',
            'booking_id' => 'nullable|exists:booking_car,id',
            'subject' => 'required|string|max:150',
            'category' => 'required|string|in:roadside_assistance,billing,extension,vehicle_complaint,general',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'initial_message' => 'required|string',
        ];
    }
}
