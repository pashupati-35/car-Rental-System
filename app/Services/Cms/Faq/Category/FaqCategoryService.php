<?php

namespace App\Services\Cms\Faq\Category;

use App\Http\Resources\Cms\Faq\Category\FaqCategoryResource;
use App\Models\Cms\Faq\Category\FaqCategory;

class FaqCategoryService
{
    public function __construct(protected FaqCategory $faqCategory) {}

    public function paginate($limit = 25, $request = null)
    {
        $faqCategories = $this->faqCategory->where(function ($query) use ($request) {
            if ($request->filled('name')) {
                $query->where('name', 'like', '%'.$request->name.'%');
            }

            if ($request->filled('is_active')) {
                $query->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC');

        $faqCategories = $faqCategories->paginate($limit);

        return FaqCategoryResource::collection($faqCategories);
    }

    public function all()
    {
        return $this->faqCategory->all();
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $faqCategory = $this->faqCategory->whereId($id)->first();
                    if (! empty($faqCategory)) {
                        $v['position'] = ($i + 1);
                        $faqCategory->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getParent()
    {
        try {
            $faqCategory = $this->faqCategory->whereIsParent(1)->orderBy('position', 'ASC')->get();

            return FaqCategoryResource::collection($faqCategory);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getAllFaqCategories()
    {
        try {
            $faqCategories = $this->faqCategory->whereIsParent(1);

            $faqCategories = $faqCategories->orderBy('position', 'ASC')->get();

            return FaqCategoryResource::collection($faqCategories);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findFirstByColumn($data)
    {
        try {
            $result = $this->faqCategory->where(function ($query) use ($data) {
                foreach ($data as $key => $item) {
                    $query->where($key, $data[$key]);
                }
            });
            $result = $result->first();

            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function store($data)
    {

        try {
            $data['position'] = $this->faqCategory->orderBy('position', 'DESC')->first();
            $data['position'] = $data['position'] && $data['position']->position ? $data['position']->position + 1 : 1;
            $faqCategory = $this->faqCategory->create($data);

            return new FaqCategoryResource($faqCategory);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $faqCategory = $this->faqCategory->find($id);
        if (! empty($faqCategory)) {
            return $faqCategory;
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $faqCategory = $this->find($id);
            $faqCategory->update($data);

            return $faqCategory;
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function delete($id)
    {
        try {
            $faqCategory = $this->find($id);

            return $faqCategory->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->faqCategory->where($column, $value)->first();
    }

    public function findByColumns($data = null, $limit = 0)
    {
        $result = $this->faqCategory->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit);

            return FaqCategoryResource::collection($result);
        } else {
            return new FaqCategoryResource($result);
        }
    }
}
