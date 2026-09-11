<?php

namespace App\Http\Requests\Cms\Popup;

use Illuminate\Foundation\Http\FormRequest;

class PopupRequest extends FormRequest
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
            'type' => 'nullable|string|in:image,video,text',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'video_url' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'show_location' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff,tif,heic,heif,avif,jfif,pjpeg,pjp|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ];
    }
}
