<?php

namespace App\Http\Requests\Customer\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCustomerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('customer')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $customerId = Auth::guard('customer')->id();

        return [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,'.$customerId,
            'phone_number' => 'nullable|string|max:25',
            'mobile' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:25',
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
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,heic,heif,avif|max:5120',
            'remove_image' => 'nullable|boolean',
        ];
    }
}
