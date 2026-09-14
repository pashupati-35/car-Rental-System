<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class PopupDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?string $link = null,
        public ?string $type = null,
        public ?string $video_url = null,
        public ?string $location = null,
        public ?int $show_location = 0,
        public ?string $image = null,
        public ?string $start_date = null,
        public ?string $end_date = null,
        public ?int $position = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            link: $data['link'] ?? null,
            type: $data['type'] ?? null,
            video_url: $data['video_url'] ?? null,
            location: $data['location'] ?? null,
            show_location: isset($data['show_location']) ? (int) $data['show_location'] : 0,
            image: $data['image'] ?? null,
            start_date: $data['start_date'] ?? null,
            end_date: $data['end_date'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
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
            link: $model->link,
            type: $model->type,
            video_url: $model->video_url,
            location: $model->location,
            show_location: $model->show_location,
            image: $model->image,
            start_date: $model->start_date,
            end_date: $model->end_date,
            position: $model->position,
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
            'link' => $this->link,
            'type' => $this->type,
            'video_url' => $this->video_url,
            'location' => $this->location,
            'show_location' => $this->show_location,
            'image' => $this->image,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
