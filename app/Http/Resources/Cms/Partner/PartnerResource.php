<?php

namespace App\Http\Resources\Cms\Partner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
            'featured_photo' => $this->featured_photo,
            'featured_photo_path' => $this->featured_photo_path,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
