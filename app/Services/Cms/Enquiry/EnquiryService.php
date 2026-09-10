<?php

namespace App\Services\Cms\Enquiry;

use App\Http\Resources\Cms\Enquiry\EnquiryResource;
use App\Models\Cms\Enquiry\Enquiry;

class EnquiryService
{
    private $enquiry;

    public function __construct(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }

    public function paginate($perPage, $filters)
    {
        $query = $this->enquiry->newQuery()
            ->when($filters['name'] ?? false, fn ($q, $name) => $q->where('name', 'like', "%$name%"))
            ->when($filters['email'] ?? false, fn ($q, $email) => $q->where('email', 'like', "%$email%"))
            ->when($filters['message'] ?? false, fn ($q, $message) => $q->where('message', 'like', "%$message%"))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return EnquiryResource::collection($query);
    }

    public function show(string $id)
    {
        $enquiry = $this->enquiry->find($id);

        return EnquiryResource::collection($enquiry);

    }

    public function update(string $id, array $data)
    {
        $enquiry = $this->enquiry->find($id);
        if ($enquiry) {
            $enquiry->update($data);

            return $enquiry;
        }

        return null;
    }

    public function delete(string $id)
    {
        $enquiry = $this->enquiry->find($id);
        if ($enquiry) {
            $enquiry->delete();

            return true;
        }

        return false;
    }

    public function create(array $data)
    {
        return $this->enquiry->create($data);
    }
}
