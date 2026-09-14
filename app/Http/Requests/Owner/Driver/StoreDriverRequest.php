<?php

namespace App\Http\Requests\Owner\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDriverRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'required|string|max:50',
            'experience_years' => 'required|string|max:10',
            'photo' => 'nullable|image|max:2048',
            'license_photo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:active,inactive,on_trip',
        ];
    }
}
