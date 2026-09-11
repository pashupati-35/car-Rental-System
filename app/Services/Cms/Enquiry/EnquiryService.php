<?php

namespace App\Services\Cms\Enquiry;

use App\DTOs\Filters\EnquiryFilterDTO;
use App\Http\Resources\Cms\Enquiry\EnquiryResource;
use App\Repositories\Cms\EnquiryRepositoryInterface;
use App\Services\Service;

class EnquiryService extends Service
{
    public function __construct(protected EnquiryRepositoryInterface $enquiryRepo) {}

    public function paginate(EnquiryFilterDTO $filter)
    {
        $enquiries = $this->enquiryRepo->getFilteredPaginated($filter);

        return EnquiryResource::collection($enquiries);
    }

    public function create(array $data)
    {
        try {
            return $this->enquiryRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function show(string|int $id)
    {
        $enquiry = $this->enquiryRepo->find($id);

        return $enquiry ? new EnquiryResource($enquiry) : null;
    }

    public function update(string|int $id, array $data)
    {
        try {
            return $this->enquiryRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete(string|int $id): bool
    {
        try {
            return $this->enquiryRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
