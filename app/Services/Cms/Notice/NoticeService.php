<?php

namespace App\Services\Cms\Notice;

use App\Http\Resources\Cms\Notice\NoticeResource;
use App\Models\Cms\Notice\Notice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class NoticeService
{
    public function __construct(protected Notice $notice) {}

    public function paginate($limit, $request)
    {
        $notices = $this->notice->where(function ($query) use ($request) {
            if (! empty($request) && $request->filled('name')) {
                $query->where('name', 'like', '%'.$request->name.'%');
            }

            if (! empty($request) && $request->filled('user_type')) {
                $request->user_type = implode(',', $request->user_type);
                $query->where('user_type', 'like', '%'.$request->user_type.'%');
            }

            if (! empty($request) && $request->filled('visible_from_date')) {
                $query->whereVisibleFromDate($request->visible_from_date);
            }

            if (! empty($request) && $request->filled('is_active')) {
                $query->whereIsActive($request->is_active);
            }
        })->orderBy('position', 'ASC')->paginate($limit);

        return NoticeResource::collection($notices);
    }

    public function all()
    {
        return $this->notice->where('user_type', 'like', '%homepage%')->get();
    }

    public function recentBlog()
    {
        return $this->notice->where('user_type', 'like', '%homepage%')->limit(3)->get();
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $notice = $this->notice->whereId($id)->first();
                    if (! empty($notice)) {
                        $v['position'] = ($i + 1);
                        $notice->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function store($data)
    {
        try {
            $data['position'] = $this->notice->orderBy('position', 'DESC')->first();
            $data['position'] = $data['position'] && $data['position']->position ? $data['position']->position + 1 : 1;
            $data['user_type'] = isset($data['user_type']) ? implode(',', $data['user_type']) : null;
            $data['visible_from_date'] = isset($data['visible_from_date']) && $data['visible_from_date'] != null ? $data['visible_from_date'] : Carbon::now()->toDateTimeString();
            $notice = $this->notice->create($data);

            return new NoticeResource($notice);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($noticeId)
    {
        $notice = $this->notice->find($noticeId);
        if (! empty($notice)) {
            return $notice;
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $notice = $this->find($id);
            $data['user_type'] = isset($data['user_type']) ? implode(',', $data['user_type']) : null;
            $notice->update($data);

            return $notice;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $notice = $this->find($id);

            return $notice->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->notice->where($column, $value)->first();
    }

    public function getBySlug($slug)
    {
        return $this->notice->whereSlug($slug)->whereIsActive(1)->first();
    }

    public function findByColumns($data, $all = false, $resource = true)
    {
        $result = $this->notice->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            $result = $result->get();

            return $resource ? NoticeResource::collection($result) : $result;
        } else {
            $result = $result->first();
            if (empty($result)) {
                return null;
            }

            return $resource ? new NoticeResource($result) : $result;
        }
    }

    public function getByUserType($limit, $type)
    {
        $todayDate = Date::now()->toDateString();
        if ($limit) {
            $notices = $this->notice->whereIsActive(1)->whereNull('visible_from_date')->orWhereDate('visible_from_date', '<=', $todayDate)->whereRaw('FIND_IN_SET(?,user_type)', [$type])->orderBy('id', 'DESC')->paginate($limit);
        } else {
            $notices = $this->notice->whereIsActive(1)->whereNull('visible_from_date')->orWhereDate('visible_from_date', '<=', $todayDate)->whereRaw('FIND_IN_SET(?,user_type)', [$type])->orderBy('id', 'DESC')->take(4)->get();
        }

        return NoticeResource::collection($notices);
    }
}
