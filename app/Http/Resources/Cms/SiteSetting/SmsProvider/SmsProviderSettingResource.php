<?php

namespace App\Http\Resources\Cms\SiteSetting\SmsProvider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsProviderSettingResource extends JsonResource
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
            'type' => $this->type,
            'token' => $this->token,
            'sender' => $this->sender,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
