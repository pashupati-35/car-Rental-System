<?php

namespace App\Http\Resources\Cms\VideoGallery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoGalleryResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'video_html' => $this->video_html,
            'video_url' => $this->video_url,
            'description' => $this->description,
            'tags' => $this->tags,
            'position' => $this->position,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'is_active' => ($this->is_active) ? true : false,
            'is_featured' => ($this->is_featured) ? true : false,
        ];

        return $resource;
    }
}
