<?php

namespace App\Repositories\Admin;

use App\Models\Admin;
use App\Repositories\BaseRepositoryInterface;

interface AdminUserRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUserType(string $userType);

    public function findByEmail(string $email): ?Admin;
}
