<?php

namespace App\Http\Requests\Owner\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('owner')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $ownerId = Auth::guard('owner')->id();

        return [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:100|unique:owners,username,'.$ownerId,
            'email' => 'required|email|max:255|unique:owners,email,'.$ownerId,
            'contact_number' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string|max:100',
            'citizenship_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:50',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_relationship' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:4096',
        ];
    }
}
