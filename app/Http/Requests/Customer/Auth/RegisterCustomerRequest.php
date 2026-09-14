<?php

namespace App\Http\Requests\Customer\Auth;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Customer::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
