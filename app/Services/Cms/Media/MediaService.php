<?php

namespace App\Services\Cms\Media;

use App\Http\Resources\Cms\Media\MediaResource;
use App\Models\Cms\Media\Media;
use App\Services\Service;

class MediaService extends Service
{
    protected $uploadPath = 'media';

    public function __construct(protected Media $media) {}

    public function paginate($data, $limit)
    {
        $media = $this->media->orderBy('id', 'DESC')->where(function ($qry) use ($data) {
            if (isset($data['title']) && ! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['type']) && $data['type'] != 'all') {
                $type = $this->getFileType($data['type']);
                $qry->where('type', $type);
            }
            if (isset($data['created_at'])) {
                $qry->where('created_at', '<=', $data['created_at']);
            }
            if (isset($data['active']) && ! empty($data['active'])) {
                $flag = $data['active'] == 'active' ? 1 : 0;
                $qry->whereIsActive($flag);
            }
        })->paginate($limit);

        return MediaResource::collection($media);
    }

    public function featuredImage($limit = 25)
    {
        $media = $this->media->whereIsFeatured(1)->orderBy('id', 'DESC')
            ->paginate($limit);

        return MediaResource::collection($media);
    }

    public function getByType($type, $limit)
    {
        $news = $this->media->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return MediaResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->media->whereSlug($slug)->first();
    }

    public function store($data, $authId)
    {
        try {

            $size = $data['size'] ?? [];
            $type = $data['type'] ?? [];
            $name = $data['title'] ?? [];

            foreach ($data['image'] as $i => $file) {

                $payload = [];

                $payload['image'] = $this->uploadFile(
                    $file,
                    $this->uploadPath,
                    'public'
                );

                $payload['size'] = isset($size[$i])
                    ? ($size[$i] / 100)
                    : null;

                $payload['title'] = $name[$i] ?? null;

                $payload['type'] = isset($type[$i])
                    ? $this->getFileType($type[$i])
                    : null;

                $payload['is_downloadable'] = $data['is_downloadable'] ?? 0;
                $payload['is_featured'] = $data['is_featured'] ?? 0;
                $payload['is_active'] = $data['is_active'] ?? 1;
                $payload['uploaded_by'] = $authId;

                $this->media->create($payload);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getFileType($type)
    {
        switch ($type) {
            case 'pdf':
                $fileType = 'pdf';
                break;
            case 'pptx':
                $fileType = 'ppt';
                break;
            case 'ppt':
                $fileType = 'ppt';
                break;
            case 'docx':
                $fileType = 'doc';
                break;
            case 'doc':
                $fileType = 'doc';
                break;
            case 'csv':
                $fileType = 'excel';
                break;
            case 'xlsx':
                $fileType = 'excel';
                break;
            case 'mp3':
                $fileType = 'audio';
                break;
            case 'mp4':
                $fileType = 'video';
                break;
            case 'png':
                $fileType = 'image';
                break;
            case 'jpg':
                $fileType = 'image';
                break;
            case 'jpeg':
                $fileType = 'image';
                break;
            case 'svg':
                $fileType = 'image';
                break;
            default:
                $fileType = 'other';
                break;
        }

        return $fileType;
    }

    public function getById($id)
    {
        $media = $this->media->find($id);

        return new MediaResource($media);
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $media = $this->getById($id);
            if (! empty($data['path'])) {
                if (! empty($media->path)) {
                    $this->deleteFile($this->uploadPath, $media->path);
                }
                $data['path'] = $this->uploadFile($data['path'], $this->uploadPath, 'public');
            }

            //            if ($data["is_featured"]) {
            //                $this->makeNonFeatured($id);
            //            }
            return $media->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function makeNonFeatured($id)
    {
        $medias = $this->media->where('id', '!=', $id)->get();
        if ($medias->count() > 0) {
            foreach ($medias as $v) {
                $v->is_featured = 0;
                $v->save();
            }
        }
    }

    public function delete($id)
    {
        //        try {
        $media = $this->getById($id);
        if (! empty($media->path)) {
            $this->deleteFile($this->uploadPath, $media->path);
        }

        return $media->delete();
        //        } catch (\Exception $ex) {
        //            return false;
        //        }
    }

    public function findByColumn($column, $media)
    {
        return $this->media->where($column, $media)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->media->where(function ($query) use ($data) {
            foreach ($data as $key => $media) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return MediaResource::collection($result);
        } else {
            return new MediaResource($result);
        }
    }

    public function getAllActive()
    {
        $media = $this->media->whereIsActive(1)->get();

        return MediaResource::collection($media);
    }
}
