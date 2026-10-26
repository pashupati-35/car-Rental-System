<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Quotation\StoreQuotationRequest;
use App\Http\Requests\Crm\Quotation\UpdateQuotationStatusRequest;
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
        $formData = $this->quotationService->getFormData();

        return Inertia::render('admin/crm/QuotationsList', [
            'quotations' => $quotations,
            'cars' => $formData['cars'],
            'customers' => $formData['customers'],
            'leads' => $formData['leads'],
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        $formData = $this->quotationService->getFormData();

        return Inertia::render('admin/crm/QuotationForm', [
            'cars' => $formData['cars'],
            'customers' => $formData['customers'],
            'leads' => $formData['leads'],
        ]);
    }

    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $quotation = $this->quotationService->createQuotation($validated, $items);

        return redirect()->route('admin.crm.quotations.index')->with('success', "Quotation {$quotation->quotation_number} generated successfully.");
    }

    public function updateStatus(UpdateQuotationStatusRequest $request, int $id): RedirectResponse
    {
        $status = $request->validated('status');
        $this->quotationService->updateStatus($id, $status);

        return redirect()->back()->with('success', "Quotation status updated to {$status}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->quotationService->deleteQuotation($id);

        return redirect()->back()->with('success', 'Quotation deleted successfully.');
    }
}
