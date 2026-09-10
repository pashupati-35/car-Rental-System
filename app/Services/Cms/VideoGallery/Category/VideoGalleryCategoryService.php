<?php

namespace App\Services\Cms\VideoGallery\Category;

use App\Http\Resources\Cms\VideoGallery\Category\VideoGalleryCategoryResource;
use App\Models\Cms\VideoGallery\VideoGalleryCategory;
use Illuminate\Support\Facades\Hash;

class VideoGalleryCategoryService
{
    protected $category;

    public function __construct(VideoGalleryCategory $category)
    {
        $this->category = $category;
    }

    public function paginate($limit = null, $type = null)
    {
        $caterogies = $this->category->where(function ($qry) {})->orderBy('id', 'DESC')->paginate($limit);

        return VideoGalleryCategoryResource::collection($caterogies);
    }

    public function store($data)
    {
        //        try {
        return $this->category->create($data);
        //        } catch
        //        (\Exception $ex) {
        //            return false;
        //        }
    }

    public function getById($id)
    {
        $category = $this->category->find($id);

        return new VideoGalleryCategoryResource($category);
    }

    public function update($id, $data)
    {
        try {
            $category = $this->getById($id);

            return $category->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->getById($id);

            return $category->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->category->where($column, $value)->first();
    }

    public function getUserForLogin($email, $password)
    {
        $category = $this->category->whereEmail($email)->first();
        if (empty($category)) {
            return false;
        }

        if (Hash::check($password, $category->password)) {
            return $category;
        }

        return false;
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->category->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return VideoGalleryCategoryResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new VideoGalleryCategoryResource($response);
        }
    }
}
