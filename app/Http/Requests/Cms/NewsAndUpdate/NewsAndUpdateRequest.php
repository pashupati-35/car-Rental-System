<?php

namespace App\Http\Requests\Cms\NewsAndUpdate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewsAndUpdateRequest extends FormRequest
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
            'url' => 'nullable|url|max:255',
            'published_by' => 'nullable|max:255',
            'publish_date' => 'nullable|date',
            'social_share_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }
}
