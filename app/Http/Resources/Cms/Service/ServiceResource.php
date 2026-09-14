<?php

namespace App\Http\Resources\Cms\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'position' => $this->position,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'slug' => $this->slug,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword,
            'seo_description' => $this->seo_description,
            'social_share_image' => $this->social_share_image,
            'social_share_image_path' => $this->social_share_image_path,
        ];
    }
}
