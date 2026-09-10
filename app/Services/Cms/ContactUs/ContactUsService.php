<?php

namespace App\Services\Cms\ContactUs;

use App\Http\Resources\Cms\ContactUs\ContactUsResource;
use App\Models\Cms\ContactUs\ContactUs;
use Carbon\Carbon;

class ContactUsService
{
    protected $contact;

    public function __construct(ContactUs $contact)
    {
        $this->contact = $contact;
    }

    public function paginate($data, $limit)
    {
        $contact = $this->contact->where(function ($query) use ($data) {
            if (isset($data['title'])) {
                $query->where('first_name', 'like', '%'.$data['title'].'%')
                    ->orWhere('last_name', 'like', '%'.$data['title'].'%')
                    ->orWhere('email', 'like', '%'.$data['title'].'%')
                    ->orWhere('phone', 'like', '%'.$data['title'].'%')
                    ->orWhere('subject', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['last_name'])) {
                $query->where('last_name', 'like', '%'.$data['last_name'].'%');
            }
            if (isset($data['publish_date_from']) && ! empty($data['publish_date_from'])) {
                $query->whereDate('created_at', '>=', Carbon::parse($data['publish_date_from']));
            }
            if (isset($data['publish_date_to']) && ! empty($data['publish_date_to'])) {
                $query->whereDate('created_at', '<=', Carbon::parse($data['publish_date_to']));
            }
            if (isset($data['filter_by']) && ! empty($data['filter_by'])) {
                if ($data['filter_by'] == 'download') {
                    $query->where('type', 'download');
                } elseif ($data['filter_by'] == 'read') {
                    $query->where('is_read', true);
                } else {
                    $query->where('replied', true);
                }
            }
        })->orderBy('id', 'DESC')->paginate($limit);

        return ContactUsResource::collection($contact);
    }

    public function store($data)
    {
        try {
            return $this->contact->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $contact = $this->contact->find($id);
        if (! empty($contact)) {
            return new ContactUsResource($contact);
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $contact = $this->getById($id);

            return $contact->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $contact = $this->getById($id);

            return $contact->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->contact->where($column, $value)->first();
    }

    public function findByColumns($data = [], $all = false, $resource = true)
    {
        $result = $this->contact->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            $result = $result->get();

            return $resource ? ContactUsResource::collection($result) : $result;
        } else {
            $result = $result->first();
            if (empty($result)) {
                return null;
            }

            return $resource ? new ContactUsResource($result) : $result;
        }
    }
}
