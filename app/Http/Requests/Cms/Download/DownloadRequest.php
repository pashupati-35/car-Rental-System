<?php

namespace App\Http\Requests\Cms\Download;

use Illuminate\Foundation\Http\FormRequest;

class DownloadRequest extends FormRequest
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
            'download_type_id' => 'nullable|exists:download_types,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
            'preview_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_private' => 'nullable|boolean',
            'public_hidden' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
