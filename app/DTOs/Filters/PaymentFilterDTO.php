<?php

namespace App\DTOs\Filters;

readonly class PaymentFilterDTO
{
    public function __construct(
        public ?int $booking_id = null,
        public ?int $customer_id = null,
        public ?int $car_id = null,
        public ?string $status = null,
        public ?string $created_from = null,
        public ?string $created_to = null,
        public ?float $amount_min = null,
        public ?float $amount_max = null,
        public ?int $per_page = 15,
        public ?int $page = 1,
        public ?string $sort_by = 'created_at',
        public ?string $sort_dir = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            booking_id: $data['booking_id'] ?? null,
            customer_id: $data['customer_id'] ?? null,
            car_id: $data['car_id'] ?? null,
            status: $data['status'] ?? null,
            created_from: $data['created_from'] ?? null,
            created_to: $data['created_to'] ?? null,
            amount_min: $data['amount_min'] ?? null,
            amount_max: $data['amount_max'] ?? null,
            per_page: $data['per_page'] ?? 15,
            page: $data['page'] ?? 1,
            sort_by: $data['sort_by'] ?? 'created_at',
            sort_dir: $data['sort_dir'] ?? 'desc',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'booking_id' => $this->booking_id,
            'customer_id' => $this->customer_id,
            'car_id' => $this->car_id,
            'status' => $this->status,
            'created_from' => $this->created_from,
            'created_to' => $this->created_to,
            'amount_min' => $this->amount_min,
            'amount_max' => $this->amount_max,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
