<?php

namespace App\Services\Cms\ContactUs;

use App\DTOs\Filters\ContactUsFilterDTO;
use App\Http\Resources\Cms\ContactUs\ContactUsResource;
use App\Repositories\Cms\ContactUsRepositoryInterface;
use App\Services\Service;

class ContactUsService extends Service
{
    public function __construct(protected ContactUsRepositoryInterface $contactRepo) {}

    public function paginate(ContactUsFilterDTO $filter)
    {
        $contacts = $this->contactRepo->getFilteredPaginated($filter);
        return ContactUsResource::collection($contacts);
    }

    public function store(array $data)
    {
        try {
            return $this->contactRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $contact = $this->contactRepo->find($id);
        return $contact ? new ContactUsResource($contact) : null;
    }

    public function update($id, array $data)
    {
        try {
            return $this->contactRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->contactRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
