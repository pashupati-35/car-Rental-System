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
            'driver_id' => $this->driver_id,
            'car_name' => $this->car_name,
            'car_model' => $this->car_model,
            'car_number' => $this->car_number,
            'number_of_seats' => $this->number_of_seats,
            'car_price_per_km' => $this->car_price_per_km,
            'car_price_per_day' => $this->car_price_per_day,
            'car_photo' => $this->car_photo,
            'blue_book_photo' => $this->blue_book_photo,
            'driver_photo' => $this->driver_photo,
            'licence_photo' => $this->licence_photo,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'image_url' => is_array($this->image_path) ? ($this->image_path['original'] ?? null) : ($this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset($this->image)) : null),
            'car_photo_path' => $this->car_photo_path,
            'car_photo_url' => is_array($this->car_photo_path) ? ($this->car_photo_path['original'] ?? null) : ($this->car_photo ? (str_starts_with($this->car_photo, 'http') ? $this->car_photo : asset($this->car_photo)) : null),
            'blue_book_path' => $this->blue_book_path,
            'blue_book_url' => $this->blue_book_url,
            'driver_photo_path' => $this->driver_photo_path,
            'licence_photo_path' => $this->licence_photo_path,
            'file_path' => $this->file_path,
            'driver_name' => $this->driver_name,
            'driver_number' => $this->driver_number,
            'driving_experience' => $this->driving_experience,
            'available' => (bool) $this->available,
            'description' => $this->description,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'owner' => new OwnerResource($this->whenLoaded('owner')),
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
