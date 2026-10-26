<?php

namespace App\Http\Requests\Crm\CorporateAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorporateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:150',
            'business_reg_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'contract_discount_percent' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'required|string|max:50',
            'status' => 'required|string|in:active,pending,suspended',
            'notes' => 'nullable|string',
        ];
    }
}
