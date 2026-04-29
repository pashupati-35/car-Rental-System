<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'car_name' => $this->car_name,
            'car_model' => $this->car_model,
            'car_number' => $this->car_number,
            'number_of_seats' => $this->number_of_seats,
            'car_price_per_day' => $this->car_price_per_day,
            'car_photo' => $this->car_photo ? asset($this->car_photo) : null,
            'description' => $this->description,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
