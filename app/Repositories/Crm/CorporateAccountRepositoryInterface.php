<?php

namespace App\Repositories\Crm;

use App\Models\Crm\CorporateAccount;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CorporateAccountRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredAccounts(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getActiveAccountsForSelect(): Collection;

    public function getAccountWithDetails(int $id): CorporateAccount;

    public function createAccount(array $data): CorporateAccount;

    public function updateAccount(CorporateAccount|int $account, array $data): CorporateAccount;

    public function deleteAccount(CorporateAccount|int $account): bool;
}
