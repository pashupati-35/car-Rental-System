<?php

namespace App\Http\Requests\Cms\Page;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'custom_slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'position' => 'nullable|integer',
            'seo_title' => 'nullable|string|max:255',
            'seo_keyword' => 'nullable|array|max:255',
            'seo_description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
