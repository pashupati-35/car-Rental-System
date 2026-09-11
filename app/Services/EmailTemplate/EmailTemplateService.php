<?php

namespace App\Services\EmailTemplate;

use App\Http\Resources\EmailTemplate\EmailTemplateResource;
use App\Models\EmailTemplate\EmailTemplate;

class EmailTemplateService
{
    protected $emailTemplate;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function paginate($limit, $request)
    {
        $emailTemplate = $this->emailTemplate->where(function ($qry) use ($request) {
            if ($request->filled('title')) {
                $qry->where('title', 'like', '%'.$request->title.'%');
            }
            if ($request->filled('role')) {
                $qry->whereRole($request->role);
            }
            if ($request->filled('identifier')) {
                $qry->whereIdentifier($request->identifier);
            }
            if ($request->filled('is_active')) {
                $qry->whereIsActive($request->is_active);
            }
        })->orderBy('id', 'DESC')->paginate($limit);

        return EmailTemplateResource::collection($emailTemplate);
    }

    public function emailTemplateRoles()
    {
        return $this->emailTemplate
            ->distinct()
            ->pluck('role');
    }

    public function all()
    {
        $emailTemplate = $this->emailTemplate->whereIsActive(1)->orderBy('position', 'ASC')->get();

        return EmailTemplateResource::collection($emailTemplate);
    }

    public function store($data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;

            return $this->emailTemplate->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $emailTemplate = $this->emailTemplate->find($id);

        return new EmailTemplateResource($emailTemplate);
    }

    public function update($id, $data)
    {
        try {
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == true) ? true : 0;
            $emailTemplate = $this->getById($id);

            return $emailTemplate->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $emailTemplate = $this->getById($id);

            return $emailTemplate->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->emailTemplate->where($column, $value)->first();
    }

    public function findByColumns($data, $limit = 0)
    {
        $result = $this->emailTemplate->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if (! empty($limit) || $limit != 0) {
            $result = $result->take($limit)->get();

            return EmailTemplateResource::collection($result);
        } else {
            return new EmailTemplateResource($result);
        }
    }

    public function findByWhereIn($column, $value, $data = [], $all = false, $limit = null)
    {
        $result = $this->emailTemplate->whereIn($column, $value)->where(function ($query) use ($data) {
            foreach ($data as $key => $value) {
                $query->where($key, $data[$key]);
            }
        });
        if ($all) {
            if (! empty($limit)) {
                $result = $result->take($limit);
            }

            return EmailTemplateResource::collection($result->get());
        } else {
            return new EmailTemplateResource($result);
        }
    }

    public function cloneEmailTemplate($request)
    {
        $emailTemplate = $this->emailTemplate->whereType($request['type'])->first();

        if ($emailTemplate) {
            $emailTemplate = $this->update($emailTemplate->id, $request);
        } else {
            $emailTemplate = $this->store($request);
        }
        if ($emailTemplate) {
            return true;
        }

        return false;
    }

    public function getRoleCounts(): array
    {
        return [
            'all' => $this->emailTemplate->count(),
            'owner' => $this->emailTemplate->where('role', 'owner')->count(),
            'customer' => $this->emailTemplate->where('role', 'customer')->count(),
            'admin' => $this->emailTemplate->where('role', 'admin')->count(),
        ];
    }

    public function findRaw($id): EmailTemplate
    {
        return $this->emailTemplate->findOrFail($id);
    }
}
