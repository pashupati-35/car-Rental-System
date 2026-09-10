<?php

namespace App\Services\Cms\Album;

use App\Http\Resources\Cms\Album\AlbumResource;
use App\Models\Cms\Album\Album;
use App\Services\Service;
use Illuminate\Support\Facades\Hash;

class AlbumService extends Service
{
    protected $uploadPath = 'album';

    public function __construct(protected Album $album) {}

    public function paginate($limit, $request)
    {
        $album = $this->album->where(function ($qry) use ($request) {
            if ($request->filled('title')) {
                $qry->where('title', 'like', '%'.$request->title.'%');
            }
            if ($request->filled('is_active')) {
                $qry->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return AlbumResource::collection($album);
    }

    public function paginateFront($limit = 25)
    {
        $album = $this->album->orderBy('position', 'DESC')->whereIsActive(1)->paginate($limit);

        return AlbumResource::collection($album);
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $album = $this->album->where('id', $id)->first();
                    if (! is_null($album)) {
                        $album->update(['position' => ($i + 1)]);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getByType($type, $limit)
    {
        $news = $this->album->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return AlbumResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->album->whereSlug($slug)->first();
    }

    public function store($data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            if (isset($data['cover_image']) && ! empty($data['cover_image'])) {
                $data['cover_image'] = $this->uploadFile($data['cover_image'], $this->uploadPath);
            }

            return $this->album->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function bulkStore($data, $files = null)
    {
        try {
            $insertData = [];
            if ($files && count($files) > 0) {
                foreach ($files as $file) {
                    $entry = $data;
                    $entry['is_active'] = (isset($entry['is_active']) && $entry['is_active'] == true) ? 1 : 0;
                    $entry['cover_image'] = $this->uploadFile($file, $this->uploadPath);
                    $position = $this->album->max('position') ?? 0;
                    $entry['position'] = $position + 1;
                    $entry['slug'] = $entry['title'];
                    $insertData[] = $entry;
                }
            } else {
                $data['slug'] = $data['title'];
                $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? 1 : 0;
                $position = $this->album->max('position') ?? 0;
                $data['position'] = $position + 1;
                $insertData[] = $data;
            }
            if (! empty($insertData)) {
                return $this->album->insert($insertData);
            }

            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $album = $this->album->find($id);

        return new AlbumResource($album);
    }

    public function update($id, $data)
    {
        try {
            $album = $this->getById($id);
            if (! empty($data['cover_image'])) {
                if (! empty($album->cover_image)) {
                    $this->deleteFile($this->uploadPath, $album->cover_image);
                }
                $data['cover_image'] = $this->uploadFile($data['cover_image'], $this->uploadPath);
            }
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == '1') ? 1 : 0;

            return $album->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $album = $this->getById($id);
            if (! empty($album->cover_image)) {
                $this->deleteFile($this->uploadPath, $album->cover_image);
            }

            return $album->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->album->where($column, $value)->first();
    }

    public function getUserForLogin($email, $password)
    {
        $album = $this->album->whereEmail($email)->first();
        if (empty($album)) {
            return false;
        }

        if (Hash::check($password, $album->password)) {
            return $album;
        }

        return false;
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->album->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return AlbumResource::collection($response->orderBy('id', 'DESC')->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new AlbumResource($response);
        }
    }

    public function searchByKey($key, $limit)
    {
        $results = $this->album
            ->where('title', 'like', '%'.$key.'%')
            ->orWhereRaw('FIND_IN_SET(?,tags)', [$key])
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $albums = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'img' => $p->cover_image_path['thumb'],
                    'route' => route('gallery-detail', $p->slug),
                ];
                array_push($albums, $temp);
            }
        }

        return $albums;
    }

    public function getAllActive()
    {
        $albumGallery = $this->album->whereIsActive(1)->get();

        return AlbumResource::collection($albumGallery);
    }
}
