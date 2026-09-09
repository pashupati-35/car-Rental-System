<?php

return [
    'roles' => [
        'admin' => [
            'email_verification_code' => [
                'title' => 'Administrator email verification',
                'identifier' => 'account-verify-email-admin',
                'subject' => 'Verify your email address to activate your Futech Solution account',
                'role' => 'admin',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your administrator account requires email verification before it can be activated.</p>
<p>Enter the verification code below on the Futech Solution verification screen:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p>For your security, do not share this code with anyone.</p>
<p>If you did not expect this verification request, you may ignore this email or contact the system administrator.</p>',
                'message_content' => 'Your Futech Solution administrator verification code is {{$verification_code}}. Do not share this code.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'Use this code only on the official Futech Solution verification screen.',
                'alert_message' => 'If you did not request this verification, do not share the code with anyone.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'welcome_email' => [
                'title' => 'Administrator account created',
                'identifier' => 'welcome-email',
                'subject' => 'Your Futech Solution administrator account is ready',
                'role' => 'admin',
                'type' => 'onboarding',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your administrator account for Futech Solution has been created successfully.</p>
<p><strong>Account details</strong></p>
<ul>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Temporary password:</strong> {{$password}}</li>
</ul>
<p>Please sign in using these credentials and change the temporary password immediately after your first login.</p>
<p>Administrator access may include sensitive company information. Keep your credentials confidential and use them only on authorized Futech Solution systems.</p>',
                'message_content' => 'Your Futech Solution administrator account has been created. Sign in and change your temporary password immediately.',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                    'password',
                ],
                'message_data' => [],
                'info_message' => 'Change the temporary password after your first successful sign-in.',
                'alert_message' => 'Do not share administrator credentials with anyone.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'new_employee' => [
                'title' => 'New employee registration',
                'identifier' => 'new_employee',
                'subject' => 'Action required: new employee registration',
                'role' => 'admin',
                'type' => 'hr',
                'description' => '<p>Hello Admin,</p>
<p>A new employee registration has been received and requires review.</p>
<p><strong>Employee details</strong></p>
<ul>
    <li><strong>Name:</strong> {{$employee_name}}</li>
    <li><strong>Email:</strong> {{$employee_email}}</li>
</ul>
<p>Please review the employee record and complete the required approval, onboarding, or account-setup actions in the administration panel.</p>',
                'message_content' => 'A new employee registration for {{$employee_name}} requires review.',
                'accepted_inputs' => [
                    'employee_name',
                    'employee_email',
                ],
                'message_data' => [],
                'info_message' => 'Review the employee details before completing onboarding or approval.',
                'alert_message' => 'A new employee registration is awaiting administrative action.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'password_changed' => [
                'title' => 'Administrator password changed',
                'identifier' => 'password_changed',
                'subject' => 'Your Futech Solution password has been changed',
                'role' => 'admin',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}} {{$middle_name}} {{$last_name}},</p>
<p>This email confirms that the password for your Futech Solution account has been changed successfully.</p>
<p><strong>Account:</strong> {{$email}}</p>
<p>If you made this change, no further action is required.</p>
<p>If you did not make this change, contact the support or system administration team immediately so the account can be secured.</p>
<p>Never share passwords or verification codes with another person.</p>',
                'message_content' => 'The password for your Futech Solution account {{$email}} was changed. If this was not you, contact support immediately.',
                'accepted_inputs' => [
                    'email',
                    'first_name',
                    'middle_name',
                    'last_name',
                ],
                'message_data' => [],
                'info_message' => 'No action is required if you changed the password.',
                'alert_message' => 'If you did not make this change, contact support immediately.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'verification_email' => [
                'title' => 'Administrator sign-in verification',
                'identifier' => 'account-verify-email-admin',
                'subject' => 'Verify your identity to continue signing in',
                'role' => 'admin',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>A sign-in attempt to your Futech Solution administrator account requires additional verification.</p>
<p>Use the verification code below to continue:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p>Do not share this code with anyone.</p>
<p>If you did not attempt to sign in, you may ignore this email and review your account security.</p>',
                'message_content' => 'Your Futech Solution sign-in verification code is {{$verification_code}}. Do not share this code.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'This code is intended only for the current sign-in request.',
                'alert_message' => 'If you did not attempt to sign in, review your account security.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'application-submitted-admin' => [
                'title' => 'New application submitted',
                'identifier' => 'application-submitted-admin',
                'subject' => 'Action required: new application submitted',
                'role' => 'admin',
                'type' => 'application',
                'description' => '<p>Hello Admin,</p>
<p>A new application has been submitted and is ready for review.</p>
<p><strong>Applicant details</strong></p>
<ul>
    <li><strong>Name:</strong> {{$first_name}} {{$last_name}}</li>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Mobile:</strong> {{$mobile}}</li>
    <li><strong>Submitted at:</strong> {{$submitted_at}}</li>
</ul>
<p>Please review the application in the administration panel and complete the appropriate next step.</p>',
                'message_content' => 'A new application from {{$first_name}} {{$last_name}} was submitted at {{$submitted_at}} and requires review.',
                'accepted_inputs' => [
                    'first_name',
                    'last_name',
                    'email',
                    'mobile',
                    'submitted_at',
                ],
                'message_data' => [],
                'info_message' => 'Review the submitted information before making a decision.',
                'alert_message' => 'A new application is awaiting administrative review.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'leave_request_email' => [
                'title' => 'Leave request awaiting review',
                'identifier' => 'leave-request-notification',
                'subject' => 'Action required: leave request from {{$first_name}}',
                'role' => 'admin',
                'type' => 'leave',
                'description' => '<p>Hello Admin,</p>
<p>{{$first_name}} has submitted a leave request that requires review.</p>
<p><strong>Leave details</strong></p>
<ul>
    <li><strong>Leave type:</strong> {{$leave_type}}</li>
    <li><strong>Start date:</strong> {{$start_date}}</li>
    <li><strong>End date:</strong> {{$end_date}}</li>
    <li><strong>Reason:</strong> {{$reason}}</li>
</ul>
<p>Please review the request and approve or decline it in accordance with company leave policy.</p>',
                'message_content' => '{{$first_name}} submitted a {{$leave_type}} leave request from {{$start_date}} to {{$end_date}}.',
                'accepted_inputs' => [
                    'first_name',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'reason',
                ],
                'message_data' => [],
                'info_message' => 'Review the leave dates and reason before making a decision.',
                'alert_message' => 'This leave request is awaiting approval or decline.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'payslip_request_email' => [
                'title' => 'Payslip request awaiting review',
                'identifier' => 'payslip-request-notification',
                'subject' => 'Action required: payslip request from {{$first_name}} {{$last_name}}',
                'role' => 'admin',
                'type' => 'payroll',
                'description' => '<p>Hello Admin,</p>
<p>{{$first_name}} {{$last_name}} has submitted a payslip request that requires review and processing.</p>
<p><strong>Request details</strong></p>
<ul>
    <li><strong>Payslip number:</strong> {{$payslip_number}}</li>
    <li><strong>Period:</strong> {{$start_date}} to {{$end_date}}</li>
    <li><strong>Reason:</strong> {{$request_reason}}</li>
    <li><strong>Email:</strong> {{$email}}</li>
</ul>
<p>Please verify the request and complete the appropriate payroll action in the administration panel.</p>',
                'message_content' => 'Payslip request {{$payslip_number}} from {{$first_name}} {{$last_name}} requires review.',
                'accepted_inputs' => [
                    'first_name',
                    'last_name',
                    'email',
                    'payslip_number',
                    'start_date',
                    'end_date',
                    'request_reason',
                ],
                'message_data' => [],
                'info_message' => 'Verify the employee and requested pay period before processing.',
                'alert_message' => 'A payslip request is awaiting administrative review.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'contact_us_email' => [
                'title' => 'New contact enquiry',
                'identifier' => 'contact-us-email',
                'subject' => 'New website contact enquiry: {{$subject}}',
                'role' => 'admin',
                'type' => 'contact',
                'description' => '<p>Hello Admin,</p>
<p>A new enquiry has been submitted through the Futech Solution contact form.</p>
<p><strong>Contact details</strong></p>
<ul>
    <li><strong>Name:</strong> {{$first_name}} {{$last_name}}</li>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Phone:</strong> {{$phone}}</li>
    <li><strong>Subject:</strong> {{$subject}}</li>
</ul>
<p><strong>Message</strong></p>
<p>{{$message}}</p>
<p>Please review the enquiry and respond through the appropriate company communication channel.</p>',
                'message_content' => 'New contact enquiry from {{$first_name}} {{$last_name}}: {{$subject}}',
                'accepted_inputs' => [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'subject',
                    'message',
                ],
                'message_data' => [],
                'info_message' => 'Review the enquiry details before responding.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'career_application_email' => [
                'title' => 'New career application',
                'identifier' => 'career-application-email',
                'subject' => 'New career application for {{$career_title}}',
                'role' => 'admin',
                'type' => 'recruitment',
                'description' => '<p>Hello Admin,</p>
<p>A new career application has been submitted for <strong>{{$career_title}}</strong>.</p>
<p><strong>Applicant details</strong></p>
<ul>
    <li><strong>Name:</strong> {{$first_name}} {{$last_name}}</li>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Phone:</strong> {{$phone}}</li>
    <li><strong>Position:</strong> {{$career_title}}</li>
    <li><strong>Submitted at:</strong> {{$submitted_at}}</li>
</ul>
<p>Please review the application and proceed according to the company recruitment process.</p>',
                'message_content' => 'New career application from {{$first_name}} {{$last_name}} for {{$career_title}}.',
                'accepted_inputs' => [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'career_title',
                    'submitted_at',
                ],
                'message_data' => [],
                'info_message' => 'Review the application against the requirements for the advertised position.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

        ],

        'employee' => [
            'welcome_email' => [
                'title' => 'Employee account created',
                'identifier' => 'welcome-email',
                'subject' => 'Welcome to Futech Solution — your account is ready',
                'role' => 'employee',
                'type' => 'onboarding',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Welcome to Futech Solution. Your employee account has been created successfully.</p>
<p><strong>Account email:</strong> {{$email}}</p>
<p>For security, create your password using the secure account setup link below before signing in:</p>
<p><a href="{{$reset_link}}" target="_blank">Set your password</a></p>
<p>Once completed, you can access the company services and information assigned to your role.</p>
<p>If you experience any difficulty accessing your account, contact the designated support or administration team.</p>',
                'message_content' => 'Your Futech Solution employee account is ready. Set your password using the secure account setup link.',
                'accepted_inputs' => [
                    'first_name',
                    'reset_link',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'Complete password setup before signing in for the first time.',
                'alert_message' => 'Do not forward or share your account setup link.',
                'cta_url' => '{{$reset_link}}',
                'cta_text' => 'Set Password',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'disapproved_email' => [
                'title' => 'Application decision',
                'identifier' => 'disapproved-email',
                'subject' => 'Update on your Futech Solution application',
                'role' => 'employee',
                'type' => 'application',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Thank you for your interest in Futech Solution.</p>
<p>After reviewing your application, we are unable to approve it at this time.</p>
<p><strong>Reason</strong></p>
<p>{{$reasons_for_disapproval}}</p>
<p>If you need clarification or further guidance, please use the support link below:</p>
<p><a href="{{$support_link}}" target="_blank">Contact support</a></p>
<p>Thank you for the time and effort you invested in your application.</p>',
                'message_content' => 'There is an update on your Futech Solution application. Please review the decision details.',
                'accepted_inputs' => [
                    'first_name',
                    'reasons_for_disapproval',
                    'support_link',
                ],
                'message_data' => [],
                'info_message' => 'You may contact support if you require clarification about the decision.',
                'alert_message' => null,
                'cta_url' => '{{$support_link}}',
                'cta_text' => 'Contact Support',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'mfa_verification_email' => [
                'title' => 'Employee email verification',
                'identifier' => 'account-verify-email-user',
                'subject' => 'Verify your email address to activate your Futech Solution account',
                'role' => 'employee',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your Futech Solution account requires email verification before activation.</p>
<p>Enter the code below on the verification screen:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p>Keep this code confidential and enter it only on an authorized Futech Solution page.</p>
<p>If you did not expect this account or verification request, you may ignore this email and contact support if necessary.</p>',
                'message_content' => 'Your Futech Solution email verification code is {{$verification_code}}. Do not share this code.',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'Use this code only for the current email verification request.',
                'alert_message' => 'Never share a verification code with another person.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'verification_code_email' => [
                'title' => 'Sign-in verification',
                'identifier' => 'verify-your-identity',
                'subject' => 'Your Futech Solution sign-in verification code',
                'role' => 'employee',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>A sign-in request requires verification before access can be granted to your Futech Solution account.</p>
<p>Enter the verification code below to continue:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p>Do not share this code with anyone.</p>
<p>If you did not attempt to sign in, you may ignore this email.</p>',
                'message_content' => 'Use code {{$verification_code}} to verify your Futech Solution sign-in request. Do not share this code.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'This code is intended only for the current sign-in request.',
                'alert_message' => 'If you did not request this sign-in, no action is required.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'verification_email' => [
                'title' => 'Employee account verification',
                'identifier' => 'account-verify-email-employee',
                'subject' => 'Verify your email address to continue',
                'role' => 'employee',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>We received a request to verify your email address for a Futech Solution account.</p>
<p>Use the verification code below or the secure verification link to continue:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p><a href="{{$href}}" target="_blank">{{$link_text}}</a></p>
<p>For your security, do not share the code or verification link.</p>
<p>If you did not initiate this request, you may ignore this email.</p>',
                'message_content' => 'Use code {{$verification_code}} to verify your Futech Solution email address. Do not share this code.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                    'href',
                    'link_text',
                ],
                'message_data' => [],
                'info_message' => 'Complete verification using either the code or the secure link.',
                'alert_message' => 'If you did not request this verification, do not share the code or link.',
                'cta_url' => '{{$href}}',
                'cta_text' => '{{$link_text}}',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'password_reset_email' => [
                'title' => 'Password reset request',
                'identifier' => 'account-password-reset',
                'subject' => 'Reset your Futech Solution account password',
                'role' => 'employee',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>We received a request to reset the password for your Futech Solution account.</p>
<p>Use the secure link below to create a new password:</p>
<p><a href="{{$href}}" target="_blank">{{$link_text}}</a></p>
<p>If you did not request a password reset, you may ignore this email. Your current password will remain unchanged.</p>
<p>Do not forward or share the password reset link.</p>',
                'message_content' => 'A password reset was requested for your Futech Solution account. If this was not you, no action is required.',
                'accepted_inputs' => [
                    'first_name',
                    'href',
                    'link_text',
                ],
                'message_data' => [],
                'info_message' => 'The reset link should be used only by the account owner.',
                'alert_message' => 'If you did not request this reset, do not open or share the link.',
                'cta_url' => '{{$href}}',
                'cta_text' => '{{$link_text}}',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'leave_request_email' => [
                'title' => 'Leave request submitted',
                'identifier' => 'leave-request-notification',
                'subject' => 'Your leave request has been submitted',
                'role' => 'employee',
                'type' => 'leave',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your leave request has been submitted successfully and is awaiting review.</p>
<p><strong>Request details</strong></p>
<ul>
    <li><strong>Leave type:</strong> {{$leave_type}}</li>
    <li><strong>Start date:</strong> {{$start_date}}</li>
    <li><strong>End date:</strong> {{$end_date}}</li>
    <li><strong>Reason:</strong> {{$reason}}</li>
</ul>
<p>You will be notified after the request has been reviewed.</p>',
                'message_content' => 'Your {{$leave_type}} leave request from {{$start_date}} to {{$end_date}} has been submitted for review.',
                'accepted_inputs' => [
                    'first_name',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'reason',
                ],
                'message_data' => [],
                'info_message' => 'Your leave request is currently awaiting review.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'leave_request_approval_email' => [
                'title' => 'Leave request approved',
                'identifier' => 'leave-request-approval-notification',
                'subject' => 'Your leave request has been approved',
                'role' => 'employee',
                'type' => 'leave',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your leave request has been approved.</p>
<p><strong>Approved leave details</strong></p>
<ul>
    <li><strong>Leave type:</strong> {{$leave_type}}</li>
    <li><strong>Start date:</strong> {{$start_date}}</li>
    <li><strong>End date:</strong> {{$end_date}}</li>
    <li><strong>Paid leave:</strong> {{$is_paid}}</li>
    <li><strong>Reason:</strong> {{$reason}}</li>
    <li><strong>Note:</strong> {{$note}}</li>
</ul>
<p>Please complete any required work handover or internal arrangements before your leave begins.</p>',
                'message_content' => 'Your {{$leave_type}} leave request from {{$start_date}} to {{$end_date}} has been approved.',
                'accepted_inputs' => [
                    'first_name',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'is_paid',
                    'note',
                    'reason',
                ],
                'message_data' => [],
                'info_message' => 'Review the approved dates and any note provided with the decision.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'leave_request_decline_email' => [
                'title' => 'Leave request not approved',
                'identifier' => 'leave-request-decline-notification',
                'subject' => 'Update on your leave request',
                'role' => 'employee',
                'type' => 'leave',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your leave request has not been approved.</p>
<p><strong>Request details</strong></p>
<ul>
    <li><strong>Leave type:</strong> {{$leave_type}}</li>
    <li><strong>Start date:</strong> {{$start_date}}</li>
    <li><strong>End date:</strong> {{$end_date}}</li>
    <li><strong>Reason:</strong> {{$reason}}</li>
</ul>
<p>If you require clarification or need to discuss the decision, please contact your manager or the HR team.</p>',
                'message_content' => 'Your {{$leave_type}} leave request from {{$start_date}} to {{$end_date}} was not approved.',
                'accepted_inputs' => [
                    'first_name',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'reason',
                ],
                'message_data' => [],
                'info_message' => 'Contact your manager or HR if you need clarification about the decision.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'approved_payslip' => [
                'title' => 'Payslip available',
                'identifier' => 'payslip',
                'subject' => 'Your payslip is now available',
                'role' => 'employee',
                'type' => 'payroll',
                'description' => '<p>Hello {{$first_name}} {{$last_name}},</p>
<p>Your payslip for <strong>{{$pay_period_start}} to {{$pay_period_end}}</strong> is now available.</p>
<p><strong>Payslip summary</strong></p>
<ul>
    <li><strong>Payslip number:</strong> {{$payslip_number}}</li>
    <li><strong>Basic salary:</strong> {{$basic_salary}}</li>
    <li><strong>Total allowance:</strong> {{$total_allowance}}</li>
    <li><strong>Gross salary:</strong> {{$gross_salary}}</li>
    <li><strong>Net salary:</strong> {{$net_salary}}</li>
    <li><strong>Pay period:</strong> {{$pay_period_start}} to {{$pay_period_end}}</li>
</ul>
<p>Please review the payslip carefully. If you identify any discrepancy or have a payroll question, contact the HR or payroll team.</p>',
                'message_content' => 'Your payslip {{$payslip_number}} for {{$pay_period_start}} to {{$pay_period_end}} is available.',
                'accepted_inputs' => [
                    'payslip_number',
                    'first_name',
                    'last_name',
                    'pay_period_start',
                    'pay_period_end',
                    'basic_salary',
                    'gross_salary',
                    'total_allowance',
                    'net_salary',
                ],
                'message_data' => [],
                'info_message' => 'Review the payslip details and contact HR or payroll if you identify a discrepancy.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'declined_payslip' => [
                'title' => 'Payslip request not approved',
                'identifier' => 'declined-payslip',
                'subject' => 'Update on your payslip request',
                'role' => 'employee',
                'type' => 'payroll',
                'description' => '<p>Hello {{$first_name}} {{$last_name}},</p>
<p>Your payslip request for <strong>{{$pay_period_start}} to {{$pay_period_end}}</strong> has not been approved.</p>
<p><strong>Request details</strong></p>
<ul>
    <li><strong>Payslip number:</strong> {{$payslip_number}}</li>
    <li><strong>Pay period:</strong> {{$pay_period_start}} to {{$pay_period_end}}</li>
    <li><strong>Reason:</strong> {{$decline_reason}}</li>
</ul>
<p>If you require clarification or believe additional information should be considered, please contact the HR or payroll team.</p>',
                'message_content' => 'Payslip request {{$payslip_number}} was not approved. Review the request details for more information.',
                'accepted_inputs' => [
                    'payslip_number',
                    'first_name',
                    'last_name',
                    'pay_period_start',
                    'pay_period_end',
                    'decline_reason',
                ],
                'message_data' => [],
                'info_message' => 'Contact HR or payroll if you require clarification.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'payslip_request_email' => [
                'title' => 'Payslip request received',
                'identifier' => 'payslip-request-notification',
                'subject' => 'We received your payslip request',
                'role' => 'employee',
                'type' => 'payroll',
                'description' => '<p>Hello {{$first_name}} {{$last_name}},</p>
<p>Your payslip request has been received successfully.</p>
<p><strong>Request details</strong></p>
<ul>
    <li><strong>Payslip number:</strong> {{$payslip_number}}</li>
    <li><strong>Requested period:</strong> {{$start_date}} to {{$end_date}}</li>
    <li><strong>Reason:</strong> {{$request_reason}}</li>
</ul>
<p>The request is now awaiting review and processing. You will be notified when its status changes.</p>',
                'message_content' => 'Your payslip request {{$payslip_number}} for {{$start_date}} to {{$end_date}} has been received.',
                'accepted_inputs' => [
                    'first_name',
                    'last_name',
                    'email',
                    'payslip_number',
                    'start_date',
                    'end_date',
                    'request_reason',
                ],
                'message_data' => [],
                'info_message' => 'Your payslip request is awaiting review and processing.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'contract_approval_email' => [
                'title' => 'Contract approved',
                'identifier' => 'contract-approval-notification',
                'subject' => 'Your contract has been approved',
                'role' => 'employee',
                'type' => 'contract',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your contract, <strong>{{$contract_title}}</strong>, was approved on <strong>{{$approval_date}}</strong>.</p>
<p>Please review the approved contract and complete any required acknowledgement, signing, onboarding, or other next steps communicated by the company.</p>
<p>If any information appears incorrect, contact your manager or the HR team.</p>',
                'message_content' => 'Your contract {{$contract_title}} was approved on {{$approval_date}}.',
                'accepted_inputs' => [
                    'first_name',
                    'contract_title',
                    'approval_date',
                ],
                'message_data' => [],
                'info_message' => 'Review the approved contract and complete any required next steps.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'contract_terminate_email' => [
                'title' => 'Contract termination notice',
                'identifier' => 'contract-terminate-notification',
                'subject' => 'Important: contract termination notice',
                'role' => 'employee',
                'type' => 'contract',
                'description' => '<p>Hello {{$first_name}},</p>
<p>This notice confirms that your contract, <strong>{{$contract_title}}</strong>, is scheduled to terminate effective <strong>{{$terminate_date}}</strong>.</p>
<p><strong>Reason for termination</strong></p>
<p>{{$terminate_reason}}</p>
<p>Please review any required offboarding, handover, company-property, access, payroll, or documentation requirements communicated by your manager or HR.</p>
<p>If you require clarification regarding this notice, contact your manager or the HR team.</p>',
                'message_content' => 'Your contract {{$contract_title}} is scheduled to terminate effective {{$terminate_date}}.',
                'accepted_inputs' => [
                    'first_name',
                    'contract_title',
                    'terminate_date',
                    'terminate_reason',
                ],
                'message_data' => [],
                'info_message' => 'Contact your manager or HR if you require clarification about the termination process.',
                'alert_message' => 'Review any required offboarding or handover actions before the effective termination date.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'event_mail' => [
                'title' => 'Company event notification',
                'identifier' => 'event-mail',
                'subject' => 'New company event: {{$event_title}}',
                'role' => 'employee',
                'type' => 'event',
                'description' => '<p>A company event has been scheduled.</p>
<p><strong>Event details</strong></p>
<ul>
    <li><strong>Event:</strong> {{$event_title}}</li>
    <li><strong>Date:</strong> {{$event_date}}</li>
    <li><strong>Description:</strong> {{$description}}</li>
    <li><strong>Note:</strong> {{$note}}</li>
</ul>
<p>Please review the event information and make any necessary arrangements to attend or participate.</p>',
                'message_content' => '{{$event_title}} is scheduled for {{$event_date}}. Review the event details for additional information.',
                'accepted_inputs' => [
                    'event_title',
                    'event_date',
                    'description',
                    'note',
                ],
                'message_data' => [],
                'info_message' => 'Review the event date, description, and any note provided by the organizer.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

        ],

        'tags' => [
            'confirm_link' => '<a href="{{$confirm_href}}" target="_blank">{{$confirm_link_text}}</a>',
            'decline_link' => '<a href="{{$decline_href}}" target="_blank">{{$decline_link_text}}</a>',
        ],
    ],
];
