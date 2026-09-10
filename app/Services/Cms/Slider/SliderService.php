<?php

namespace App\Services\Cms\Slider;

use App\Http\Resources\Cms\Slider\SliderResource;
use App\Models\Cms\Slider\Slider;
use App\Services\Service;

class SliderService extends Service
{
    protected $uploadPath = 'slider';

    public function __construct(protected Slider $slider) {}

    public function paginate($request, $limit = 25)
    {
        $slider = $this->slider->where(function ($qry) use ($request) {
            if ($request->filled('title')) {
                $qry->where('title', 'like', '%'.$request['title'].'%');
            }
            if ($request->filled('is_active')) {
                $qry->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return SliderResource::collection($slider);
    }

    public function getFrontSlider($typeId)
    {
        $slider = $this->slider->whereIsActive(1)->orderBy('position', 'ASC')->whereSliderTypeId($typeId)->get();

        return SliderResource::collection($slider);
    }

    public function featuredImage($limit = 25)
    {
        $slider = $this->slider->whereIsFeatured(1)->orderBy('id', 'DESC')
            ->paginate($limit);

        return SliderResource::collection($slider);
    }

    public function getByType($type, $limit)
    {
        $news = $this->slider->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return SliderResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->slider->whereSlug($slug)->first();
    }

    public function store($data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            $data['position'] = $this->slider->orderBy('position', 'DESC')->first();
            $data['position'] = $data['position'] && $data['position']->position ? $data['position']->position + 1 : 1;
            $data['show_button'] = (isset($data['show_button']) && $data['show_button'] == true) ? true : 0;
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->slider->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $slider = $this->slider->whereId($id)->first();
                    if (! empty($slider)) {
                        $v['position'] = ($i + 1);
                        $slider->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getById($id)
    {
        $slider = $this->slider->find($id);
        if (! empty($slider)) {
            return new SliderResource($slider);
        }

        return false;
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $data['show_button'] = (isset($data['show_button']) && $data['show_button'] == true) ? true : 0;
            $data['position'] = $this->slider->orderBy('position', 'DESC')->first();
            $data['position'] = $data['position'] && $data['position']->position ? $data['position']->position + 1 : 1;
            $slider = $this->getById($id);
            if (! empty($data['image'])) {
                if (! empty($slider->image)) {
                    $this->deleteFile($this->uploadPath, $slider->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $slider->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function makeNonFeatured($id)
    {
        $sliders = $this->slider->where('id', '!=', $id)->get();
        if ($sliders->count() > 0) {
            foreach ($sliders as $v) {
                $v->is_featured = 0;
                $v->save();
            }
        }
    }

    public function delete($id)
    {
        try {
            $slider = $this->slider->find($id);
            if (! empty($slider->image)) {
                $this->deleteFile($this->uploadPath, $slider->image);
            }

            return $slider->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $slider)
    {
        return $this->slider->where($column, $slider)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->slider->where(function ($query) use ($data) {
            foreach ($data as $key => $slider) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return SliderResource::collection($result);
        } else {
            return new SliderResource($result);
        }
    }
}
