<?php

namespace App\Http\Requests\Cms\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
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
            'image' => 'nullable|array',
            'image.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,webp,pdf,doc,docx,xls,xlsx,csv,ppt,pptx`|max:51200',
            'title' => 'nullable|array',
            'title.*' => 'nullable|string|max:255',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string|max:50',
            'size' => 'nullable|array',
            'size.*' => 'nullable|numeric|min:0',
            'is_downloadable' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
