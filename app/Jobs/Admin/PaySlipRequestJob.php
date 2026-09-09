<?php

namespace App\Jobs\Admin;

use App\Mail\Admin\WelcomeEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PaySlipRequestJob implements ShouldQueue
{
    use Dispatchable ,InteractsWithQueue, Queueable, SerializesModels;

    protected $payslip;

    public function __construct($payslip)
    {
        $this->payslip = $payslip;
    }

    public function handle(): void
    {
        $adminEmails = getAdminEmails();
        setSMTP();
        $emailTemplate = getEmailTemplate('admin', 'payslip_request_email');
        $acceptedData = [
            'first_name' => $this->payslip->employee?->first_name,
            'last_name' => $this->payslip->employee?->last_name,
            'email' => $this->payslip->employee?->email,
            'payslip_number' => $this->payslip->payroll_number,
            'start_date' => formatDate($this->payslip->pay_period_start),
            'end_date' => formatDate($this->payslip->pay_period_end),
            'request_reason' => $this->payslip->request_reason,
        ];

        $acceptedTag = [];
        $acceptedInputs = normalizeEmailTemplateInputs($emailTemplate->accepted_inputs);

        $content = renderEmailHTML($emailTemplate->description, $acceptedTag);
        $content = renderEmailData($content, $acceptedInputs, $acceptedData);

        Mail::to($adminEmails)->send(new WelcomeEmailMail($content, $emailTemplate));
    }
}
