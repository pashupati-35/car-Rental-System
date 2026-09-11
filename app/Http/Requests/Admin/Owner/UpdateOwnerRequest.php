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
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:owners,email,'.$id,
            'contact_number' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:25',
            'mobile' => 'nullable|string|max:25',
            'username' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'citizenship_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:50',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_relationship' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'is_mfa_enabled' => 'nullable|boolean',
            'is_email_authentication_enabled' => 'nullable|boolean',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff,tif,heic,heif,avif,jfif,pjpeg,pjp|max:5120',
            'password' => 'nullable|string|min:6',
        ];
    }
}
