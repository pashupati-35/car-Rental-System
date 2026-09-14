<?php

namespace App\DTOs\Filters;

readonly class BlogFilterDTO
{
    public function __construct(
        public ?string $title = null,
        public ?string $type = null,
        public ?int $category_id = null,
        public ?string $publish_date_from = null,
        public ?string $publish_date_to = null,
        public ?int $is_active = null,
        public ?string $filter_by = null,
        public ?int $per_page = 20,
        public ?int $page = 1,
        public ?string $sort_by = 'id',
        public ?string $sort_dir = 'DESC',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            type: $data['type'] ?? null,
            category_id: isset($data['category_id']) ? (int) $data['category_id'] : null,
            publish_date_from: $data['publish_date_from'] ?? null,
            publish_date_to: $data['publish_date_to'] ?? null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : null,
            filter_by: $data['filter_by'] ?? null,
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : (isset($data['per_pages']) ? (int) $data['per_pages'] : 20),
            page: isset($data['page']) ? (int) $data['page'] : 1,
            sort_by: $data['sort_by'] ?? 'id',
            sort_dir: $data['sort_dir'] ?? 'DESC',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'publish_date_from' => $this->publish_date_from,
            'publish_date_to' => $this->publish_date_to,
            'is_active' => $this->is_active,
            'filter_by' => $this->filter_by,
            'per_page' => $this->per_page,
            'page' => $this->page,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ], fn ($value) => $value !== null);
    }
}
