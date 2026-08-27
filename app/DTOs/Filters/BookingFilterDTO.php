<?php

namespace App\DTOs\Filters;

readonly class BookingFilterDTO
{
    public function __construct(
        public ?int $car_id = null,
        public ?int $customer_id = null,
        public ?string $status = null,
        public ?string $pick_up_from = null,
        public ?string $pick_up_to = null,
        public ?string $last_date_from = null,
        public ?string $last_date_to = null,
        public ?int $per_page = 15,
        public ?int $page = 1,
        public ?string $sort_by = 'created_at',
        public ?string $sort_dir = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            car_id: $data['car_id'] ?? null,
            customer_id: $data['customer_id'] ?? null,
            status: $data['status'] ?? null,
            pick_up_from: $data['pick_up_from'] ?? null,
            pick_up_to: $data['pick_up_to'] ?? null,
            last_date_from: $data['last_date_from'] ?? null,
            last_date_to: $data['last_date_to'] ?? null,
            per_page: $data['per_page'] ?? 15,
            page: $data['page'] ?? 1,
            sort_by: $data['sort_by'] ?? 'created_at',
            sort_dir: $data['sort_dir'] ?? 'desc',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'car_id' => $this->car_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'pick_up_from' => $this->pick_up_from,
            'pick_up_to' => $this->pick_up_to,
            'last_date_from' => $this->last_date_from,
            'last_date_to' => $this->last_date_to,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
