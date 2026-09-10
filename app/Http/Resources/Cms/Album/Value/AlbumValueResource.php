<?php

namespace App\Http\Resources\Cms\Album\Value;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumValueResource extends JsonResource
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
            'album_id' => $this->album_id,
            'slug' => $this->slug,
            'title' => $this->title,
            'image_path' => $this->image_path,
            'is_featured' => $this->is_featured,
            'position' => $this->position,
            'cover_image_path' => $this->cover_image_path,
        ];

        return $resource;
    }
}
