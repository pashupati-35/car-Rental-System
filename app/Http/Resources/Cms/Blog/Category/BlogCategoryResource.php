<?php

namespace App\Http\Resources\Cms\Blog\Category;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'featured_image' => $this->featured_image,
            'featured_image_path' => $this->featured_image_path,
            'parent_title' => $this->parent->name ?? null,
            'is_active' => ($this->is_active) ? true : false,
        ];
    }
}
