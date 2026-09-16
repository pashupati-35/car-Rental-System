<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Crm\Lead;
use App\Models\Crm\Quotation;
use App\Models\Customer;
use App\Services\Crm\QuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuotationController extends Controller
{
    public function __construct(
        protected QuotationService $quotationService
    ) {}

    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status', 'all'),
        ];

        $quotations = $this->quotationService->getQuotations($filters, $perPage);
        $cars = Car::select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day')->get();
        $customers = Customer::select('id', 'name', 'email', 'phone_number')->latest('id')->take(100)->get();
        $leads = Lead::select('id', 'first_name', 'last_name', 'email')->where('status', '!=', 'converted')->latest('id')->take(100)->get();

        return Inertia::render('admin/crm/QuotationsList', [
            'quotations' => $quotations,
            'cars' => $cars,
            'customers' => $customers,
            'leads' => $leads,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $cars = Car::select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day')->get();
        $customers = Customer::select('id', 'name', 'email', 'phone_number')->latest('id')->take(100)->get();
        $leads = Lead::select('id', 'first_name', 'last_name', 'email')->where('status', '!=', 'converted')->latest('id')->take(100)->get();

        return Inertia::render('admin/crm/QuotationForm', [
            'cars' => $cars,
            'customers' => $customers,
            'leads' => $leads,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_rate' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'valid_until' => 'nullable|date',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:draft,sent,accepted,rejected,expired',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $quotation = $this->quotationService->createQuotation($validated, $items);

        return redirect()->route('admin.crm.quotations.index')->with('success', "Quotation {$quotation->quotation_number} generated successfully.");
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:draft,sent,accepted,rejected,expired',
        ]);

        $quotation = Quotation::findOrFail($id);
        $this->quotationService->updateStatus($quotation, $validated['status']);

        return redirect()->back()->with('success', "Quotation status updated to {$validated['status']}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $quotation = Quotation::findOrFail($id);
        $this->quotationService->deleteQuotation($quotation);

        return redirect()->back()->with('success', 'Quotation deleted successfully.');
    }
}
