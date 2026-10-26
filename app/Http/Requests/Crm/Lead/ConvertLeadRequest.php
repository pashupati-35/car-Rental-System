<?php

namespace App\Http\Requests\Crm\Lead;

use Illuminate\Foundation\Http\FormRequest;

class ConvertLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'create_customer' => 'boolean',
            'create_deal' => 'boolean',
            'deal_title' => 'nullable|string|max:150',
            'deal_value' => 'nullable|numeric|min:0',
        ];
    }
}
