<?php

namespace App\Http\Requests\Crm\SupportTicket;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:open,in_progress,waiting_customer,resolved,closed',
        ];
    }
}
