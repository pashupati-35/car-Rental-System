<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class NewsAndUpdatesDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $published_date = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            image: $data['image'] ?? null,
            published_date: $data['published_date'] ?? null,
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
            image: $model->image,
            published_date: $model->published_date,
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
            'image' => $this->image,
            'published_date' => $this->published_date,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
