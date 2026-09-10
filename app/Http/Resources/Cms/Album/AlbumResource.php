<?php

namespace App\Http\Resources\Cms\Album;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'cover_image' => $this->cover_image,
            'cover_image_path' => $this->cover_image_path,
            'image' => $this->image ?? $this->cover_image,
            'image_path' => $this->image_path ?? $this->cover_image_path,
            'file_path' => $this->file_path ?? $this->cover_image_path,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'format_event_date' => formatDate($this->event_date),
            'tags' => $this->tags ? explode(',', $this->tags) : [],
            'position' => $this->position,
            'is_active' => ($this->is_active) ? true : false,
        ];

        return $resource;
    }
}
