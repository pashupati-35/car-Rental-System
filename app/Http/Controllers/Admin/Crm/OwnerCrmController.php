<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Owner\LogOwnerInteractionRequest;
use App\Http\Requests\Crm\Owner\ScheduleOwnerTaskRequest;
use App\Http\Requests\Crm\Owner\UpdateOwnerPreferenceRequest;
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
        $owners = $this->ownerCrmService->getPaginatedOwners(
            $request->only(['search', 'tier']),
            12
        );

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
        $timelineData = $this->ownerCrmService->getOwnerTimeline($ownerId);

        return Inertia::render('admin/crm/OwnerTimeline', $timelineData);
    }

    /**
     * Log a CRM interaction for a fleet owner.
     */
    public function logInteraction(LogOwnerInteractionRequest $request, int $ownerId): RedirectResponse
    {
        $this->ownerCrmService->logInteraction($ownerId, $request->validated());

        return redirect()->back()->with('success', 'Fleet owner interaction logged successfully.');
    }

    /**
     * Update owner CRM partner preferences & contract terms.
     */
    public function updatePreferences(UpdateOwnerPreferenceRequest $request, int $ownerId): RedirectResponse
    {
        $this->ownerCrmService->updatePreference($ownerId, $request->validated());

        return redirect()->back()->with('success', 'Fleet owner partner terms & preferences updated.');
    }

    /**
     * Schedule a follow-up task for a fleet owner.
     */
    public function addTask(ScheduleOwnerTaskRequest $request, int $ownerId): RedirectResponse
    {
        $validated = $request->validated();
        $this->ownerCrmService->addTask($ownerId, $validated);

        $message = (! empty($validated['to_owner']) || ! empty($validated['notify_recipient']))
            ? 'Partner task scheduled and email dispatched to fleet owner.'
            : 'Partner task scheduled successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Mark a task as completed.
     */
    public function completeTask(int $taskId): RedirectResponse
    {
        $this->ownerCrmService->completeTask($taskId);

        return redirect()->back()->with('success', 'Task marked as completed.');
    }
}
