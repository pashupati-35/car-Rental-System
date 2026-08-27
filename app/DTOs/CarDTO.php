<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

readonly class CarDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $owner_id = null,
        public ?string $car_name = null,
        public ?string $car_model = null,
        public ?string $car_number = null,
        public ?int $number_of_seats = null,
        public ?float $car_price_per_km = null,
        public ?float $car_price_per_day = null,
        public ?string $car_photo = null,
        public ?string $blue_book_photo = null,
        public ?string $driver_photo = null,
        public ?string $licence_photo = null,
        public ?string $driver_name = null,
        public ?string $driver_number = null,
        public ?string $driving_experience = null,
        public ?string $available = null,
        public ?string $description = null,
        public ?string $status = 'pending',
        public ?string $fuel_type = null,
        public ?string $transmission = null,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            owner_id: $data['owner_id'] ?? null,
            car_name: $data['car_name'] ?? null,
            car_model: $data['car_model'] ?? null,
            car_number: $data['car_number'] ?? null,
            number_of_seats: $data['number_of_seats'] ?? null,
            car_price_per_km: $data['car_price_per_km'] ?? null,
            car_price_per_day: $data['car_price_per_day'] ?? null,
            car_photo: $data['car_photo'] ?? null,
            blue_book_photo: $data['blue_book_photo'] ?? null,
            driver_photo: $data['driver_photo'] ?? null,
            licence_photo: $data['licence_photo'] ?? null,
            driver_name: $data['driver_name'] ?? null,
            driver_number: $data['driver_number'] ?? null,
            driving_experience: $data['driving_experience'] ?? null,
            available: $data['available'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'pending',
            fuel_type: $data['fuel_type'] ?? null,
            transmission: $data['transmission'] ?? null,
            created_at: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            owner_id: $model->owner_id,
            car_name: $model->car_name,
            car_model: $model->car_model,
            car_number: $model->car_number,
            number_of_seats: $model->number_of_seats,
            car_price_per_km: $model->car_price_per_km,
            car_price_per_day: $model->car_price_per_day,
            car_photo: $model->car_photo,
            blue_book_photo: $model->blue_book_photo,
            driver_photo: $model->driver_photo,
            licence_photo: $model->licence_photo,
            driver_name: $model->driver_name,
            driver_number: $model->driver_number,
            driving_experience: $model->driving_experience,
            available: $model->available,
            description: $model->description,
            status: $model->status,
            fuel_type: $model->fuel_type ?? null,
            transmission: $model->transmission ?? null,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'owner_id' => $this->owner_id,
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
            'driver_name' => $this->driver_name,
            'driver_number' => $this->driver_number,
            'driving_experience' => $this->driving_experience,
            'available' => $this->available,
            'description' => $this->description,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null);
    }
}
