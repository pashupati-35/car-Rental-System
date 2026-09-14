<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class BlogDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $content = null,
        public ?string $image = null,
        public ?string $author_name = null,
        public ?string $author_image = null,
        public ?int $category_id = null,
        public ?string $publish_date = null,
        public ?string $type = 'blog',
        public ?int $is_active = 1,
        public ?string $event_date = null,
        public ?string $event_end = null,
        public ?string $meta_title = null,
        public ?string $meta_description = null,
        public ?string $social_share_image = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            content: $data['content'] ?? null,
            image: $data['image'] ?? null,
            author_name: $data['author_name'] ?? null,
            author_image: $data['author_image'] ?? null,
            category_id: isset($data['category_id']) ? (int) $data['category_id'] : null,
            publish_date: $data['publish_date'] ?? null,
            type: $data['type'] ?? 'blog',
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
            event_date: $data['event_date'] ?? null,
            event_end: $data['event_end'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            social_share_image: $data['social_share_image'] ?? null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            content: $model->content,
            image: $model->image,
            author_name: $model->author_name,
            author_image: $model->author_image,
            category_id: $model->category_id,
            publish_date: $model->publish_date,
            type: $model->type,
            is_active: $model->is_active,
            event_date: $model->event_date,
            event_end: $model->event_end,
            meta_title: $model->meta_title,
            meta_description: $model->meta_description,
            social_share_image: $model->social_share_image,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => $this->image,
            'author_name' => $this->author_name,
            'author_image' => $this->author_image,
            'category_id' => $this->category_id,
            'publish_date' => $this->publish_date,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'event_date' => $this->event_date,
            'event_end' => $this->event_end,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'social_share_image' => $this->social_share_image,
        ], fn ($value) => $value !== null);
    }
}
