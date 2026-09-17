<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Lead\ConvertLeadRequest;
use App\Http\Requests\Crm\Lead\StoreLeadRequest;
use App\Http\Requests\Crm\Lead\UpdateLeadRequest;
use App\Services\Crm\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status', 'all'),
            'source' => $request->input('source', 'all'),
            'priority' => $request->input('priority', 'all'),
        ];

        $leads = $this->leadService->getLeads($filters, $perPage);
        $statusCounts = $this->leadService->getStatusCounts();
        $cars = $this->leadService->getCarOptions();

        return Inertia::render('admin/crm/LeadsList', [
            'leads' => $leads,
            'counts' => $statusCounts,
            'cars' => $cars,
            'filters' => $filters,
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $this->leadService->createLead($request->validated());

        return redirect()->back()->with('success', 'Lead created successfully.');
    }

    public function show(int $id): Response
    {
        $lead = $this->leadService->getLead($id);
        $cars = $this->leadService->getCarOptions();

        return Inertia::render('admin/crm/LeadDetails', [
            'lead' => $lead,
            'cars' => $cars,
        ]);
    }

    public function update(UpdateLeadRequest $request, int $id): RedirectResponse
    {
        $this->leadService->updateLead($id, $request->validated());

        return redirect()->back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->leadService->deleteLead($id);

        return redirect()->route('admin.crm.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function convert(ConvertLeadRequest $request, int $id): RedirectResponse
    {
        $customer = $this->leadService->convertLeadToCustomer($id, $request->validated());

        return redirect()->route('admin.crm.leads.index')->with('success', "Lead successfully converted to Customer (#{$customer->id}: {$customer->name}).");
    }
}
