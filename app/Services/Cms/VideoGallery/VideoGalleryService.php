<?php

namespace App\Services\Cms\VideoGallery;

use App\Http\Resources\Cms\VideoGallery\VideoGalleryResource;
use App\Models\Cms\VideoGallery\VideoGallery;
use App\Services\Service;
use Illuminate\Support\Facades\Hash;

class VideoGalleryService extends Service
{
    protected $videoGallery;

    protected $uploadPath = 'videoGallery';

    public function __construct(VideoGallery $videoGallery)
    {
        $this->videoGallery = $videoGallery;
    }

    public function paginate($data, $limit = 25)
    {
        $videoGallery = $this->videoGallery->orderBy('position', 'ASC')->where(function ($qry) use ($data) {
            if (isset($data['title']) && ! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['active']) && ! empty($data['active'])) {
                $flag = $data['active'] == 'active' ? 1 : 0;
                $qry->whereIsActive($flag);
            }
        })
            ->paginate($limit);

        return VideoGalleryResource::collection($videoGallery);
    }

    public function paginateFront($limit = 25)
    {
        $videoGallery = $this->videoGallery->orderBy('position', 'ASC')->whereIsActive(1)->paginate($limit);

        return VideoGalleryResource::collection($videoGallery);
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $videoGalleryb = $this->videoGallery->whereId($id)->first();
                    if (! empty($videoGalleryb)) {
                        $v['position'] = ($i + 1);
                        $videoGalleryb->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function frontPaginate($limit = 25)
    {
        $videoGallery = $this->videoGallery->orderBy('position', 'ASC')->whereIsActive(1)
            ->paginate($limit);

        return VideoGalleryResource::collection($videoGallery);
    }

    public function getByType($type, $limit)
    {
        $news = $this->videoGallery->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return VideoGalleryResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->videoGallery->whereSlug($slug)->first();
    }

    public function getFeaturedVideos($limit = 2)
    {
        return $this->videoGallery->whereIsFeatured(1)->orderBy('position', 'ASC')->whereIsActive(1)->limit($limit)->get();
    }

    public function store($data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->videoGallery->create($data);
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getById($id)
    {
        $videoGallery = $this->videoGallery->whereId($id)->first();

        return new VideoGalleryResource($videoGallery);
    }

    public function update($id, $data)
    {
        try {
            $videoGallery = $this->getById($id);
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == '1') ? 1 : 0;

            return $videoGallery->update($data);
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function delete($id)
    {
        try {
            $videoGallery = $this->getById($id);
            if (! empty($videoGallery->cover_image)) {
                $this->deleteFile($this->uploadPath, $videoGallery->cover_image);
            }

            return $videoGallery->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->videoGallery->where($column, $value)->first();
    }

    public function getUserForLogin($email, $password)
    {
        $videoGallery = $this->videoGallery->whereEmail($email)->first();
        if (empty($videoGallery)) {
            return false;
        }

        if (Hash::check($password, $videoGallery->password)) {
            return $videoGallery;
        }

        return false;
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->videoGallery->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return VideoGalleryResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new VideoGalleryResource($response);
        }
    }

    public function searchByKey($key, $limit)
    {
        $results = $this->videoGallery
            ->where('title', 'like', '%'.$key.'%')
            ->orWhereRaw('FIND_IN_SET(?,tags)', [$key])
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $videoGallerys = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'img' => $p->cover_image_path['thumb'],
                    'route' => route('gallery-detail', $p->slug),
                ];
                array_push($videoGallerys, $temp);
            }
        }

        return $videoGallerys;
    }
}
