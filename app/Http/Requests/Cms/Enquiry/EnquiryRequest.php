<?php

namespace App\Http\Requests\Cms\Enquiry;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'mark_as_read' => 'sometimes|boolean',
        ];
    }
}
