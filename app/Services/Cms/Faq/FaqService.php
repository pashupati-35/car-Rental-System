<?php

namespace App\Services\Cms\Faq;

use App\DTOs\Filters\FaqFilterDTO;
use App\Http\Resources\Cms\Faq\FaqResource;
use App\Repositories\Cms\FaqRepositoryInterface;
use App\Services\Service;

class FaqService extends Service
{
    protected $uploadPath = 'faq';

    public function __construct(protected FaqRepositoryInterface $faqRepo) {}

    public function paginate(FaqFilterDTO $filter)
    {
        $faqs = $this->faqRepo->getFilteredPaginated($filter);
        return FaqResource::collection($faqs);
    }

    public function sort(array $data): bool
    {
        return $this->faqRepo->updatePositions($data);
    }

    public function store(array $data)
    {
        try {
            return $this->faqRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $faq = $this->faqRepo->find($id);
        return $faq ? new FaqResource($faq) : null;
    }

    public function update($id, array $data)
    {
        try {
            return $this->faqRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->faqRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
