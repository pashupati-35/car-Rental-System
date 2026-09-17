<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CorporateAccount\StoreCorporateAccountRequest;
use App\Http\Requests\Crm\CorporateAccount\UpdateCorporateAccountRequest;
use App\Services\Crm\CorporateAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CorporateAccountController extends Controller
{
    public function __construct(
        protected CorporateAccountService $corporateAccountService
    ) {}

    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $accounts = $this->corporateAccountService->getAccounts([
            'search' => $search,
            'status' => $status,
        ], $perPage);

        return Inertia::render('admin/crm/CorporateAccounts', [
            'accounts' => $accounts,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status,
            ],
        ]);
    }

    public function store(StoreCorporateAccountRequest $request): RedirectResponse
    {
        $this->corporateAccountService->createAccount($request->validated());

        return redirect()->back()->with('success', 'Corporate account registered successfully.');
    }

    public function update(UpdateCorporateAccountRequest $request, int $id): RedirectResponse
    {
        $this->corporateAccountService->updateAccount($id, $request->validated());

        return redirect()->back()->with('success', 'Corporate account updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->corporateAccountService->deleteAccount($id);

        return redirect()->back()->with('success', 'Corporate account removed successfully.');
    }
}
