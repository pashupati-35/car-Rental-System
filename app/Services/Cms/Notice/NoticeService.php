<?php

namespace App\Services\Cms\Notice;

use App\DTOs\Filters\NoticeFilterDTO;
use App\Http\Resources\Cms\Notice\NoticeResource;
use App\Repositories\Cms\NoticeRepositoryInterface;
use App\Services\Service;

class NoticeService extends Service
{
    public function __construct(protected NoticeRepositoryInterface $noticeRepo) {}

    public function paginate(NoticeFilterDTO $filter)
    {
        $notices = $this->noticeRepo->getFilteredPaginated($filter);

        return NoticeResource::collection($notices);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->noticeRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (isset($data['user_type']) && is_array($data['user_type'])) {
                $data['user_type'] = implode(',', $data['user_type']);
            }

            return $this->noticeRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $notice = $this->noticeRepo->find($id);

        return $notice ? new NoticeResource($notice) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (isset($data['user_type']) && is_array($data['user_type'])) {
                $data['user_type'] = implode(',', $data['user_type']);
            }

            return $this->noticeRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->noticeRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
