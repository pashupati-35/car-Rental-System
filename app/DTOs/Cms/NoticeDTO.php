<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class NoticeDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $file = null,
        public ?int $position = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            file: $data['file'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            description: $model->description,
            file: $model->file,
            position: $model->position,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'file' => $this->file,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
