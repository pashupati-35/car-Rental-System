<?php

namespace App\DTOs\Filters;

readonly class ContactUsFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $is_read = null,
        public ?int $per_page = 20,
        public ?int $page = 1,
        public ?string $sort_by = 'id',
        public ?string $sort_dir = 'DESC',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            is_read: isset($data['is_read']) ? (bool) $data['is_read'] : null,
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : (isset($data['per_pages']) ? (int) $data['per_pages'] : 20),
            page: isset($data['page']) ? (int) $data['page'] : 1,
            sort_by: $data['sort_by'] ?? 'id',
            sort_dir: $data['sort_dir'] ?? 'DESC',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'is_read' => $this->is_read,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
