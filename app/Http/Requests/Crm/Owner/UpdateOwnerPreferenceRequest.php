<?php

namespace App\Http\Requests\Crm\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'partner_tier' => 'required|string|in:Standard,Silver Partner,Gold Partner,Platinum Partner',
            'payout_frequency' => 'required|string|in:Weekly,Bi-weekly,Monthly',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'payout_method' => 'required|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'routing_number' => 'nullable|string|max:100',
            'vip_partner' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
