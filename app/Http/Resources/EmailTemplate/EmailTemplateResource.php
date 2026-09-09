<?php

namespace App\Http\Resources\EmailTemplate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
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
            'role' => $this->role,
            'subject' => $this->subject,
            'identifier' => $this->identifier,
            'description' => $this->description,
            'type' => $this->type,
            'accepted_inputs' => $this->accepted_inputs,
            'message_content' => $this->message_content,
            'message_data' => $this->message_data,
            'info_message' => $this->info_message,
            'alert_message' => $this->alert_message,
            'cta_url' => $this->cta_url,
            'cta_text' => $this->cta_text,
            'secondary_cta_url' => $this->secondary_cta_url,
            'secondary_cta_text' => $this->secondary_cta_text,
            'is_active' => $this->is_active,
            'status_text' => $this->status_text,
        ];

        return $resource;
    }
}
