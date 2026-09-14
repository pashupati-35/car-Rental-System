<?php

namespace App\Http\Requests\Cms\Team;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string',
            'job_title' => 'nullable|string|max:255',
            'fb_url' => 'nullable|url|max:255',
            'linked_url' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }
}
