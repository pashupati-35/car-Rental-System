<?php

namespace App\Http\Requests\Cms\Career\Application;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CareerApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,svg|max:2048',
            'received_at' => 'nullable|date',
            'is_read' => 'nullable|boolean',
            'is_shortlisted' => 'nullable|boolean',
        ];
    }
}
