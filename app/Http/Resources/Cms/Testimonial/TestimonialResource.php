<?php

namespace App\Http\Resources\Cms\Testimonial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
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
            'description' => $this->description,
            'name' => $this->name,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'position' => $this->position,
            'rating' => $this->rating,
            'type' => $this->type,
            'job_title' => $this->job_title,
            'status' => $this->status,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
