<?php

namespace App\Http\Resources\Cms\Career;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'position' => $this->position,
            'description' => $this->description,
            'opened_at' => $this->opened_at,
            'formatted_opened_at' => formatDate($this->opened_at),
            'display_expiry_date' => formatDate($this->expiry_date),
            'expiry_date' => $this->expiry_date,
            'expiry_date_format' => formatDate($this->expiry_date),
            'employment_type' => $this->employment_type,
            'min_qualification' => $this->min_qualification,
            'salary_offer' => $this->salary_offer,
            'no_of_vacancies' => $this->no_of_vacancies,
            'is_active' => $this->is_active,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword,
            'seo_description' => $this->seo_description,
            'career_application_count' => $this->career_applications->count() ?? 0,
        ];

    }
}
