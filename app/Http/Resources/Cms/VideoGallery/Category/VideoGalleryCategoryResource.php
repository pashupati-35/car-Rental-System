<?php

namespace App\Http\Resources\Cms\VideoGallery\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoGalleryCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'featured_image' => $this->featured_image,
            'is_active' => $this->is_active ? true : false,
        ];
    }
}
