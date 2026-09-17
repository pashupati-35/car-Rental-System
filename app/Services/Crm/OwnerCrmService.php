<?php

namespace App\Services\Crm;

use App\Jobs\Admin\FollowUpTaskJob;
use App\Models\BookingCar;
use App\Models\Crm\CrmTask;
use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\OwnerPreference;
use App\Models\Crm\SupportTicket;
use App\Models\Owner;
use Illuminate\Support\Facades\Log;

class OwnerCrmService
{
    /**
     * Get 360-degree timeline and relationship dossier for a Fleet Owner.
     */
    public function getOwnerTimeline(Owner $owner): array
    {
        $interactions = CustomerInteraction::where('owner_id', $owner->id)
            ->with('admin')
            ->latest('interaction_date')
            ->get();

        $preference = OwnerPreference::firstOrCreate(
            ['owner_id' => $owner->id],
            [
                'partner_tier' => 'Standard',
                'payout_frequency' => 'Monthly',
                'commission_rate' => 15.00,
                'payout_method' => 'Bank Transfer',
            ]
        );

        $tasks = CrmTask::where(function ($q) use ($owner) {
            $q->where('owner_id', $owner->id)
                ->orWhere(function ($sub) use ($owner) {
                    $sub->where('related_type', 'owner')
                        ->where('related_id', $owner->id);
                });
        })
        ->latest('due_date')
        ->get();

        $cars = $owner->cars()->latest('id')->get();
        $carIds = $cars->pluck('id');

        $recentBookings = BookingCar::whereIn('car_id', $carIds)
            ->with(['car', 'customer'])
            ->latest('id')
            ->take(10)
            ->get();

        $drivers = $owner->drivers()->latest('id')->take(10)->get();

        $supportTickets = SupportTicket::where('owner_id', $owner->id)
            ->orWhereIn('car_id', $carIds)
            ->latest('id')
            ->take(8)
            ->get();

        return [
            'owner' => $owner,
            'interactions' => $interactions,
            'preference' => $preference,
            'tasks' => $tasks,
            'cars' => $cars,
            'drivers' => $drivers,
            'recent_bookings' => $recentBookings,
            'support_tickets' => $supportTickets,
        ];
    }

    /**
     * Log a CRM interaction for a fleet owner.
     */
    public function logInteraction(Owner $owner, array $data): CustomerInteraction
    {
        $data['owner_id'] = $owner->id;
        $data['customer_id'] = null;
        $data['admin_id'] = auth('admin')->id();
        if (empty($data['interaction_date'])) {
            $data['interaction_date'] = now();
        }

        return CustomerInteraction::create($data);
    }

    /**
     * Update or create owner CRM partner preferences.
     */
    public function updatePreference(Owner $owner, array $data): OwnerPreference
    {
        $preference = OwnerPreference::firstOrCreate(['owner_id' => $owner->id]);
        $preference->update($data);

        return $preference;
    }

    /**
     * Add follow-up task for a fleet owner.
     */
    public function addTask(Owner $owner, array $data): CrmTask
    {
        $notifyRecipient = ! empty($data['to_owner']) || ! empty($data['notify_recipient']);

        $data['related_type'] = 'owner';
        $data['related_id'] = $owner->id;
        $data['owner_id'] = $owner->id;
        $data['assigned_admin_id'] = auth('admin')->id();
        $data['notify_recipient'] = $notifyRecipient;
        unset($data['to_owner']);

        $task = CrmTask::create($data);

        if ($notifyRecipient) {
            try {
                FollowUpTaskJob::dispatch($task, $owner);
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch FollowUpTaskJob for owner #{$owner->id}: ".$e->getMessage());
            }
        }

        return $task;
    }
}
