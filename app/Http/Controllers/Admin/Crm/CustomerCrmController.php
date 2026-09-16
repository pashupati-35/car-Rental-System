<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmTask;
use App\Models\Customer;
use App\Services\Crm\CustomerCrmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerCrmController extends Controller
{
    public function __construct(
        protected CustomerCrmService $customerCrmService
    ) {}

    public function index(Request $request): Response
    {
        $query = Customer::query()->with('preference');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($tier = $request->input('tier')) {
            $query->whereHas('preference', function ($q) use ($tier) {
                $q->where('loyalty_tier', $tier);
            });
        }

        $customers = $query->withCount(['bookedCars', 'interactions', 'supportTickets', 'quotations'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('admin/crm/CustomerIndex', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'tier']),
        ]);
    }

    public function timeline(int $customerId): Response
    {
        $customer = Customer::findOrFail($customerId);
        $timelineData = $this->customerCrmService->getCustomerTimeline($customer);

        return Inertia::render('admin/crm/CustomerTimeline', $timelineData);
    }

    public function logInteraction(Request $request, int $customerId): RedirectResponse
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'type' => 'required|string|in:call,email,meeting,note,whatsapp,sms',
            'subject' => 'required|string|max:150',
            'details' => 'required|string',
            'interaction_date' => 'nullable|date',
        ]);

        $this->customerCrmService->logInteraction($customer, $validated);

        return redirect()->back()->with('success', 'Customer interaction logged successfully.');
    }

    public function updatePreferences(Request $request, int $customerId): RedirectResponse
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'preferred_car_type' => 'nullable|string|max:50',
            'preferred_transmission' => 'nullable|string|in:Automatic,Manual',
            'preferred_fuel_type' => 'nullable|string|in:Petrol,Diesel,Electric,Hybrid',
            'needs_child_seat' => 'boolean',
            'needs_chauffeur' => 'boolean',
            'vip_status' => 'boolean',
            'loyalty_tier' => 'required|string|in:Standard,Silver,Gold,Platinum',
            'special_requests' => 'nullable|string',
        ]);

        $this->customerCrmService->updatePreference($customer, $validated);

        return redirect()->back()->with('success', 'Customer CRM preferences updated successfully.');
    }

    public function addTask(Request $request, int $customerId): RedirectResponse
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        $this->customerCrmService->addTask($customer, $validated);

        return redirect()->back()->with('success', 'Follow-up task scheduled successfully.');
    }

    public function completeTask(int $taskId): RedirectResponse
    {
        $task = CrmTask::findOrFail($taskId);
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Task marked as completed.');
    }
}
