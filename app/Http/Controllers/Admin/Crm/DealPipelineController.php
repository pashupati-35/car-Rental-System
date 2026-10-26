<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Deal\StoreDealRequest;
use App\Http\Requests\Crm\Deal\UpdateDealRequest;
use App\Http\Requests\Crm\Deal\UpdateDealStageRequest;
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
        $formData = $this->dealService->getFormData();

        return Inertia::render('admin/crm/DealsPipeline', [
            'view_mode' => $viewMode,
            'stages' => $stages,
            'grouped_deals' => $groupedDeals,
            'deals_list' => $dealsList,
            'cars' => $formData['cars'],
            'customers' => $formData['customers'],
            'corporate_accounts' => $formData['corporate_accounts'],
            'leads' => $formData['leads'],
            'filters' => [
                'search' => $request->input('search', ''),
                'stage' => $request->input('stage', 'all'),
            ],
        ]);
    }

    public function store(StoreDealRequest $request): RedirectResponse
    {
        $this->dealService->createDeal($request->validated());

        return redirect()->back()->with('success', 'Deal created successfully.');
    }

    public function updateStage(UpdateDealStageRequest $request, int $id): RedirectResponse
    {
        $this->dealService->updateDealStage($id, $request->validated('stage'));

        return redirect()->back()->with('success', "Deal stage updated to {$request->validated('stage')}.");
    }

    public function update(UpdateDealRequest $request, int $id): RedirectResponse
    {
        $this->dealService->updateDeal($id, $request->validated());

        return redirect()->back()->with('success', 'Deal updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->dealService->deleteDeal($id);

        return redirect()->back()->with('success', 'Deal deleted successfully.');
    }
}
