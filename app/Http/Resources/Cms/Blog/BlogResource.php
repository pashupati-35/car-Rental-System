<?php

namespace App\Http\Resources\Cms\Blog;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'publish_date' => $this->publish_date,
            'formatted_publish_date' => formatDate($this->publish_date),
            'author_name' => $this->author_name,
            'author_image_path' => $this->author_image_path,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'file_path' => $this->file_path ?? $this->image_path,
            'content' => $this->content,
            'seo_title' => $this->seo_title,
            'seo_keyword' => $this->seo_keyword ? explode(',', $this->seo_keyword) : null,
            'seo_description' => $this->seo_description,
            'social_share_image' => $this->social_share_image,
            'social_share_description' => $this->social_share_description,
            'category_id' => $this->category_ids,
            'categories' => $this->categories,
            'is_active' => $this->is_active ? true : false,
        ];

        return $resource;
    }
}
