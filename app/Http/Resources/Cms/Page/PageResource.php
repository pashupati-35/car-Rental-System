<?php

namespace App\Http\Resources\Cms\Page;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'slug' => $this->slug,
            'custom_slug' => $this->custom_slug,
            'content' => $this->content,
            'position' => $this->position,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword ? explode(',', $this->seo_keyword) : [],
            'seo_description' => $this->seo_description,
            'views' => $this->views,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
