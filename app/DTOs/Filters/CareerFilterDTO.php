<?php

namespace App\DTOs\Filters;

readonly class CareerFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $employment_type = null,
        public ?int $is_active = null,
        public ?int $per_page = 20,
        public ?int $page = 1,
        public ?string $sort_by = 'position',
        public ?string $sort_dir = 'ASC',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? ($data['title'] ?? null),
            employment_type: $data['employment_type'] ?? null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : null,
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : (isset($data['per_pages']) ? (int) $data['per_pages'] : 20),
            page: isset($data['page']) ? (int) $data['page'] : 1,
            sort_by: $data['sort_by'] ?? 'position',
            sort_dir: $data['sort_dir'] ?? 'ASC',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'employment_type' => $this->employment_type,
            'is_active' => $this->is_active,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
