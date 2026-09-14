<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class FaqDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $short_description = null,
        public ?string $tags = null,
        public ?string $description = null,
        public ?string $seo_title = null,
        public ?string $seo_description = null,
        public ?string $seo_keyword = null,
        public ?int $position = null,
        public ?int $is_active = 1,
        public ?int $faq_category_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            short_description: $data['short_description'] ?? null,
            tags: $data['tags'] ?? null,
            description: $data['description'] ?? null,
            seo_title: $data['seo_title'] ?? null,
            seo_description: $data['seo_description'] ?? null,
            seo_keyword: $data['seo_keyword'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
            faq_category_id: isset($data['faq_category_id']) ? (int) $data['faq_category_id'] : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            short_description: $model->short_description,
            tags: $model->tags,
            description: $model->description,
            seo_title: $model->seo_title,
            seo_description: $model->seo_description,
            seo_keyword: $model->seo_keyword,
            position: $model->position,
            is_active: $model->is_active,
            faq_category_id: $model->faq_category_id,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'tags' => $this->tags,
            'description' => $this->description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keyword' => $this->seo_keyword,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'faq_category_id' => $this->faq_category_id,
        ], fn ($value) => $value !== null);
    }
}
