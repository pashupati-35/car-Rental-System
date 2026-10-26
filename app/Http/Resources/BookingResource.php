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
            'pick_up_date' => formatUserDate($this->pick_up_date, 'Y-m-d'),
            'pick_up_date_formatted' => formatUserDate($this->pick_up_date),
            'last_date' => formatUserDate($this->last_date, 'Y-m-d'),
            'last_date_formatted' => formatUserDate($this->last_date),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'car_id' => $this->car_id,
            'customer_id' => $this->customer_id,
            'purpose' => $this->purpose,
            'other_purpose' => $this->other_purpose,
            'car' => new CarResource($this->whenLoaded('car')),
            'customer' => $this->whenLoaded('customer') ? new CustomerResource($this->customer) : null,
            'created_at' => convertToUserTimezone($this->created_at)?->toIso8601String(),
            'created_at_formatted' => formatUserDateTime($this->created_at),
            'updated_at' => convertToUserTimezone($this->updated_at)?->toIso8601String(),
            'updated_at_formatted' => formatUserDateTime($this->updated_at),
        ];
    }
}
