<?php

namespace App\Services\Cms\Career;

use App\Http\Resources\Cms\Career\CareerResource;
use App\Models\Cms\Career\Career;
use Carbon\Carbon;

class CareerService
{
    public function __construct(
        protected Career $career
    ) {}

    public function paginate($data, $limit)
    {
        $career = $this->career->orderBy('id', 'DESC')->where(function ($qry) use ($data) {
            if (isset($data['title']) && ! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['is_active']) && $data['is_active'] !== '') {
                $qry->where('is_active', (int) $data['is_active']);
            }
        })->paginate($limit);

        return CareerResource::collection($career);
    }

    public function getAllActive()
    {
        try {
            $careers = $this->career->whereIsActive(1)->where('opened_at', '<=', Carbon::now())->where('expiry_date', '>=', Carbon::now())->orderBy('id', 'DESC')->get();

            return $careers;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function store($data)
    {
        try {
            $data['opened_at'] = isset($data['opened_at']) ? date('Y/m/d', strtotime($data['opened_at'])) : null;
            $data['expiry_date'] = isset($data['expiry_date']) ? date('Y/m/d', strtotime($data['expiry_date'])) : null;

            return $this->career->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $career = $this->career->find($id);
        if (! empty($career)) {
            return new CareerResource($career);
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $career = $this->getById($id);
            $data['opened_at'] = isset($data['opened_at']) ? date('Y/m/d', strtotime($data['opened_at'])) : null;
            $data['expiry_date'] = isset($data['expiry_date']) ? date('Y/m/d', strtotime($data['expiry_date'])) : null;

            return $career->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getBySlug($slug)
    {
        $career = $this->career->whereSlug($slug)->whereIsActive(1)->first();

        return $career;
    }

    public function delete($id)
    {
        try {
            $career = $this->getById($id);

            return $career->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->career->where($column, $value)->first();
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->career->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            if (! empty($limit)) {
                $response = $response->take($limit);
            }

            return CareerResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new CareerResource($response);
        }
    }

    public function searchByKey($key, $limit, $limitText = 100)
    {
        $results = $this->career
            ->where(function ($qry) use ($key) {
                if (! in_array($key, ['car', 'vac', 'career', 'careers', 'vacancies', 'vacancy'])) {
                    $qry->where('title', 'like', '%'.$key.'%');
                }
            })
            ->where('opened_at', '<=', Carbon::now())->where('expiry_date', '>=', Carbon::now())
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $careers = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'description' => trim(substr($p->content, 0, $limitText)).'...',
                    'route' => route('apply-now', $p->slug),
                ];
                array_push($careers, $temp);
            }
        }

        return $careers;
    }
}
