<?php

namespace App\Http\Resources\Cms\Download;

use App\Http\Resources\Cms\Download\Type\DownloadTypeResoruce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DownloadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'download_type_id' => $this->download_type_id,
            'download_type' => $this->whenLoaded('type', function () {
                return DownloadTypeResoruce::make($this->type);
            }),
            'position' => $this->position,
            'type_name' => ! empty($this->type) ? $this->type->title : null,
            'file' => $this->file,
            'file_path' => $this->file_path,
            'image' => $this->preview_image ?? $this->file,
            'image_path' => $this->preview_image_path ?? $this->file_path,
            'preview_image' => $this->preview_image,
            'preview_image_path' => $this->preview_image_path,
            'is_private' => $this->is_private,
            'public_hidden' => $this->public_hidden,
            'is_active' => $this->is_active,
        ];

        return $resource;
    }
}
