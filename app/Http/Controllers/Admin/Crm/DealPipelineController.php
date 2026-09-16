<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Crm\CorporateAccount;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Models\Customer;
use App\Services\Crm\DealService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DealPipelineController extends Controller
{
    public function __construct(
        protected DealService $dealService
    ) {}

    public function index(Request $request): Response
    {
        $viewMode = $request->input('view', 'kanban'); // kanban or list
        $perPage = (int) $request->input('per_page', 15);

        $stages = DealService::STAGES;
        $groupedDeals = $this->dealService->getDealsByStage();
        $dealsList = $this->dealService->getDealsList($request->only(['search', 'stage']), $perPage);

        $cars = Car::select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day')->get();
        $customers = Customer::select('id', 'name', 'email', 'phone_number')->latest('id')->take(100)->get();
        $corporateAccounts = CorporateAccount::select('id', 'company_name', 'contact_person')->where('status', 'active')->get();
        $leads = Lead::select('id', 'first_name', 'last_name', 'company_name')->where('status', '!=', 'converted')->latest('id')->take(100)->get();

        return Inertia::render('admin/crm/DealsPipeline', [
            'view_mode' => $viewMode,
            'stages' => $stages,
            'grouped_deals' => $groupedDeals,
            'deals_list' => $dealsList,
            'cars' => $cars,
            'customers' => $customers,
            'corporate_accounts' => $corporateAccounts,
            'leads' => $leads,
            'filters' => [
                'search' => $request->input('search', ''),
                'stage' => $request->input('stage', 'all'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'corporate_account_id' => 'nullable|exists:crm_corporate_accounts,id',
            'car_id' => 'nullable|exists:cars,id',
            'stage' => 'required|string|in:lead_in,needs_analysis,vehicle_proposed,negotiation,won,lost',
            'value' => 'required|numeric|min:0',
            'win_probability' => 'nullable|integer|between:0,100',
            'expected_close_date' => 'nullable|date',
            'loss_reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_admin_id'] = auth('admin')->id();

        $this->dealService->createDeal($validated);

        return redirect()->back()->with('success', 'Deal created successfully.');
    }

    public function updateStage(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => 'required|string|in:lead_in,needs_analysis,vehicle_proposed,negotiation,won,lost',
        ]);

        $deal = Deal::findOrFail($id);
        $this->dealService->updateDealStage($deal, $validated['stage']);

        return redirect()->back()->with('success', "Deal stage updated to {$validated['stage']}.");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'corporate_account_id' => 'nullable|exists:crm_corporate_accounts,id',
            'car_id' => 'nullable|exists:cars,id',
            'stage' => 'required|string|in:lead_in,needs_analysis,vehicle_proposed,negotiation,won,lost',
            'value' => 'required|numeric|min:0',
            'win_probability' => 'nullable|integer|between:0,100',
            'expected_close_date' => 'nullable|date',
            'loss_reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $deal = Deal::findOrFail($id);
        $this->dealService->updateDeal($deal, $validated);

        return redirect()->back()->with('success', 'Deal updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $deal = Deal::findOrFail($id);
        $this->dealService->deleteDeal($deal);

        return redirect()->back()->with('success', 'Deal deleted successfully.');
    }
}
