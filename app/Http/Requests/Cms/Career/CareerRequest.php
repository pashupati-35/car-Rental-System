<?php

namespace App\Http\Requests\Cms\Career;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
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
            'slug' => 'nullable|string|max:255|unique:careers,slug',
            'position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'opened_at' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:opened_at',
            'employment_type' => 'nullable|string|max:255',
            'min_qualification' => 'nullable|string|max:255',
            'salary_offer' => 'nullable|string|max:255',
            'no_of_vacancies' => 'nullable|integer|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_keyword' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
