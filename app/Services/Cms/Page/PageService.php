<?php

namespace App\Services\Cms\Page;

use App\DTOs\Filters\PageFilterDTO;
use App\Http\Resources\Cms\Page\PageResource;
use App\Repositories\Cms\PageRepositoryInterface;
use App\Services\Service;

class PageService extends Service
{
    public function __construct(
        protected PageRepositoryInterface $pageRepo
    ) {}

    public function paginate(PageFilterDTO $filter)
    {
        $pages = $this->pageRepo->getFilteredPaginated($filter);

        return PageResource::collection($pages);
    }

    public function store(array $data)
    {
        try {
            if (isset($data['seo_keyword']) && is_array($data['seo_keyword'])) {
                $data['seo_keyword'] = implode(',', $data['seo_keyword']);
            }

            return $this->pageRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $page = $this->pageRepo->find($id);

        return $page ? new PageResource($page) : null;
    }

    public function findBySlug(string $slug)
    {
        $page = $this->pageRepo->findBySlug($slug);

        return $page ? new PageResource($page) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (isset($data['seo_keyword']) && is_array($data['seo_keyword'])) {
                $data['seo_keyword'] = implode(',', $data['seo_keyword']);
            }

            return $this->pageRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->pageRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
