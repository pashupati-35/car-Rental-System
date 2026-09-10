<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class ServiceDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $type = null,
        public ?int $is_active = 1,
        public ?int $position = null,
        public ?float $price = null,
        public ?string $seo_title = null,
        public ?string $seo_keyword = null,
        public ?string $seo_description = null,
        public ?string $social_share_image = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            image: $data['image'] ?? null,
            type: $data['type'] ?? null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
            position: isset($data['position']) ? (int) $data['position'] : null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            seo_title: $data['seo_title'] ?? null,
            seo_keyword: $data['seo_keyword'] ?? null,
            seo_description: $data['seo_description'] ?? null,
            social_share_image: $data['social_share_image'] ?? null,
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
            type: $model->type,
            is_active: $model->is_active,
            position: $model->position,
            price: $model->price,
            seo_title: $model->seo_title,
            seo_keyword: $model->seo_keyword,
            seo_description: $model->seo_description,
            social_share_image: $model->social_share_image,
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
            'type' => $this->type,
            'is_active' => $this->is_active,
            'position' => $this->position,
            'price' => $this->price,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword,
            'seo_description' => $this->seo_description,
            'social_share_image' => $this->social_share_image,
        ], fn ($value) => $value !== null);
    }
}
