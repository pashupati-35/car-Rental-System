<?php

namespace App\Services\Cms\Page;

use App\Http\Resources\Cms\Page\PageResource;
use App\Models\Cms\Page\Page;

class PageService
{
    public function __construct(
        protected Page $page
    ) {}

    public function paginate($data, $limit)
    {
        $page = $this->page->orderBy('id', 'DESC')->where(function ($qry) use ($data) {
            if (isset($data['title']) && ! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['is_active']) && $data['is_active'] !== null && $data['is_active'] !== '') {
                $qry->where('is_active', $data['is_active']);
            }
        })->paginate($limit);

        return PageResource::collection($page);
    }

    public function store($data)
    {
        try {

            if (isset($data['seo_keyword'])) {
                $data['seo_keyword'] = implode(',', $data['seo_keyword']);
            }
            $page = $this->page->create($data);

            return $page;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $page = $this->page->find($id);
        if (! empty($page)) {
            return new PageResource($page);
        }

        return null;
    }

    public function findBySlug($slug)
    {
        $page = $this->page->whereSlug($slug)->first();
        if (! empty($page)) {
            return new PageResource($page);
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            if (isset($data['seo_keyword'])) {
                $data['seo_keyword'] = implode(',', $data['seo_keyword']);
            }
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $page = $this->getById($id);

            return $page->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $page = $this->getById($id);

            return $page->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->page->where($column, $value)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->page->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return PageResource::collection($result);
        } else {
            return new PageResource($result);
        }
    }

    public function searchByKey($key, $limit, $limitText = 100)
    {
        $results = $this->page
            ->where('title', 'like', '%'.$key.'%')
            ->orWhere('content', 'like', '%'.$key.'%')
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $pages = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'description' => trim(substr($p->content, 0, $limitText)).'...',
                    'route' => route('show.page', $p->slug),
                ];
                array_push($pages, $temp);
            }
        }

        return $pages;
    }
}
