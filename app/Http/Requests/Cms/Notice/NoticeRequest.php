<?php

namespace App\Http\Requests\Cms\Notice;

use Illuminate\Foundation\Http\FormRequest;

class NoticeRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_type' => 'nullable|string',
            'position' => 'nullable|integer',
            'visible_from_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ];
    }
}
