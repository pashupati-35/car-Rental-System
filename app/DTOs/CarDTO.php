<?php

namespace App\DTOs;

class CarDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $owner_id = null,
        public ?string $car_name = null,
        public ?string $car_model = null,
        public ?string $car_number = null,
        public ?int $number_of_seats = null,
        public ?float $car_price_per_day = null,
        public ?string $car_photo = null,
        public ?string $description = null,
        public ?string $status = 'pending',
        public ?string $fuel_type = null,
        public ?string $transmission = null,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            owner_id: $model->owner_id,
            car_name: $model->car_name,
            car_model: $model->car_model,
            car_number: $model->car_number,
            number_of_seats: $model->number_of_seats,
            car_price_per_day: $model->car_price_per_day,
            car_photo: $model->car_photo,
            description: $model->description,
            status: $model->status,
            fuel_type: $model->fuel_type ?? null,
            transmission: $model->transmission ?? null,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }

    public static function from(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            owner_id: $data['owner_id'] ?? null,
            car_name: $data['car_name'] ?? null,
            car_model: $data['car_model'] ?? null,
            car_number: $data['car_number'] ?? null,
            number_of_seats: $data['number_of_seats'] ?? null,
            car_price_per_day: $data['car_price_per_day'] ?? null,
            car_photo: $data['car_photo'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'pending',
            fuel_type: $data['fuel_type'] ?? null,
            transmission: $data['transmission'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'car_name' => $this->car_name,
            'car_model' => $this->car_model,
            'car_number' => $this->car_number,
            'number_of_seats' => $this->number_of_seats,
            'car_price_per_day' => $this->car_price_per_day,
            'car_photo' => $this->car_photo,
            'description' => $this->description,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
