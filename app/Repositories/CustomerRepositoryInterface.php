<?php

namespace App\Repositories;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);

    public function findByPhone(string $phone);

    public function getByOwner($ownerId);

    public function getByAdmin($adminId);
}
