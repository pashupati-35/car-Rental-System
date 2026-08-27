<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;

readonly class PaymentDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $booking_id = null,
        public ?int $customer_id = null,
        public ?int $car_id = null,
        public ?float $amount = null,
        public ?string $card_number = null,
        public ?string $expiry_date = null,
        public ?string $cvv = null,
        public ?string $status = 'pending',
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            booking_id: $data['booking_id'] ?? null,
            customer_id: $data['customer_id'] ?? null,
            car_id: $data['car_id'] ?? null,
            amount: $data['amount'] ?? null,
            card_number: $data['card_number'] ?? null,
            expiry_date: $data['expiry_date'] ?? null,
            cvv: $data['cvv'] ?? null,
            status: $data['status'] ?? 'pending',
            created_at: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            booking_id: $model->booking_id,
            customer_id: $model->customer_id,
            car_id: $model->car_id,
            amount: $model->amount,
            card_number: $model->card_number,
            expiry_date: $model->expiry_date,
            cvv: $model->cvv,
            status: $model->status,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'customer_id' => $this->customer_id,
            'car_id' => $this->car_id,
            'amount' => $this->amount,
            'card_number' => $this->card_number,
            'expiry_date' => $this->expiry_date,
            'cvv' => $this->cvv,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null);
    }
}
