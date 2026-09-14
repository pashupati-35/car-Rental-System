<?php

namespace App\Http\Resources\Cms\Faq\Category;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqCategoryResource extends JsonResource
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
            'position' => $this->position,
            'parent_name' => $this->parent->name ?? null,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
