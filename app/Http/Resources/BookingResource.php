<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pickup_location' => $this->pickup_location,
            'drop_location' => $this->drop_location,
            'pick_up_date' => $this->pick_up_date?->format('Y-m-d'),
            'last_date' => $this->last_date?->format('Y-m-d'),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'car_id' => $this->car_id,
            'customer_id' => $this->customer_id,
            'purpose' => $this->purpose,
            'other_purpose' => $this->other_purpose,
            'car' => new CarResource($this->whenLoaded('car')),
            'customer' => $this->whenLoaded('customer') ? new CustomerResource($this->customer) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
