<?php

namespace App\Services\Cms\Partner;

use App\DTOs\Filters\PartnerFilterDTO;
use App\Http\Resources\Cms\Partner\PartnerResource;
use App\Repositories\Cms\PartnerRepositoryInterface;
use App\Services\Service;

class PartnerService extends Service
{
    protected $uploadPath = 'our-partners';

    public function __construct(protected PartnerRepositoryInterface $partnerRepo) {}

    public function paginate(PartnerFilterDTO $filter)
    {
        $partners = $this->partnerRepo->getFilteredPaginated($filter);

        return PartnerResource::collection($partners);
    }

    public function store(array $data)
    {
        try {
            if (! empty($data['featured_photo'])) {
                $data['featured_photo'] = $this->uploadFile($data['featured_photo'], $this->uploadPath);
            }

            return $this->partnerRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $partner = $this->partnerRepo->find($id);

        return $partner ? new PartnerResource($partner) : null;
    }

    public function update($id, array $data)
    {
        try {
            $partner = $this->partnerRepo->find($id);
            if (! $partner) {
                return false;
            }

            if (! empty($data['featured_photo'])) {
                $data['featured_photo'] = $this->uploadFile($data['featured_photo'], $this->uploadPath);
            } else {
                unset($data['featured_photo']);
            }

            return $this->partnerRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->partnerRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
