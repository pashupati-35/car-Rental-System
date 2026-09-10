<?php

namespace App\Services\Cms\Popup;

use App\Http\Resources\Cms\Popup\PopupResource;
use App\Models\Cms\Popup\Popup;
use App\Services\Service;

class PopupService extends Service
{
    protected $uploadPath = 'popup';

    public function __construct(protected Popup $popup) {}

    public function paginate($limit, $request)
    {
        $popups = $this->popup->where(function ($query) use ($request) {

            if ($request->filled('title')) {
                $query->where('title', 'like', '%'.$request->title.'%');
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return PopupResource::collection($popups);
    }

    public function frontPopups()
    {
        $popups = $this->popup->whereIsActive(1)->orderBy('id', 'DESC')->get();

        return PopupResource::collection($popups);
    }

    public function getByType($type, $limit)
    {
        $news = $this->popup->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return PopupResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->popup->whereSlug($slug)->first();
    }

    public function store($data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->popup->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $popup = $this->popup->find($id);

        return new PopupResource($popup);
    }

    public function update($id, $data)
    {
        try {
            $popup = $this->getById($id);
            if (! empty($data['image'])) {
                if (! empty($popup->path)) {
                    $this->deleteFile($this->uploadPath, $popup->path);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            //            if ($data["is_featured"]) {
            //                $this->makeNonFeatured($id);
            //            }
            return $popup->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function makeNonFeatured($id)
    {
        $popups = $this->popup->where('id', '!=', $id)->get();
        if ($popups->count() > 0) {
            foreach ($popups as $v) {
                $v->is_featured = 0;
                $v->save();
            }
        }
    }

    public function delete($id)
    {
        //        try {
        $popup = $this->getById($id);
        if (! empty($popup->path)) {
            $this->deleteFile($this->uploadPath, $popup->path);
        }

        return $popup->delete();
        //        } catch (\Exception $ex) {
        //            return false;
        //        }
    }

    public function findByColumn($column, $popup)
    {
        return $this->popup->where($column, $popup)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->popup->where(function ($query) use ($data) {
            foreach ($data as $key => $popup) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return PopupResource::collection($result);
        } else {
            return new PopupResource($result);
        }
    }

    public function sort($data)
    {
        if (count($data) > 0) {
            foreach ($data as $i => $datum) {
                $popup = $this->findByColumn('id', $datum['id']);
                if (! empty($popup)) {
                    $popup->position = ($i + 1);
                    $popup->save();
                }
            }
        }

        return true;
    }
}
