<?php

namespace App\DTOs\Cms;

use Illuminate\Database\Eloquent\Model;

readonly class CareerDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?int $position = null,
        public ?string $description = null,
        public ?string $opened_at = null,
        public ?string $expiry_date = null,
        public ?string $employment_type = null,
        public ?string $min_qualification = null,
        public ?string $salary_offer = null,
        public ?int $no_of_vacancies = null,
        public ?string $seo_title = null,
        public ?string $seo_description = null,
        public ?string $seo_keywords = null,
        public ?int $is_active = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            description: $data['description'] ?? null,
            opened_at: $data['opened_at'] ?? null,
            expiry_date: $data['expiry_date'] ?? null,
            employment_type: $data['employment_type'] ?? null,
            min_qualification: $data['min_qualification'] ?? null,
            salary_offer: $data['salary_offer'] ?? null,
            no_of_vacancies: isset($data['no_of_vacancies']) ? (int) $data['no_of_vacancies'] : null,
            seo_title: $data['seo_title'] ?? null,
            seo_description: $data['seo_description'] ?? null,
            seo_keywords: $data['seo_keywords'] ?? null,
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
            description: $model->description,
            opened_at: $model->opened_at,
            expiry_date: $model->expiry_date,
            employment_type: $model->employment_type,
            min_qualification: $model->min_qualification,
            salary_offer: $model->salary_offer,
            no_of_vacancies: $model->no_of_vacancies,
            seo_title: $model->seo_title,
            seo_description: $model->seo_description,
            seo_keywords: $model->seo_keywords,
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
            'description' => $this->description,
            'opened_at' => $this->opened_at,
            'expiry_date' => $this->expiry_date,
            'employment_type' => $this->employment_type,
            'min_qualification' => $this->min_qualification,
            'salary_offer' => $this->salary_offer,
            'no_of_vacancies' => $this->no_of_vacancies,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'is_active' => $this->is_active,
        ], fn ($value) => $value !== null);
    }
}
