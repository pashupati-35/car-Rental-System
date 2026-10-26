<?php

namespace App\Services\Crm;

use App\Models\Crm\CorporateAccount;
use App\Repositories\Crm\CorporateAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CorporateAccountService
{
    public function __construct(
        protected CorporateAccountRepositoryInterface $corporateAccountRepository
    ) {}

    public function getAccounts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->corporateAccountRepository->getFilteredAccounts($filters, $perPage);
    }

    public function getActiveAccountsForSelect(): Collection
    {
        return $this->corporateAccountRepository->getActiveAccountsForSelect();
    }

    public function getAccount(int $id): CorporateAccount
    {
        return $this->corporateAccountRepository->getAccountWithDetails($id);
    }

    public function createAccount(array $data): CorporateAccount
    {
        $data['assigned_admin_id'] = auth('admin')->id();

        return $this->corporateAccountRepository->createAccount($data);
    }

    public function updateAccount(int $id, array $data): CorporateAccount
    {
        return $this->corporateAccountRepository->updateAccount($id, $data);
    }

    public function deleteAccount(int $id): bool
    {
        return $this->corporateAccountRepository->deleteAccount($id);
    }
}
