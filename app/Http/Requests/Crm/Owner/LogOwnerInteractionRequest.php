<?php

namespace App\Http\Requests\Crm\Owner;

use Illuminate\Foundation\Http\FormRequest;

class LogOwnerInteractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:call,email,meeting,note,whatsapp,sms',
            'subject' => 'required|string|max:150',
            'details' => 'required|string',
            'interaction_date' => 'nullable|date',
        ];
    }
}
