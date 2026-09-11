<?php

namespace App\Http\Requests\Cms\Blog;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
     * BlogResource hands these back to the client as arrays (category_id via the
     * category_ids accessor, seo_keyword via explode), but both are stored in a
     * single comma separated column and read back with FIND_IN_SET / explode.
     * Fold the array form back down so a client can post what it was given.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        foreach (['category_id', 'seo_keyword'] as $key) {
            if (! $this->has($key)) {
                continue;
            }

            $value = $this->input($key);

            if (is_array($value)) {
                $value = implode(',', array_filter($value, fn ($item) => $item !== null && $item !== ''));
            } elseif (is_int($value) || is_float($value)) {
                $value = (string) $value;
            } else {
                continue;
            }

            $this->merge([$key => $value]);
        }
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
            'slug' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'author_name' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_keyword' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'social_share_description' => 'nullable|string',
            'content' => 'nullable|string',
            'category_id' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'author_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'social_share_image_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }
}
