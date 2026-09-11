<?php

namespace App\Http\Resources\Cms\Popup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
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
            'link' => $this->link,
            'type' => $this->type,
            'type_text' => ucwords($this->type),
            'location' => $this->location,
            'show_location' => $this->show_location,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_date_value' => formatDate($this->start_date),
            'end_date_value' => formatDate($this->end_date),
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
