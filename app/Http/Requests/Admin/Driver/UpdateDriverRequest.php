<?php

namespace App\Http\Requests\Admin\Driver;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'experience_years' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'owner_id' => 'nullable|exists:owners,id',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|file|max:10240',
        ];
    }
}
