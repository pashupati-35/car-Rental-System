<?php

namespace App\Http\Requests\Cms\Slider;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'slider_type_id' => 'nullable|exists:slider_types,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'link' => 'nullable|url|max:255',
            'position' => 'nullable|integer',
            'new_tab' => 'nullable|boolean',
            'heading_text' => 'nullable|string|max:255',
            'sub_heading_text' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'show_button' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
