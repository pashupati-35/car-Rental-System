<?php

namespace App\Services\Cms\Service;

use App\Http\Resources\Cms\Service\ServiceResource;
use App\Models\Cms\Service\Services;
use App\Services\Service;

class ServiceService extends Service
{
    protected $service;

    protected $uploadPath = 'services';

    public function __construct(Services $service)
    {
        $this->service = $service;
    }

    public function paginate($request, $limit)
    {
        $service = $this->service
            ->when($request->filled('type'), function ($qry) use ($request) {
                $qry->where('type', $request->type);
            })
            ->when($request->filled('title'), function ($qry) use ($request) {
                $qry->where('title', 'like', '%'.$request->title.'%');
            })
            ->when($request->filled('price'), function ($qry) use ($request) {
                $qry->where('price', 'like', '%'.$request->price.'%');
            })
            ->when($request->filled('is_active'), function ($qry) use ($request) {
                $qry->where('is_active', $request->is_active);
            })
            ->when($request->filled('search'), function ($qry) use ($request) {
                $search = $request->search;
                $qry->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->orderBy('position', 'ASC')
            ->paginate($request->filled('limit') ? $request->limit : $limit);

        return ServiceResource::collection($service);
    }

    public function store($data)
    {
        try {
            $maxPosition = (int) $this->service->max('position');
            $data['position'] = $maxPosition + 1;
            if (isset($data['image']) && ! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            if (isset($data['social_share_image']) && ! empty($data['social_share_image'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            return $this->service->create($data);
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function find($id)
    {
        $service = $this->service
            ->find($id);

        if (! $service) {
            return null;
        }

        return new ServiceResource($service);
    }

    public function sort($data)
    {
        try {
            if (count($data)) {
                foreach ($data as $i => $id) {
                    $service = $this->service->whereId($id)->first();
                    if ($service) {
                        $service->update([
                            'position' => $i + 1,

                        ]);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $service = $this->service->find($id);
            if (! $service) {
                return false;
            }
            if (! empty($data['image'])) {
                if (! empty($service->image)) {
                    $this->deleteFile($this->uploadPath, $service->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            if (! empty($data['social_share_image'])) {
                if (! empty($service->social_share_image)) {
                    $this->deleteFile($this->uploadPath, $service->social_share_image);
                }
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            return $service->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $service = $this->service->find($id);
            if (! $service) {
                return false;
            }

            return $service->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getAllActive()
    {
        $service = $this->service->where('is_active', 1)->get();

        return ServiceResource::collection($service);
    }
}
