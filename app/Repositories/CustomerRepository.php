<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone)
    {
        return $this->model->where('phone_number', $phone)->first();
    }

    public function getByOwner($ownerId)
    {
        return $this->model->where('owner_id', $ownerId)->get();
    }

    public function getByAdmin($adminId)
    {
        return $this->model->where('admin_id', $adminId)->get();
    }
}
