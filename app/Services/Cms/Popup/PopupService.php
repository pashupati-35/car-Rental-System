<?php

namespace App\Services\Cms\Popup;

use App\DTOs\Filters\PopupFilterDTO;
use App\Http\Resources\Cms\Popup\PopupResource;
use App\Repositories\Cms\PopupRepositoryInterface;
use App\Services\Service;

class PopupService extends Service
{
    protected $uploadPath = 'popup';

    public function __construct(protected PopupRepositoryInterface $popupRepo) {}

    public function paginate(PopupFilterDTO $filter)
    {
        $popups = $this->popupRepo->getFilteredPaginated($filter);

        return PopupResource::collection($popups);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->popupRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $this->popupRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $popup = $this->popupRepo->find($id);

        return $popup ? new PopupResource($popup) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            } else {
                unset($data['image']);
            }

            return $this->popupRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->popupRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
