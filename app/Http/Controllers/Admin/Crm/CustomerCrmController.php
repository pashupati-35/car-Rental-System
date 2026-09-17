<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Customer\LogCustomerInteractionRequest;
use App\Http\Requests\Crm\Customer\ScheduleCustomerTaskRequest;
use App\Http\Requests\Crm\Customer\UpdateCustomerPreferenceRequest;
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
        $customers = $this->customerCrmService->getPaginatedCustomers(
            $request->only(['search', 'tier']),
            12
        );

        return Inertia::render('admin/crm/CustomerIndex', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'tier']),
        ]);
    }

    public function timeline(int $customerId): Response
    {
        $timelineData = $this->customerCrmService->getCustomerTimeline($customerId);

        return Inertia::render('admin/crm/CustomerTimeline', $timelineData);
    }

    public function logInteraction(LogCustomerInteractionRequest $request, int $customerId): RedirectResponse
    {
        $this->customerCrmService->logInteraction($customerId, $request->validated());

        return redirect()->back()->with('success', 'Customer interaction logged successfully.');
    }

    public function updatePreferences(UpdateCustomerPreferenceRequest $request, int $customerId): RedirectResponse
    {
        $this->customerCrmService->updatePreference($customerId, $request->validated());

        return redirect()->back()->with('success', 'Customer CRM preferences updated successfully.');
    }

    public function addTask(ScheduleCustomerTaskRequest $request, int $customerId): RedirectResponse
    {
        $validated = $request->validated();
        $this->customerCrmService->addTask($customerId, $validated);

        $message = (! empty($validated['to_customer']) || ! empty($validated['notify_recipient']))
            ? 'Follow-up task scheduled and email dispatched to customer.'
            : 'Follow-up task scheduled successfully.';

        return redirect()->back()->with('success', $message);
    }

    public function completeTask(int $taskId): RedirectResponse
    {
        $this->customerCrmService->completeTask($taskId);

        return redirect()->back()->with('success', 'Task marked as completed.');
    }
}
