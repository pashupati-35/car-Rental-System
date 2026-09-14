<?php

namespace App\Http\Resources\Cms\Slider\Type;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderTypeResource extends JsonResource
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
            'is_active' => $this->is_active ? true : false,
        ];

        return $resource;
    }
}
