<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

readonly class BookingDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $pickup_location = null,
        public ?string $drop_location = null,
        public ?\DateTime $pick_up_date = null,
        public ?\DateTime $last_date = null,
        public ?float $total_price = null,
        public ?string $status = 'reserved',
        public ?int $car_id = null,
        public ?int $customer_id = null,
        public ?string $purpose = null,
        public ?string $other_purpose = null,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            pickup_location: $data['pickup_location'] ?? null,
            drop_location: $data['drop_location'] ?? null,
            pick_up_date: isset($data['pick_up_date']) ? self::parseDate($data['pick_up_date']) : null,
            last_date: isset($data['last_date']) ? self::parseDate($data['last_date']) : null,
            total_price: $data['total_price'] ?? null,
            status: $data['status'] ?? 'reserved',
            car_id: $data['car_id'] ?? null,
            customer_id: $data['customer_id'] ?? null,
            purpose: $data['purpose'] ?? null,
            other_purpose: $data['other_purpose'] ?? null,
            created_at: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            pickup_location: $model->pickup_location,
            drop_location: $model->drop_location,
            pick_up_date: $model->pick_up_date,
            last_date: $model->last_date,
            total_price: $model->total_price,
            status: $model->status,
            car_id: $model->car_id,
            customer_id: $model->customer_id,
            purpose: $model->purpose,
            other_purpose: $model->other_purpose,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
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
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null);
    }

    private static function parseDate(mixed $value): ?\DateTime
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        return $value ? new \DateTime($value) : null;
    }
}
