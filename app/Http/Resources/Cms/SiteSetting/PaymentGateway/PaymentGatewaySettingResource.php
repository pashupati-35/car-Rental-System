<?php

namespace App\Http\Resources\Cms\SiteSetting\PaymentGateway;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewaySettingResource extends JsonResource
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
            'merchant_id' => $this->merchant_id,
            'public_key' => $this->public_key,
            'private_key' => $this->private_key,
            'app_id' => $this->app_id,
            'app_name' => $this->app_name,
            'user_name' => $this->user_name,
            'password' => $this->password,
            'pfx_password' => $this->pfx_password,
            'bank_qr_code' => $this->bank_qr_code,
            'bank_qr_code_path' => $this->bank_qr_code_path,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
