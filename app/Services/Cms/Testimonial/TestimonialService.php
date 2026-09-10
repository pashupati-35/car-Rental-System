<?php

namespace App\Services\Cms\Testimonial;

use App\Http\Resources\Cms\Testimonial\TestimonialResource;
use App\Models\Cms\Testimonial\Testimonial;
use App\Services\Service;

class TestimonialService extends Service
{
    protected $testimonial;

    protected $uploadPath = 'testimonial';

    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    public function paginate($limit, $request)
    {
        $testimonial = $this->testimonial->orderBy('position', 'ASC')->where(function ($qry) use ($request) {
            if ($request->filled('title')) {
                $qry->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $qry->whereIsActive($request->is_active);
            }
        })->paginate($limit);

        return TestimonialResource::collection($testimonial);
    }

    public function getFront($limit = 3)
    {
        $testimonial = $this->testimonial->orderBy('position', 'ASC')->whereIsActive(1)->paginate($limit);

        return TestimonialResource::collection($testimonial);
    }

    public function test()
    {
        $testimonial = $this->testimonial->whereIsActive(1)->orderBy('position', 'ASC')
            ->get();

        return TestimonialResource::collection($testimonial);
    }

    public function featuredImage($limit = 25)
    {
        $testimonial = $this->testimonial->whereIsFeatured(1)->orderBy('id', 'DESC')
            ->paginate($limit);

        return TestimonialResource::collection($testimonial);
    }

    public function getByType($type, $limit)
    {
        $news = $this->testimonial->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return TestimonialResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->testimonial->whereSlug($slug)->first();
    }

    public function store($data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->testimonial->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $testimonial = $this->testimonial->whereId($id)->first();
                    if (! empty($testimonial)) {
                        $v['position'] = ($i + 1);
                        $testimonial->update($v);
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
        $testimonial = $this->testimonial->find($id);

        return new TestimonialResource($testimonial);
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $testimonial = $this->getById($id);
            if (! empty($data['image'])) {
                if (! empty($testimonial->path)) {
                    $this->deleteFile($this->uploadPath, $testimonial->path);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $testimonial->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function makeNonFeatured($id)
    {
        $testimonials = $this->testimonial->where('id', '!=', $id)->get();
        if ($testimonials->count() > 0) {
            foreach ($testimonials as $v) {
                $v->is_featured = 0;
                $v->save();
            }
        }
    }

    public function delete($id)
    {
        //        try {
        $testimonial = $this->getById($id);
        if (! empty($testimonial->path)) {
            $this->deleteFile($this->uploadPath, $testimonial->path);
        }

        return $testimonial->delete();
        //        } catch (\Exception $ex) {
        //            return false;
        //        }
    }

    public function findByColumn($column, $testimonial)
    {
        return $this->testimonial->where($column, $testimonial)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->testimonial->where(function ($query) use ($data) {
            foreach ($data as $key => $testimonial) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return TestimonialResource::collection($result);
        } else {
            return new TestimonialResource($result);
        }
    }
}
