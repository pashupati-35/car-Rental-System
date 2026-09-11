<?php

namespace App\Http\Resources\Cms\ContactUs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'is_read' => ($this->is_read) ? true : false,
            'replied' => ($this->replied) ? true : false,
            'type' => $this->type,
            'date' => formatDate($this->created_at, 'Y/m/d'),
        ];

        return $resource;
    }
}
