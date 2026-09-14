<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class TestimonialDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $type = null,
        public ?string $job_title = null,
        public ?string $image = null,
        public ?int $rating = 5,
        public ?string $status = null,
        public ?int $position = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            type: $data['type'] ?? null,
            job_title: $data['job_title'] ?? null,
            image: $data['image'] ?? null,
            rating: isset($data['rating']) ? (int) $data['rating'] : 5,
            status: $data['status'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            name: $model->name,
            description: $model->description,
            type: $model->type,
            job_title: $model->job_title,
            image: $model->image,
            rating: $model->rating,
            status: $model->status,
            position: $model->position,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'job_title' => $this->job_title,
            'image' => $this->image,
            'rating' => $this->rating,
            'status' => $this->status,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
