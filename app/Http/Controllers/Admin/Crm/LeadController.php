<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Crm\Lead;
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
        $cars = Car::select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day')->get();

        return Inertia::render('admin/crm/LeadsList', [
            'leads' => $leads,
            'counts' => $statusCounts,
            'cars' => $cars,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'source' => 'required|string|in:website,phone,walk_in,referral,corporate,ai_chat,other',
            'status' => 'required|string|in:new,contacted,qualified,proposal_sent,converted,lost',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'interested_car_id' => 'nullable|exists:cars,id',
            'pickup_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:pickup_date',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_admin_id'] = auth('admin')->id();

        $this->leadService->createLead($validated);

        return redirect()->back()->with('success', 'Lead created successfully.');
    }

    public function show(int $id): Response
    {
        $lead = Lead::with(['interestedCar', 'convertedCustomer', 'assignedAdmin', 'deals', 'quotations'])
            ->findOrFail($id);

        $cars = Car::select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day')->get();

        return Inertia::render('admin/crm/LeadDetails', [
            'lead' => $lead,
            'cars' => $cars,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'source' => 'required|string|in:website,phone,walk_in,referral,corporate,ai_chat,other',
            'status' => 'required|string|in:new,contacted,qualified,proposal_sent,converted,lost',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'interested_car_id' => 'nullable|exists:cars,id',
            'pickup_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:pickup_date',
            'notes' => 'nullable|string',
        ]);

        $this->leadService->updateLead($lead, $validated);

        return redirect()->back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $lead = Lead::findOrFail($id);
        $this->leadService->deleteLead($lead);

        return redirect()->route('admin.crm.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function convert(Request $request, int $id): RedirectResponse
    {
        $lead = Lead::findOrFail($id);

        $customer = $this->leadService->convertLeadToCustomer($lead, [
            'create_deal' => (bool) $request->input('create_deal', true),
            'deal_title' => $request->input('deal_title'),
        ]);

        return redirect()->route('admin.crm.leads.index')->with('success', "Lead successfully converted to Customer (#{$customer->id}: {$customer->name}).");
    }
}
