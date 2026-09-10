<?php

namespace App\Http\Resources\Cms\Notice;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'position' => $this->position,
            'user_type' => ! empty($this->user_type) && $this->user_type != 'all' ? array_map('trim', explode(',', $this->user_type)) : null,
            'user_types' => $this->user_types,
            'visible_from_date' => $this->visible_from_date,
            'formatted_date' => $this->visible_from_date ? formatDate($this->visible_from_date) : null,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
