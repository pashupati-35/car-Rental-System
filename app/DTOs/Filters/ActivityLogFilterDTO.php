<?php

namespace App\DTOs\Filters;

readonly class ActivityLogFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $log_type = null,
        public ?string $causer_type = null,
        public ?int $user_id = null,
        public ?int $owner_id = null,
        public ?int $customer_id = null,
        public ?int $per_page = 20,
        public ?int $page = 1,
        public ?string $sort_by = 'id',
        public ?string $sort_dir = 'DESC',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            log_type: $data['log_type'] ?? null,
            causer_type: $data['causer_type'] ?? null,
            user_id: isset($data['user_id']) ? (int) $data['user_id'] : (isset($data['employee_id']) ? (int) $data['employee_id'] : null),
            owner_id: isset($data['owner_id']) ? (int) $data['owner_id'] : null,
            customer_id: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : 20,
            page: isset($data['page']) ? (int) $data['page'] : 1,
            sort_by: $data['sort_by'] ?? 'id',
            sort_dir: $data['sort_dir'] ?? 'DESC',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'log_type' => $this->log_type,
            'causer_type' => $this->causer_type,
            'user_id' => $this->user_id,
            'owner_id' => $this->owner_id,
            'customer_id' => $this->customer_id,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
