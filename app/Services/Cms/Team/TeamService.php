<?php

namespace App\Services\Cms\Team;

use App\Http\Resources\Cms\Team\TeamResource;
use App\Models\Cms\Team\Team;
use App\Services\Service;

class TeamService extends Service
{
    protected $uploadPath = 'team';

    public function __construct(protected Team $team) {}

    public function paginate($limit, $request)
    {
        $teams = $this->team->where(function ($query) use ($request) {
            if ($request->filled('title')) {
                $query->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $query->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return TeamResource::collection($teams);
    }

    public function getAllActive()
    {
        $teams = $this->team->whereIsActive(1)->orderBy('position', 'ASC')->get();

        return TeamResource::collection($teams);
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $team = $this->team->whereId($id)->first();
                    if (! empty($team)) {
                        $v['position'] = ($i + 1);
                        $team->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function store($data)
    {
        try {
            $data['position'] = $this->team->orderBy('position', 'DESC')->first();
            $data['position'] = $data['position'] && $data['position']->position ? $data['position']->position + 1 : 1;
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            $team = $this->team->create($data);

            return new TeamResource($team);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($teamId)
    {
        $team = $this->team->find($teamId);
        if (! empty($team)) {
            return $team;
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $team = $this->find($id);
            if (! empty($data['image'])) {
                if (! empty($team->image)) {
                    $this->deleteFile($this->uploadPath, $team->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $team->update($data);
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function delete($id)
    {
        try {
            $team = $this->find($id);

            return $team->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->team->where($column, $value)->first();
    }

    public function getBySlug($slug)
    {
        return $this->team->whereSlug($slug)->whereIsActive(1)->first();
    }

    public function findByColumns($data, $all = false, $resource = true)
    {
        $result = $this->team->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            $result = $result->get();

            return $resource ? TeamResource::collection($result) : $result;
        } else {
            $result = $result->first();
            if (empty($result)) {
                return null;
            }

            return $resource ? new TeamResource($result) : $result;
        }
    }
}
