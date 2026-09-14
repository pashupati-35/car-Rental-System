<?php

namespace App\Services\Cms\Blog\Category;

use App\Http\Resources\Cms\Blog\Category\BlogCategoryResource;
use App\Models\Cms\Blog\Category\BlogCategory;
use App\Services\Service;
use Illuminate\Support\Facades\Hash;

class BlogCategoryService extends Service
{
    protected $category;

    protected $uploadPath = 'blog/category';

    public function __construct(BlogCategory $category)
    {
        $this->category = $category;
    }

    public function paginate($limit, $request)
    {
        $caterogies = $this->category->where(function ($qry) use ($request) {

            if ($request->filled('title')) {
                $qry->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $qry->whereIsActive($request->is_active);
            }
        })->orderBy('id', 'DESC')->paginate($limit);

        return BlogCategoryResource::collection($caterogies);
    }

    public function store($data)
    {
        try {
            if (isset($data['featured_image'])) {
                $data['featured_image'] = $this->uploadFile($data['featured_image'], $this->uploadPath);
            }
            $this->category->create($data);

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $category = $this->category->find($id);
        if (! empty($category)) {
            return $category;
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $category = $this->find($id);
            if (! empty($data['featured_image'])) {
                if (! empty($category->featured_image)) {
                    $this->deleteFile($this->uploadPath, $category->featured_image);
                }
                $data['featured_image'] = $this->uploadFile($data['featured_image'], $this->uploadPath);
            }
            $category->update($data);

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->find($id);

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

    public function getCategories()
    {
        $category = $this->category->whereIsActive(1)->orderBy('id', 'DESC')->take(10)->get();

        return BlogCategoryResource::collection($category);
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
            return BlogCategoryResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new BlogCategoryResource($response);
        }
    }
}
