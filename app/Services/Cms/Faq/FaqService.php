<?php

namespace App\Services\Cms\Faq;

use App\Http\Resources\Cms\Faq\FaqResource;
use App\Models\Cms\Faq\Faq;
use App\Services\Service;
use Illuminate\Support\Facades\Hash;

class FaqService extends Service
{
    protected $faq;

    protected $uploadPath = 'faq';

    public function __construct(Faq $faq)
    {
        $this->faq = $faq;
    }

    public function paginate($limit, $request)
    {
        $faqs = $this->faq->where(function ($query) use ($request) {
            if ($request->filled('title')) {
                $query->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $query->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return FaqResource::collection($faqs);
    }

    public function paginateFront($limit = 25)
    {
        $faq = $this->faq->orderBy('position', 'DESC')->whereIsActive(1)->paginate($limit);

        return FaqResource::collection($faq);
    }

    public function sort(array $data): bool
    {
        foreach ($data as $index => $id) {
            $this->faq
                ->where('id', $id)
                ->update(['position' => $index + 1]);
        }

        return true;
    }

    public function frontPaginate($limit = 25)
    {
        $faq = $this->faq->orderBy('id', 'DESC')->whereIsActive(1)
            ->paginate($limit);

        return FaqResource::collection($faq);
    }

    public function getByType($type, $limit)
    {
        $news = $this->faq->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return FaqResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->faq->whereSlug($slug)->first();
    }

    public function store($data)
    {
        try {
            return $this->faq->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $faq = $this->faq->whereId($id)->first();

        return new FaqResource($faq);
    }

    public function update($id, $data)
    {
        try {
            $faq = $this->getById($id);

            return $faq->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $faq = $this->getById($id);
            if (! empty($faq->cover_image)) {
                $this->deleteFile($this->uploadPath, $faq->cover_image);
            }

            return $faq->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->faq->where($column, $value)->first();
    }

    public function getUserForLogin($email, $password)
    {
        $faq = $this->faq->whereEmail($email)->first();
        if (empty($faq)) {
            return false;
        }

        if (Hash::check($password, $faq->password)) {
            return $faq;
        }

        return false;
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->faq->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return FaqResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new FaqResource($response);
        }
    }

    public function searchByKey($key, $limit)
    {
        $results = $this->faq
            ->where('title', 'like', '%'.$key.'%')
            ->orWhereRaw('FIND_IN_SET(?,tags)', [$key])
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $faqs = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'img' => $p->cover_image_path['thumb'],
                    'route' => route('gallery-detail', $p->slug),
                ];
                array_push($faqs, $temp);
            }
        }

        return $faqs;
    }
}
