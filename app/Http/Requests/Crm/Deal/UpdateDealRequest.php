<?php

namespace App\Http\Requests\Crm\Deal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'corporate_account_id' => 'nullable|exists:crm_corporate_accounts,id',
            'car_id' => 'nullable|exists:cars,id',
            'stage' => 'required|string|in:discovery,proposal_sent,negotiation,won,lost',
            'deal_value' => 'required|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
