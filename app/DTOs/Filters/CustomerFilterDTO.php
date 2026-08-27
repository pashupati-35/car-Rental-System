<?php

namespace App\DTOs\Filters;

readonly class CustomerFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $gender = null,
        public ?int $owner_id = null,
        public ?int $admin_id = null,
        public ?int $per_page = 15,
        public ?int $page = 1,
        public ?string $sort_by = 'created_at',
        public ?string $sort_dir = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            gender: $data['gender'] ?? null,
            owner_id: $data['owner_id'] ?? null,
            admin_id: $data['admin_id'] ?? null,
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
            'gender' => $this->gender,
            'owner_id' => $this->owner_id,
            'admin_id' => $this->admin_id,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
