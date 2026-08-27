<?php

namespace App\DTOs\Filters;

readonly class CarFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $fuel_type = null,
        public ?string $transmission = null,
        public ?string $status = null,
        public ?string $available = null,
        public ?int $owner_id = null,
        public ?int $per_page = 15,
        public ?int $page = 1,
        public ?string $sort_by = 'created_at',
        public ?string $sort_dir = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            fuel_type: $data['fuel_type'] ?? null,
            transmission: $data['transmission'] ?? null,
            status: $data['status'] ?? null,
            available: $data['available'] ?? null,
            owner_id: $data['owner_id'] ?? null,
            per_page: $data['per_page'] ?? 15,
            page: $data['page'] ?? 1,
            sort_by: $data['sort_by'] ?? 'created_at',
            sort_dir: $data['sort_dir'] ?? 'desc',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'status' => $this->status,
            'available' => $this->available,
            'owner_id' => $this->owner_id,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }

    public function hasFilters(): bool
    {
        return $this->search !== null
            || $this->fuel_type !== null
            || $this->transmission !== null
            || $this->status !== null
            || $this->available !== null
            || $this->owner_id !== null;
    }
}
