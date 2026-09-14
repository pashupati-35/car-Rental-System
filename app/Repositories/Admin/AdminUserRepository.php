<?php

namespace App\Repositories\Admin;

use App\Models\Admin;
use App\Repositories\BaseRepository;

class AdminUserRepository extends BaseRepository implements AdminUserRepositoryInterface
{
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function getByUserType(string $userType)
    {
        return $this->model->where('role', $userType)->get();
    }

    public function findByEmail(string $email): ?Admin
    {
        return $this->model->where('email', $email)->first();
    }
}
