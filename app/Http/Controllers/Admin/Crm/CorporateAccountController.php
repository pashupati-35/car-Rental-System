<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CorporateAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CorporateAccountController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $query = CorporateAccount::with('assignedAdmin')->latest('id');

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

        $accounts = $query->paginate($perPage)->withQueryString();

        return Inertia::render('admin/crm/CorporateAccounts', [
            'accounts' => $accounts,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'business_reg_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'contract_discount_percent' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'required|string|max:50',
            'status' => 'required|string|in:active,pending,suspended',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_admin_id'] = auth('admin')->id();

        CorporateAccount::create($validated);

        return redirect()->back()->with('success', 'Corporate account registered successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $account = CorporateAccount::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'business_reg_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'contract_discount_percent' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'required|string|max:50',
            'status' => 'required|string|in:active,pending,suspended',
            'notes' => 'nullable|string',
        ]);

        $account->update($validated);

        return redirect()->back()->with('success', 'Corporate account updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $account = CorporateAccount::findOrFail($id);
        $account->delete();

        return redirect()->back()->with('success', 'Corporate account removed successfully.');
    }
}
