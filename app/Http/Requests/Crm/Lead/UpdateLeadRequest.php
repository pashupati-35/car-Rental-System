<?php

namespace App\Http\Requests\Crm\Lead;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'source' => 'required|string|in:website,phone,walk_in,referral,corporate,ai_chat,other',
            'status' => 'required|string|in:new,contacted,qualified,proposal_sent,converted,lost',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'interested_car_id' => 'nullable|exists:cars,id',
            'pickup_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:pickup_date',
            'notes' => 'nullable|string',
        ];
    }
}
