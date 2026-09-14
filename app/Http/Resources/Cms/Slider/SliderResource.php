<?php

namespace App\Http\Resources\Cms\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'slider_type_id' => $this->slider_type_id,
            'type' => $this->type->title ?? null,
            'heading_text' => $this->heading_text,
            'sub_heading_text' => $this->sub_heading_text,
            'description' => $this->description,
            'button_text' => $this->button_text,
            'show_button' => $this->show_button,
            'link' => $this->link,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'position' => $this->position,
            'new_tab' => $this->new_tab,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
