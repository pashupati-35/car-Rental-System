<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class AlbumDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $cover_image = null,
        public ?string $description = null,
        public ?string $event_date = null,
        public ?string $tags = null,
        public ?int $position = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            cover_image: $data['cover_image'] ?? null,
            description: $data['description'] ?? null,
            event_date: $data['event_date'] ?? null,
            tags: $data['tags'] ?? null,
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
            cover_image: $model->cover_image,
            description: $model->description,
            event_date: $model->event_date,
            tags: $model->tags,
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
            'cover_image' => $this->cover_image,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'tags' => $this->tags,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
