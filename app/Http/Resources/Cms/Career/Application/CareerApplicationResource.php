<?php

namespace App\Http\Resources\Cms\Career\Application;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerApplicationResource extends JsonResource
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
            'career_id' => $this->career_id,
            'career_title' => ($this->career_id) ? $this->career->title : null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'file' => $this->file,
            'file_path' => $this->file_path,
            'received_at' => $this->received_at,
            'formatted_received_at' => formatDate($this->received_at),
            'is_read' => $this->is_read,
            'is_shortlisted' => $this->is_shortlisted,
        ];
    }
}
