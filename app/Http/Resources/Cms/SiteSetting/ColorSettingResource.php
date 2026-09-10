<?php

namespace App\Http\Resources\Cms\SiteSetting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorSettingResource extends JsonResource
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
            'college_name' => $this->college_name,
            'college_email' => $this->email,
            'college_phone' => $this->phone,
            'fav_icon_path' => $this->fav_icon_path,
            'logo_path' => $this->logo_path,
            'slogan' => $this->slogan,
            'tagline' => $this->tagline,
            'login_bg_path' => $this->login_bg_path,
            'login_bg_color' => $this->login_bg_color,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'colors_variables' => $this->colors_variables,
            'colors_variables_json_values' => $this->colors_variables_json_values,
        ];

        return $resource;
    }
}
