<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'car_name' => $this->car_name,
            'car_model' => $this->car_model,
            'car_number' => $this->car_number,
            'number_of_seats' => $this->number_of_seats,
            'car_price_per_km' => $this->car_price_per_km,
            'car_price_per_day' => $this->car_price_per_day,
            'car_photo' => $this->car_photo ? asset($this->car_photo) : null,
            'blue_book_photo' => $this->blue_book_photo ? asset($this->blue_book_photo) : null,
            'driver_photo' => $this->driver_photo ? asset($this->driver_photo) : null,
            'licence_photo' => $this->licence_photo ? asset($this->licence_photo) : null,
            'driver_name' => $this->driver_name,
            'driver_number' => $this->driver_number,
            'driving_experience' => $this->driving_experience,
            'available' => $this->available,
            'description' => $this->description,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
