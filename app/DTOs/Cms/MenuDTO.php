<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class MenuDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $menu_type = null,
        public ?string $header = null,
        public ?int $position = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            menu_type: $data['menu_type'] ?? null,
            header: $data['header'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            menu_type: $model->menu_type,
            header: $model->header,
            position: $model->position,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'menu_type' => $this->menu_type,
            'header' => $this->header,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
