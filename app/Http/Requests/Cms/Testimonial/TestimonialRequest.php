<?php

namespace App\Http\Requests\Cms\Testimonial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
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
            'description' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'position' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:0|max:5',
            'status' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
