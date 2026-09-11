<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class DownloadDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $preview_image = null,
        public ?string $description = null,
        public ?int $download_type_id = null,
        public ?int $position = null,
        public ?string $file = null,
        public ?string $type = null,
        public ?int $is_private = 0,
        public ?int $public_hidden = 0,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            preview_image: $data['preview_image'] ?? null,
            description: $data['description'] ?? null,
            download_type_id: isset($data['download_type_id']) ? (int) $data['download_type_id'] : null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            file: $data['file'] ?? null,
            type: $data['type'] ?? null,
            is_private: isset($data['is_private']) ? (int) $data['is_private'] : 0,
            public_hidden: isset($data['public_hidden']) ? (int) $data['public_hidden'] : 0,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            preview_image: $model->preview_image,
            description: $model->description,
            download_type_id: $model->download_type_id,
            position: $model->position,
            file: $model->file,
            type: $model->type,
            is_private: $model->is_private,
            public_hidden: $model->public_hidden,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'preview_image' => $this->preview_image,
            'description' => $this->description,
            'download_type_id' => $this->download_type_id,
            'position' => $this->position,
            'file' => $this->file,
            'type' => $this->type,
            'is_private' => $this->is_private,
            'public_hidden' => $this->public_hidden,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
