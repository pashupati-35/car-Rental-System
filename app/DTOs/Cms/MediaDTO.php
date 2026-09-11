<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class MediaDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $original_name = null,
        public ?string $path = null,
        public ?string $type = null,
        public ?int $size = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            original_name: $data['original_name'] ?? null,
            path: $data['path'] ?? null,
            type: $data['type'] ?? null,
            size: isset($data['size']) ? (int) $data['size'] : null,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            original_name: $model->original_name,
            path: $model->path,
            type: $model->type,
            size: $model->size,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'original_name' => $this->original_name,
            'path' => $this->path,
            'type' => $this->type,
            'size' => $this->size,
        ], fn ($value) => $value !== null);
    }
}
