<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmTask;
use App\Models\Owner;
use App\Services\Crm\OwnerCrmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OwnerCrmController extends Controller
{
    public function __construct(
        protected OwnerCrmService $ownerCrmService
    ) {}

    /**
     * Display the Fleet Owner 360 Directory.
     */
    public function index(Request $request): Response
    {
        $query = Owner::query()->with('preference');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($tier = $request->input('tier')) {
            $query->whereHas('preference', function ($q) use ($tier) {
                $q->where('partner_tier', $tier);
            });
        }

        $owners = $query->withCount(['cars', 'drivers', 'interactions', 'supportTickets'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('admin/crm/OwnerIndex', [
            'owners' => $owners,
            'filters' => $request->only(['search', 'tier']),
        ]);
    }

    /**
     * Display the Owner 360 Dossier & Relationship Timeline.
     */
    public function timeline(int $ownerId): Response
    {
        $owner = Owner::findOrFail($ownerId);
        $timelineData = $this->ownerCrmService->getOwnerTimeline($owner);

        return Inertia::render('admin/crm/OwnerTimeline', $timelineData);
    }

    /**
     * Log a CRM interaction for a fleet owner.
     */
    public function logInteraction(Request $request, int $ownerId): RedirectResponse
    {
        $owner = Owner::findOrFail($ownerId);

        $validated = $request->validate([
            'type' => 'required|string|in:call,email,meeting,note,whatsapp,sms',
            'subject' => 'required|string|max:150',
            'details' => 'required|string',
            'interaction_date' => 'nullable|date',
        ]);

        $this->ownerCrmService->logInteraction($owner, $validated);

        return redirect()->back()->with('success', 'Fleet owner interaction logged successfully.');
    }

    /**
     * Update owner CRM partner preferences & contract terms.
     */
    public function updatePreferences(Request $request, int $ownerId): RedirectResponse
    {
        $owner = Owner::findOrFail($ownerId);

        $validated = $request->validate([
            'partner_tier' => 'required|string|in:Standard,Silver Partner,Gold Partner,Platinum Partner',
            'payout_frequency' => 'required|string|in:Weekly,Bi-weekly,Monthly',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'payout_method' => 'required|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'routing_number' => 'nullable|string|max:100',
            'vip_partner' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $this->ownerCrmService->updatePreference($owner, $validated);

        return redirect()->back()->with('success', 'Fleet owner partner terms & preferences updated.');
    }

    /**
     * Schedule a follow-up task for a fleet owner.
     */
    public function addTask(Request $request, int $ownerId): RedirectResponse
    {
        $owner = Owner::findOrFail($ownerId);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        $this->ownerCrmService->addTask($owner, $validated);

        return redirect()->back()->with('success', 'Partner task scheduled successfully.');
    }

    /**
     * Mark a task as completed.
     */
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
