<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\FollowUpTaskMail;
use App\Models\Crm\CrmTask;
use App\Models\Customer;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FollowUpTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public CrmTask $task;

    public $recipient;

    public function __construct(CrmTask $task, $recipient)
    {
        $this->task = $task;
        $this->recipient = $recipient;
    }

    public function handle(): void
    {
        try {
            setSMTP();
        } catch (\Throwable $e) {
            Log::warning('SMTP initialization warning in FollowUpTaskJob: '.$e->getMessage());
        }

        $role = 'customer';
        if ($this->recipient instanceof Owner) {
            $role = 'owner';
        } elseif ($this->recipient instanceof Customer) {
            $role = 'customer';
        }

        $emailTemplate = getEmailTemplate($role, 'follow_up_task');
        if (! $emailTemplate) {
            Log::warning("Follow-up task email template not found for role: {$role}");

            return;
        }

        $recipientEmail = $this->recipient->email ?? null;
        if (! $recipientEmail) {
            Log::warning("Recipient has no email address for follow-up task #{$this->task->id}");

            return;
        }

        $recipientName = $this->recipient->first_name
            ?? $this->recipient->name
            ?? $this->recipient->full_name
            ?? ($role === 'owner' ? 'Fleet Partner' : 'Valued Customer');

        $dueDateStr = $this->task->due_date
            ? Carbon::parse($this->task->due_date)->format('M d, Y')
            : 'No due date specified';

        $acceptedData = [
            'first_name' => $recipientName,
            'email' => $recipientEmail,
            'task_title' => $this->task->title ?? '',
            'task_description' => $this->task->description ?: 'No additional notes provided.',
            'due_date' => $dueDateStr,
            'priority' => ucfirst($this->task->priority ?? 'medium'),
            'portal_link' => url('/'),
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        $customSubject = renderEmailData($emailTemplate->subject, $acceptedInputs, $acceptedData);

        try {
            Mail::to($recipientEmail)->send(new FollowUpTaskMail($content, $emailTemplate, $customSubject));
        } catch (\Throwable $e) {
            Log::error("Failed to send follow-up task email to {$recipientEmail}: ".$e->getMessage());
        }
    }
}
