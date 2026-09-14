<?php

return [
    'roles' => [
        'owner' => [
            'welcome_email' => [
                'title' => 'Fleet Owner account created',
                'identifier' => 'welcome-email-owner',
                'subject' => 'Welcome to AutoRent Fleet Partner Program',
                'role' => 'owner',
                'type' => 'onboarding',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Welcome to AutoRent! Your fleet partner owner account has been created successfully.</p>
<p><strong>Account email:</strong> {{$email}}</p>
<p>For security, please set your password using the secure link below before signing in:</p>
<p><a href="{{$reset_link}}" target="_blank">Set your owner password</a></p>
<p>Once set, you can list and verify your vehicles, assign drivers, and monitor rental reservations in your dedicated Owner Portal.</p>',
                'message_content' => 'Your AutoRent fleet owner account is ready. Set your password using the secure link: {{$reset_link}}',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                    'reset_link',
                    'contact_number',
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

            'car_approved' => [
                'title' => 'Car Listing Approved & Live',
                'identifier' => 'car-verified-notification',
                'subject' => 'Your vehicle listing for {{$car_name}} is now approved and live',
                'role' => 'owner',
                'type' => 'fleet',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Great news! Your vehicle <strong>{{$car_name}}</strong> (Plate: {{$car_number}}) has passed administration inspection and is now <strong>VERIFIED</strong> on the public fleet showroom.</p>
<p>Daily Rental Rate: <strong>${{$daily_price}}/day</strong></p>
<p>Customers can now reserve your vehicle directly.</p>',
                'message_content' => 'Your vehicle {{$car_name}} (Plate: {{$car_number}}) has been approved and listed on AutoRent showroom.',
                'accepted_inputs' => [
                    'first_name',
                    'car_name',
                    'car_number',
                    'daily_price',
                    'portal_link',
                ],
                'message_data' => [],
                'info_message' => 'Vehicle is now available for public bookings.',
                'alert_message' => null,
                'cta_url' => '{{$portal_link}}',
                'cta_text' => 'View Vehicle Status',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'car_rejected' => [
                'title' => 'Car Listing Review Decision',
                'identifier' => 'car-rejected-notification',
                'subject' => 'Update on your vehicle listing for {{$car_name}}',
                'role' => 'owner',
                'type' => 'fleet',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Thank you for submitting your vehicle <strong>{{$car_name}}</strong> (Plate: {{$car_number}}).</p>
<p>During administrative inspection, we found items that need revision before listing:</p>
<p><strong>Reason:</strong> {{$reasons}}</p>
<p>Please update your vehicle documents or photo records in your Owner Portal.</p>',
                'message_content' => 'Your vehicle listing for {{$car_name}} requires revision. Reason: {{$reasons}}',
                'accepted_inputs' => [
                    'first_name',
                    'car_name',
                    'car_number',
                    'reasons',
                    'support_link',
                ],
                'message_data' => [],
                'info_message' => 'Review the feedback and re-submit your vehicle record.',
                'alert_message' => null,
                'cta_url' => '{{$support_link}}',
                'cta_text' => 'Contact Support',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'new_booking_notification' => [
                'title' => 'New Booking on Your Vehicle',
                'identifier' => 'new-booking-owner',
                'subject' => 'New rental reservation received for {{$car_name}}',
                'role' => 'owner',
                'type' => 'booking',
                'description' => '<p>Hello {{$first_name}},</p>
<p>A new customer booking has been scheduled for your vehicle <strong>{{$car_name}}</strong>.</p>
<ul>
    <li><strong>Booking ID:</strong> #{{$booking_id}}</li>
    <li><strong>Rental Period:</strong> {{$start_date}} to {{$end_date}}</li>
    <li><strong>Gross Value:</strong> ${{$total_amount}}</li>
</ul>
<p>Please check your Owner Portal to confirm driver dispatch and pickup readiness.</p>',
                'message_content' => 'New booking #{{$booking_id}} for {{$car_name}} from {{$start_date}} to {{$end_date}}.',
                'accepted_inputs' => [
                    'first_name',
                    'car_name',
                    'booking_id',
                    'start_date',
                    'end_date',
                    'total_amount',
                ],
                'message_data' => [],
                'info_message' => 'Ensure vehicle is sanitized and fueled prior to rental dispatch.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'driver_assigned' => [
                'title' => 'Driver Assignment Notification',
                'identifier' => 'driver-assigned-owner',
                'subject' => 'Driver {{$driver_name}} assigned to your fleet vehicle',
                'role' => 'owner',
                'type' => 'fleet',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Driver <strong>{{$driver_name}}</strong> (License: {{$license_number}}) has been assigned to your vehicle <strong>{{$car_name}}</strong>.</p>',
                'message_content' => 'Driver {{$driver_name}} assigned to {{$car_name}}.',
                'accepted_inputs' => [
                    'first_name',
                    'driver_name',
                    'car_name',
                    'license_number',
                ],
                'message_data' => [],
                'info_message' => 'Driver credentials verified by administration.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'password_reset_email' => [
                'title' => 'Fleet Owner Password Reset',
                'identifier' => 'account-password-reset-owner',
                'subject' => 'Reset your AutoRent Owner password',
                'role' => 'owner',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>We received a password reset request for your AutoRent Fleet Owner account ({{$email}}).</p>
<p><a href="{{$reset_link}}" target="_blank">Click here to reset your password</a></p>
<p>If you did not request this, please disregard this email.</p>',
                'message_content' => 'Reset your AutoRent owner password using: {{$reset_link}}',
                'accepted_inputs' => [
                    'first_name',
                    'reset_link',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'This link expires in 60 minutes.',
                'alert_message' => 'Do not share your password reset link.',
                'cta_url' => '{{$reset_link}}',
                'cta_text' => 'Reset Password',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'verification_code_email' => [
                'title' => 'Fleet Owner Sign-in Verification',
                'identifier' => 'verify-identity-owner',
                'subject' => 'Your AutoRent sign-in security code',
                'role' => 'owner',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your sign-in security verification code is:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>
<p>Do not share this code with anyone.</p>',
                'message_content' => 'Your AutoRent owner security code is {{$verification_code}}.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'Enter this code on the login verification screen.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'mfa_verification_email' => [
                'title' => 'Fleet Owner Email Verification',
                'identifier' => 'account-verify-email-owner',
                'subject' => 'Verify your email address for AutoRent Owner account',
                'role' => 'owner',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Please verify your email address to activate all owner management features.</p>
<p>Verification Code: <strong>{{$verification_code}}</strong></p>',
                'message_content' => 'Your AutoRent email verification code is {{$verification_code}}.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'Use this code to verify your owner email.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'payout_statement' => [
                'title' => 'Monthly Fleet Payout Statement',
                'identifier' => 'owner-payout-statement',
                'subject' => 'Your AutoRent monthly rental revenue statement',
                'role' => 'owner',
                'type' => 'payout',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your fleet earnings summary for <strong>{{$month}}</strong> is now available.</p>
<ul>
    <li><strong>Total Gross Earnings:</strong> ${{$total_earnings}}</li>
    <li><strong>Vehicles Rented:</strong> {{$cars_rented_count}}</li>
</ul>
<p>Payouts are processed directly to your registered bank account.</p>',
                'message_content' => 'Your revenue summary for {{$month}} is ${{$total_earnings}} across {{$cars_rented_count}} vehicles.',
                'accepted_inputs' => [
                    'first_name',
                    'month',
                    'total_earnings',
                    'cars_rented_count',
                    'payout_link',
                ],
                'message_data' => [],
                'info_message' => 'Statements are generated on the 1st of every month.',
                'alert_message' => null,
                'cta_url' => '{{$payout_link}}',
                'cta_text' => 'View Payout Statement',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],
        ],

        'customer' => [
            'welcome_email' => [
                'title' => 'Customer Account Welcome',
                'identifier' => 'welcome-email-customer',
                'subject' => 'Welcome to AutoRent — Start your journey',
                'role' => 'customer',
                'type' => 'onboarding',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Welcome to AutoRent! Your customer account has been registered successfully.</p>
<p>Explore our premium fleet of SUVs, sedans, and luxury vehicles at competitive daily rates.</p>
<p><a href="{{$reset_link}}" target="_blank">Complete your password setup</a></p>',
                'message_content' => 'Welcome to AutoRent! Set your password and start browsing: {{$reset_link}}',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                    'reset_link',
                ],
                'message_data' => [],
                'info_message' => 'Complete your profile to unlock instant booking verification.',
                'alert_message' => null,
                'cta_url' => '{{$reset_link}}',
                'cta_text' => 'Set Password',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'booking_confirmed' => [
                'title' => 'Rental Booking Confirmed',
                'identifier' => 'booking-confirm-notification',
                'subject' => 'Booking Confirmation #{{$booking_id}} for {{$car_name}}',
                'role' => 'customer',
                'type' => 'booking',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your rental booking #<strong>{{$booking_id}}</strong> for <strong>{{$car_name}}</strong> has been <strong>CONFIRMED</strong>.</p>
<ul>
    <li><strong>Pickup Date:</strong> {{$start_date}}</li>
    <li><strong>Return Date:</strong> {{$end_date}}</li>
    <li><strong>Pickup Location:</strong> {{$pickup_location}}</li>
    <li><strong>Total Cost:</strong> ${{$total_amount}}</li>
</ul>
<p>Please present a valid driver\'s license upon vehicle pickup.</p>',
                'message_content' => 'Booking #{{$booking_id}} for {{$car_name}} is confirmed for {{$start_date}} to {{$end_date}}.',
                'accepted_inputs' => [
                    'first_name',
                    'booking_id',
                    'car_name',
                    'start_date',
                    'end_date',
                    'pickup_location',
                    'total_amount',
                ],
                'message_data' => [],
                'info_message' => 'Bring your physical driving license during vehicle handover.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'booking_cancelled' => [
                'title' => 'Rental Booking Cancelled',
                'identifier' => 'booking-cancel-notification',
                'subject' => 'Booking Cancellation Notice #{{$booking_id}}',
                'role' => 'customer',
                'type' => 'booking',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your booking #<strong>{{$booking_id}}</strong> for <strong>{{$car_name}}</strong> has been cancelled.</p>
<p><strong>Reason:</strong> {{$cancellation_reason}}</p>
<p>If you made a payment, refunds are automatically processed within 3-5 business days.</p>',
                'message_content' => 'Booking #{{$booking_id}} for {{$car_name}} has been cancelled. Reason: {{$cancellation_reason}}',
                'accepted_inputs' => [
                    'first_name',
                    'booking_id',
                    'car_name',
                    'cancellation_reason',
                    'support_link',
                ],
                'message_data' => [],
                'info_message' => 'You can book another vehicle from our fleet anytime.',
                'alert_message' => null,
                'cta_url' => '{{$support_link}}',
                'cta_text' => 'Support Desk',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'password_reset_email' => [
                'title' => 'Customer Password Reset',
                'identifier' => 'account-password-reset-customer',
                'subject' => 'Reset your AutoRent Customer password',
                'role' => 'customer',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>We received a password reset request for your AutoRent Customer account ({{$email}}).</p>
<p><a href="{{$reset_link}}" target="_blank">Click here to reset your password</a></p>',
                'message_content' => 'Reset your customer password: {{$reset_link}}',
                'accepted_inputs' => [
                    'first_name',
                    'reset_link',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'This link expires in 60 minutes.',
                'alert_message' => null,
                'cta_url' => '{{$reset_link}}',
                'cta_text' => 'Reset Password',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'verification_code_email' => [
                'title' => 'Customer Sign-in Verification',
                'identifier' => 'verify-identity-customer',
                'subject' => 'Your AutoRent login verification code',
                'role' => 'customer',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your login security code is:</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>',
                'message_content' => 'Your AutoRent customer login code is {{$verification_code}}.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'Enter this code to verify your sign-in.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'mfa_verification_email' => [
                'title' => 'Customer Email Verification',
                'identifier' => 'account-verify-email-customer',
                'subject' => 'Verify your email address for AutoRent',
                'role' => 'customer',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your email verification code is: <strong>{{$verification_code}}</strong></p>',
                'message_content' => 'Your AutoRent email verification code is {{$verification_code}}.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'Enter this code on the activation screen.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'rental_invoice' => [
                'title' => 'Rental Invoice & Payment Receipt',
                'identifier' => 'rental-invoice-customer',
                'subject' => 'Payment Receipt for Booking #{{$booking_id}}',
                'role' => 'customer',
                'type' => 'billing',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Thank you for your payment of <strong>${{$amount_paid}}</strong> for booking #{{$booking_id}} ({{$car_name}}).</p>
<p>Payment Method: {{$payment_method}}</p>',
                'message_content' => 'Receipt for Booking #{{$booking_id}}: ${{$amount_paid}} paid successfully.',
                'accepted_inputs' => [
                    'first_name',
                    'booking_id',
                    'car_name',
                    'amount_paid',
                    'payment_method',
                    'invoice_url',
                ],
                'message_data' => [],
                'info_message' => 'Tax invoice attached or available online.',
                'alert_message' => null,
                'cta_url' => '{{$invoice_url}}',
                'cta_text' => 'View Full Invoice',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'rental_review' => [
                'title' => 'Rental Trip Completed & Feedback',
                'identifier' => 'rental-completed-review',
                'subject' => 'How was your ride with {{$car_name}}? Leave a review',
                'role' => 'customer',
                'type' => 'engagement',
                'description' => '<p>Hello {{$first_name}},</p>
<p>We hope you had a fantastic journey with <strong>{{$car_name}}</strong>!</p>
<p>Please share your rating and experience to help fellow renters.</p>',
                'message_content' => 'Rate your recent trip with {{$car_name}} on AutoRent: {{$review_link}}',
                'accepted_inputs' => [
                    'first_name',
                    'booking_id',
                    'car_name',
                    'review_link',
                ],
                'message_data' => [],
                'info_message' => 'Your review helps us keep the highest fleet standards.',
                'alert_message' => null,
                'cta_url' => '{{$review_link}}',
                'cta_text' => 'Rate Your Trip',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],
        ],

        'admin' => [
            'email_verification_code' => [
                'title' => 'Administrator email verification',
                'identifier' => 'account-verify-email-admin',
                'subject' => 'Verify your email address for AutoRent Super Admin',
                'role' => 'admin',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your administrator account requires email verification before activation.</p>
<div class="verification-code-wrapper">
    <span class="verification-code">{{$verification_code}}</span>
</div>',
                'message_content' => 'Your AutoRent administrator verification code is {{$verification_code}}.',
                'accepted_inputs' => [
                    'first_name',
                    'verification_code',
                ],
                'message_data' => [],
                'info_message' => 'Use this code on the administrator verification screen.',
                'alert_message' => 'Do not share administrator verification codes.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'welcome_email' => [
                'title' => 'Administrator account created',
                'identifier' => 'welcome-email-admin',
                'subject' => 'Your AutoRent administrator account is ready',
                'role' => 'admin',
                'type' => 'onboarding',
                'description' => '<p>Hello {{$first_name}},</p>
<p>Your administrator account for AutoRent has been created successfully.</p>
<ul>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Temporary Password:</strong> {{$password}}</li>
</ul>
<p>Please change your temporary password immediately after signing in.</p>',
                'message_content' => 'Your administrator account has been created. Sign in with your temporary password.',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                    'password',
                ],
                'message_data' => [],
                'info_message' => 'Change password immediately after first login.',
                'alert_message' => 'Never share administrative credentials.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'password_changed' => [
                'title' => 'Administrator password changed',
                'identifier' => 'password_changed_admin',
                'subject' => 'Your AutoRent administrator password has been changed',
                'role' => 'admin',
                'type' => 'security',
                'description' => '<p>Hello {{$first_name}},</p>
<p>The password for your administrator account ({{$email}}) was updated successfully.</p>
<p>If you did not make this change, contact system security immediately.</p>',
                'message_content' => 'The password for your AutoRent administrator account was changed.',
                'accepted_inputs' => [
                    'first_name',
                    'email',
                ],
                'message_data' => [],
                'info_message' => 'No action is required if you performed this change.',
                'alert_message' => 'Contact security immediately if unauthorized.',
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'new_car_submitted' => [
                'title' => 'New Car Listing Submitted',
                'identifier' => 'new-car-submission-admin',
                'subject' => 'Action Required: New vehicle listing submitted by {{$owner_name}}',
                'role' => 'admin',
                'type' => 'fleet',
                'description' => '<p>Hello Admin,</p>
<p>Fleet owner <strong>{{$owner_name}}</strong> has submitted a new vehicle for audit and verification.</p>
<ul>
    <li><strong>Vehicle:</strong> {{$car_name}}</li>
    <li><strong>Plate Number:</strong> {{$plate_number}}</li>
    <li><strong>Submitted At:</strong> {{$submitted_at}}</li>
</ul>
<p>Please inspect bluebook records and approve or reject the listing.</p>',
                'message_content' => 'New car {{$car_name}} ({{$plate_number}}) submitted by {{$owner_name}} awaits audit.',
                'accepted_inputs' => [
                    'owner_name',
                    'car_name',
                    'plate_number',
                    'submitted_at',
                    'admin_review_link',
                ],
                'message_data' => [],
                'info_message' => 'Review vehicle specifications and documents before approving.',
                'alert_message' => 'Awaiting administrative verification.',
                'cta_url' => '{{$admin_review_link}}',
                'cta_text' => 'Review Vehicle',
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],

            'new_booking_received' => [
                'title' => 'New Booking Reservation Received',
                'identifier' => 'new-booking-received-admin',
                'subject' => 'New rental booking #{{$booking_id}} received',
                'role' => 'admin',
                'type' => 'booking',
                'description' => '<p>Hello Admin,</p>
<p>A new customer booking #<strong>{{$booking_id}}</strong> has been submitted by <strong>{{$customer_name}}</strong> for vehicle <strong>{{$car_name}}</strong>.</p>
<p>Total Amount: <strong>${{$total_amount}}</strong></p>',
                'message_content' => 'New booking #{{$booking_id}} received from {{$customer_name}} for ${{$total_amount}}.',
                'accepted_inputs' => [
                    'customer_name',
                    'booking_id',
                    'car_name',
                    'total_amount',
                    'submitted_at',
                ],
                'message_data' => [],
                'info_message' => 'Verify driver schedule and security deposit.',
                'alert_message' => null,
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
<p>A new enquiry has been submitted through the AutoRent contact form.</p>
<ul>
    <li><strong>Name:</strong> {{$first_name}} {{$last_name}}</li>
    <li><strong>Email:</strong> {{$email}}</li>
    <li><strong>Phone:</strong> {{$phone}}</li>
    <li><strong>Subject:</strong> {{$subject}}</li>
</ul>
<p><strong>Message:</strong></p>
<p>{{$message}}</p>',
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
                'info_message' => 'Review enquiry details before responding.',
                'alert_message' => null,
                'cta_url' => null,
                'cta_text' => null,
                'secondary_cta_url' => null,
                'secondary_cta_text' => null,
                'is_active' => true,
            ],
        ],
    ],
];
