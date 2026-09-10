<?php

namespace App\Http\Resources\Cms\Menu\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
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
            'page_id' => $this->page_id,
            'page' => $this->whenLoaded('page', function () {
                return [
                    'title' => $this->page->title,
                ];
            }),
            'blog_id' => $this->blog_id,
            'blog' => $this->whenLoaded('blog', function () {
                return [
                    'title' => $this->blog->title,
                ];
            }),
            'menu_type' => $this->menu_type,
            'menu_id' => $this->menu_id,
            'menu' => $this->whenLoaded('menu', function () {
                return [
                    'title' => $this->menu->title,
                ];
            }),
            'menu_location_type' => $this->menu_location_type,
            'link' => $this->link,
            'position' => $this->position,
            'depth' => $this->depth,
            'new_tab' => $this->new_tab,
            'is_active' => ($this->is_active) ? true : false,
        ];

        return $resource;
    }
}
