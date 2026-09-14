<?php

namespace App\Http\Resources\Cms\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keyword' => $this->seo_keyword,
            'tags' => $this->tags,
            'position' => $this->position,
            'is_active' => ($this->is_active) ? true : false,
            'faq_category_id' => $this->faq_category_id,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
        ];

        return $resource;
    }
}
