<?php

namespace App\Http\Requests\Cms\Album\Value;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AlbumValueRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'path' => 'nullable|array',
            'path.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_featured' => 'boolean|nullable',
            'position' => 'integer|nullable',
        ];
    }
}
