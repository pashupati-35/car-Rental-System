<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class SliderDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?int $slider_type_id = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $link = null,
        public ?int $position = null,
        public ?int $new_tab = 0,
        public ?string $heading_text = null,
        public ?string $sub_heading_text = null,
        public ?string $button_text = null,
        public ?int $show_button = 0,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            slider_type_id: isset($data['slider_type_id']) ? (int) $data['slider_type_id'] : null,
            description: $data['description'] ?? null,
            image: $data['image'] ?? null,
            link: $data['link'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            new_tab: isset($data['new_tab']) ? (int) $data['new_tab'] : 0,
            heading_text: $data['heading_text'] ?? null,
            sub_heading_text: $data['sub_heading_text'] ?? null,
            button_text: $data['button_text'] ?? null,
            show_button: isset($data['show_button']) ? (int) $data['show_button'] : 0,
            is_active: isset($data['is_active']) ? (int) $data['is_active'] : 1,
        );
    }

    public static function fromModel(Model $model): self
    {
        return new self(
            id: $model->id,
            title: $model->title,
            slug: $model->slug,
            slider_type_id: $model->slider_type_id,
            description: $model->description,
            image: $model->image,
            link: $model->link,
            position: $model->position,
            new_tab: $model->new_tab,
            heading_text: $model->heading_text,
            sub_heading_text: $model->sub_heading_text,
            button_text: $model->button_text,
            show_button: $model->show_button,
            is_active: $model->is_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'slider_type_id' => $this->slider_type_id,
            'description' => $this->description,
            'image' => $this->image,
            'link' => $this->link,
            'position' => $this->position,
            'new_tab' => $this->new_tab,
            'heading_text' => $this->heading_text,
            'sub_heading_text' => $this->sub_heading_text,
            'button_text' => $this->button_text,
            'show_button' => $this->show_button,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
