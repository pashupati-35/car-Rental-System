<?php

namespace App\Http\Requests\Crm\Deal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage' => 'required|string|in:discovery,proposal_sent,negotiation,won,lost',
        ];
    }
}
