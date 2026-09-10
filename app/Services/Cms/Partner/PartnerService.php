<?php

namespace App\Services\Cms\Partner;

use App\Http\Resources\Cms\Partner\PartnerResource;
use App\Models\Cms\Partner\Partner;
use App\Services\Service;

class PartnerService extends Service
{
    protected $uploadPath = 'our-partners';

    public function __construct(protected Partner $partner) {}

    public function paginate($limit, $request)
    {
        $partners = $this->partner->where(function ($query) use ($request) {
            if ($request->filled('title')) {
                $query->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $query->whereIsActive($request->is_active);
            }
        })->paginate($limit);

        return PartnerResource::collection($partners);
    }

    public function getBySlug($slug)
    {
        return $this->partner->whereSlug($slug)->whereIsActive(1)->first();
    }

    public function frontPaginate($request)
    {
        $partners = $this->partner->orderBy('id', 'DESC')->whereIsActive(1)->paginate($request->per_pages ?? 20);

        return PartnerResource::collection($partners);
    }

    public function getByType($type, $limit)
    {
        $news = $this->partner->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return PartnerResource::collection($news);
    }

    public function store($data)
    {

        if (! empty($data['featured_photo'])) {
            $data['featured_photo'] = $this->uploadFile($data['featured_photo'], $this->uploadPath);
        }

        return $this->partner->create($data);
    }

    public function find($ourProject_id)
    {
        $partner = $this->partner->find($ourProject_id);
        if (! empty($partner)) {
            return $partner;
        }

        return null;
    }

    public function update($id, $data)
    {
        // try {
        $partner = $this->find($id);
        if (! empty($data['featured_photo'])) {
            if (! empty($partner->featured_photo)) {
                $this->deleteFile($this->uploadPath, $partner->featured_photo);
            }
            $data['featured_photo'] = $this->uploadFile($data['featured_photo'], $this->uploadPath);
        }
        // $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == "1") ? 1 : 0;

        return $partner->update($data);
        // } catch (\Exception $ex) {
        //     return false;
        // }
    }

    public function delete($id)
    {
        try {
            $partner = $this->find($id);
            if (! empty($partner->featured_photo)) {
                $this->deleteFile($this->uploadPath, $partner->featured_photo);
            }

            return $partner->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->partner->where($column, $value)->first();
    }

    public function findByColumns($data = null, $limit = 0)
    {
        $result = $this->partner->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit);

            return PartnerResource::collection($result);
        } else {
            return new PartnerResource($result);
        }
    }

    public function getAllActive()
    {
        $project = $this->partner->whereIsActive(1)->get();

        return PartnerResource::collection($project);
    }
}
