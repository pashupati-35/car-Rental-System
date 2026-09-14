<?php

namespace App\Services\Cms\Album\Value;

use App\Http\Resources\Cms\Album\Value\AlbumValueResource;
use App\Models\Cms\Album\Value\AlbumValue;
use App\Services\Service;

class AlbumValueService extends Service
{
    protected $uploadPath = 'album/value';

    public function __construct(protected AlbumValue $value) {}

    public function paginate($albumId, $limit)
    {
        $value = $this->value->whereAlbumId($albumId)->orderBy('position', 'ASC')
            ->paginate($limit);

        return AlbumValueResource::collection($value);
    }

    public function featuredImage($limit = 25)
    {
        $value = $this->value->whereIsFeatured(1)->orderBy('id', 'DESC')
            ->paginate($limit);

        return AlbumValueResource::collection($value);
    }

    public function getByType($albumId, $type, $limit)
    {
        $news = $this->value->whereAlbumId($albumId)->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return AlbumValueResource::collection($news);
    }

    public function getBySlug($albumId, $slug)
    {
        return $this->value->whereAlbumId($albumId)->whereSlug($slug)->first();
    }

    public function getByAlbumId($albumId)
    {
        return $this->value->whereAlbumId($albumId)->get();
    }

    public function store($albumId, $data)
    {
        try {
            $data['album_id'] = $albumId;
            foreach ($data['path'] as $i => $file) {
                $data['path'] = $this->uploadFile($file, $this->uploadPath);
                $this->value->create($data);
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function sort($albumId, $data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $albumbValue = $this->value->whereAlbumId($albumId)->whereId($id)->first();
                    if (! empty($albumbValue)) {
                        $v['position'] = ($i + 1);
                        $albumbValue->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getById($albumId, $id)
    {
        $value = $this->value->whereAlbumId($albumId)->find($id);

        return new AlbumValueResource($value);
    }

    public function update($albumId, $id, $data)
    {
        try {
            $value = $this->getById($albumId, $id);

            if (! empty($data['path'])) {
                if (! empty($value->path)) {
                    $this->deleteFile($this->uploadPath, $value->path);
                }

                $data['path'] = $this->uploadFile(
                    $data['path'][0],
                    $this->uploadPath
                );
            }

            if ($data['is_featured']) {
                $this->makeNonFeatured($albumId, $id);
            }

            return $value->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function makeNonFeatured($albumId, $id)
    {
        $values = $this->value->whereAlbumId($albumId)->where('id', '!=', $id)->get();
        if ($values->count() > 0) {
            foreach ($values as $v) {
                $v->is_featured = 0;
                $v->save();
            }
        }
    }

    public function delete($albumId, $id)
    {
        try {
            $value = $this->getById($albumId, $id);
            if (! empty($value->path)) {
                $this->deleteFile($this->uploadPath, $value->path);
            }

            return $value->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($albumId, $column, $value)
    {
        return $this->value->whereAlbumId($albumId)->where($column, $value)->first();
    }

    public function findByColumns($albumId, $data, $limit = 0)
    {
        $result = $this->value->whereAlbumId($albumId)->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return AlbumValueResource::collection($result);
        } else {
            return new AlbumValueResource($result);
        }
    }

    public function getAllPicture($id)
    {
        $albumPicture = $this->value->where('album_id', $id)->get();

        return $albumPicture;
    }
}
