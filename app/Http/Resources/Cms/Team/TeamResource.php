<?php

namespace App\Http\Resources\Cms\Team;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $resource = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'job_title' => $this->job_title,
            'fb_url' => $this->fb_url,
            'linked_url' => $this->linked_url,
            'position' => $this->position,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'is_active' => $this->is_active,
            'whatsapp' => $this->whatsapp,
        ];

        return $resource;
    }
}
