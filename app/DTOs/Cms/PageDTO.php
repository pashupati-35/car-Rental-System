<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class PageDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $seo_title = null,
        public ?string $seo_description = null,
        public ?string $seo_keyword = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            seo_title: $data['seo_title'] ?? null,
            seo_description: $data['seo_description'] ?? null,
            seo_keyword: $data['seo_keyword'] ?? null,
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
            seo_title: $model->seo_title,
            seo_description: $model->seo_description,
            seo_keyword: $model->seo_keyword,
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
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keyword' => $this->seo_keyword,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
