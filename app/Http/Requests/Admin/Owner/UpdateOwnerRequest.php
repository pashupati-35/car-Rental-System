<?php

namespace App\Http\Requests\Admin\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('owner') ?? $this->id;

        return [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:owners,email,' . $id,
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ];
    }
}
