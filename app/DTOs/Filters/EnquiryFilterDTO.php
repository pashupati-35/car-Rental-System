<?php

namespace App\DTOs\Filters;

readonly class EnquiryFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $mark_as_read = null,
        public ?int $per_page = 20,
        public ?int $page = 1,
        public ?string $sort_by = 'id',
        public ?string $sort_dir = 'DESC',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            mark_as_read: isset($data['mark_as_read']) ? (bool) $data['mark_as_read'] : null,
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
            'mark_as_read' => $this->mark_as_read,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
