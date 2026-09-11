<?php

namespace App\Services\Cms\Slider\Type;

use App\Http\Resources\Cms\Slider\Type\SliderTypeResource;
use App\Models\Cms\Slider\Type\SliderType;
use App\Services\Service;

class SliderTypeService extends Service
{
    protected $type;

    public function __construct(
        SliderType $type
    ) {
        $this->type = $type;
    }

    public function paginate($limit = 25)
    {
        $type = $this->type->orderBy('id', 'DESC')->paginate($limit);

        return SliderTypeResource::collection($type);
    }

    public function getActive()
    {
        $type = $this->type->whereIsActive(1)->orderBy('id', 'DESC')->get();

        return SliderTypeResource::collection($type);
    }

    public function store($data)
    {
        try {
            if (empty($data['is_active'])) {
                $data['is_active'] = true;
            }

            return $this->type->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $type = $this->type->find($id);
        if (! empty($type)) {
            return new SliderTypeResource($type);
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $type = $this->getById($id);

            return $type->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $type = $this->getById($id);
            if (! empty($type->path)) {
                $this->deleteUploaded($this->uploadPath, $type->path);
            }

            return $type->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->type->where($column, $value)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->type->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return SliderTypeResource::collection($result);
        } else {
            return new SliderTypeResource($result);
        }
    }
}
