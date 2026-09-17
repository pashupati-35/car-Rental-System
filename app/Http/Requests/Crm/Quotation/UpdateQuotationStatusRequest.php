<?php

namespace App\Http\Requests\Crm\Quotation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:draft,sent,accepted,rejected,expired',
        ];
    }
}
