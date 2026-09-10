<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'license_number' => $this->license_number,
            'experience_years' => $this->experience_years,
            'photo' => $this->photo,
            'license_photo' => $this->license_photo,
            'status' => $this->status,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'photo_path' => $this->photo_path,
            'license_photo_path' => $this->license_photo_path,
            'license_photo_url' => $this->license_photo_url,
            'file_path' => $this->file_path,
            'owner' => new OwnerResource($this->whenLoaded('owner')),
            'cars' => CarResource::collection($this->whenLoaded('cars')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
