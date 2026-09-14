<?php

namespace App\Http\Requests\Cms\Partner;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'featured_photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }
}
