<?php

namespace App\Http\Requests\Crm\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleCustomerTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'to_customer' => 'nullable|boolean',
            'notify_recipient' => 'nullable|boolean',
        ];
    }
}
