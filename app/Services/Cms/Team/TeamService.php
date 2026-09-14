<?php

namespace App\Services\Cms\Team;

use App\DTOs\Filters\TeamFilterDTO;
use App\Http\Resources\Cms\Team\TeamResource;
use App\Repositories\Cms\TeamRepositoryInterface;
use App\Services\Service;

class TeamService extends Service
{
    protected $uploadPath = 'team';

    public function __construct(protected TeamRepositoryInterface $teamRepo) {}

    public function paginate(TeamFilterDTO $filter)
    {
        $teams = $this->teamRepo->getFilteredPaginated($filter);

        return TeamResource::collection($teams);
    }

    public function getAllActive()
    {
        return TeamResource::collection($this->teamRepo->where('is_active', 1)->get());
    }

    public function sort(array $data)
    {
        return $this->teamRepo->updatePositions($data);
    }

    public function store(array $data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            $team = $this->teamRepo->create($data);

            return new TeamResource($team);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        return $this->teamRepo->find($id);
    }

    public function update($id, array $data)
    {
        try {
            $team = $this->teamRepo->findOrFail($id);
            if (! empty($data['image'])) {
                if (! empty($team->image)) {
                    $this->deleteFile($this->uploadPath, $team->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $this->teamRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $team = $this->teamRepo->find($id);
            if ($team && ! empty($team->image)) {
                $this->deleteFile($this->uploadPath, $team->image);
            }

            return $this->teamRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
