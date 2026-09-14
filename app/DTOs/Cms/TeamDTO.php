<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class TeamDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?int $position = null,
        public ?string $image = null,
        public ?string $description = null,
        public ?string $job_title = null,
        public ?string $fb_url = null,
        public ?string $linked_url = null,
        public ?string $whatsapp = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            image: $data['image'] ?? null,
            description: $data['description'] ?? null,
            job_title: $data['job_title'] ?? null,
            fb_url: $data['fb_url'] ?? null,
            linked_url: $data['linked_url'] ?? null,
            whatsapp: $data['whatsapp'] ?? null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            position: $model->position,
            image: $model->image,
            description: $model->description,
            job_title: $model->job_title,
            fb_url: $model->fb_url,
            linked_url: $model->linked_url,
            whatsapp: $model->whatsapp,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'position' => $this->position,
            'image' => $this->image,
            'description' => $this->description,
            'job_title' => $this->job_title,
            'fb_url' => $this->fb_url,
            'linked_url' => $this->linked_url,
            'whatsapp' => $this->whatsapp,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
