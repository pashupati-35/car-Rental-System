<?php

namespace App\Repositories\Crm;

use App\Models\Crm\CorporateAccount;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CorporateAccountRepository extends BaseRepository implements CorporateAccountRepositoryInterface
{
    public function __construct(CorporateAccount $model)
    {
        parent::__construct($model);
    }

    public function getFilteredAccounts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $status = $filters['status'] ?? 'all';

        $query = $this->model->with('assignedAdmin')->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('business_reg_number', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getActiveAccountsForSelect(): Collection
    {
        return $this->model->select('id', 'company_name', 'contact_person')
            ->where('status', 'active')
            ->get();
    }

    public function getAccountWithDetails(int $id): CorporateAccount
    {
        return $this->model->with('assignedAdmin')->findOrFail($id);
    }

    public function createAccount(array $data): CorporateAccount
    {
        return $this->model->create($data);
    }

    public function updateAccount(CorporateAccount|int $account, array $data): CorporateAccount
    {
        if (is_int($account)) {
            $account = $this->model->findOrFail($account);
        }

        $account->update($data);

        return $account;
    }

    public function deleteAccount(CorporateAccount|int $account): bool
    {
        if (is_int($account)) {
            $account = $this->model->findOrFail($account);
        }

        return (bool) $account->delete();
    }
}
