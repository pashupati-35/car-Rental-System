<?php

namespace Database\Seeders;

use App\Models\EmailTemplate\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emailTemplates = Config::get('email-template.roles');
        if (count($emailTemplates) > 0) {
            foreach ($emailTemplates as $r => $types) {
                if (! empty($types) && is_array($types)) {
                    foreach ($types as $t => $type) {
                        if (! empty($type) && is_array($type)) {
                            $type['role'] = $r;
                            $type['type'] = $t;
                            $type['accepted_inputs'] = is_array($type['accepted_inputs'] ?? null) ? implode(',', $type['accepted_inputs']) : ($type['accepted_inputs'] ?? '');
                            $type['message_data'] = is_array($type['message_data'] ?? null) ? implode(',', $type['message_data']) : ($type['message_data'] ?? '');
                            $emailTemplate = EmailTemplate::where('role', $r)->where('type', $t)->where('identifier', $type['identifier'])->first();
                            if (empty($emailTemplate)) {
                                EmailTemplate::create($type);
                            } else {
                                $emailTemplate->update($type);
                            }
                        }
                    }
                }
            }
        }

        // Ensure at least 20 templates exist
        $extraTemplates = [
            ['title' => 'Driver Assigned Notification', 'identifier' => 'driver-assigned-notify', 'role' => 'customer', 'type' => 'booking', 'subject' => 'Your Driver Has Been Assigned for Booking #{{$booking_id}}'],
            ['title' => 'Vehicle Service Due Alert', 'identifier' => 'vehicle-service-alert', 'role' => 'owner', 'type' => 'fleet', 'subject' => 'Maintenance Service Reminder for {{$car_name}}'],
            ['title' => 'Payment Receipt Confirmation', 'identifier' => 'payment-receipt-notify', 'role' => 'customer', 'type' => 'billing', 'subject' => 'Payment Receipt for Rental Booking #{{$booking_id}}'],
            ['title' => 'Trip Completion & Feedback', 'identifier' => 'trip-completed-feedback', 'role' => 'customer', 'type' => 'feedback', 'subject' => 'How Was Your Rental Trip with {{$car_name}}?'],
            ['title' => 'Owner Monthly Earnings Statement', 'identifier' => 'owner-monthly-payout', 'role' => 'owner', 'type' => 'payout', 'subject' => 'Monthly Fleet Earnings Statement - {{$month}}'],
            ['title' => 'Emergency Roadside Dispatch Alert', 'identifier' => 'roadside-dispatch-alert', 'role' => 'admin', 'type' => 'emergency', 'subject' => 'Emergency Assistance Dispatched for Vehicle {{$car_number}}'],
            ['title' => 'Driver License Expiry Warning', 'identifier' => 'driver-license-warning', 'role' => 'owner', 'type' => 'compliance', 'subject' => 'Driver License Renewal Required for {{$driver_name}}'],
            ['title' => 'Seasonal Special Promotion', 'identifier' => 'seasonal-special-promo', 'role' => 'customer', 'type' => 'marketing', 'subject' => 'Exclusive 20% Off Your Next Road Trip Adventure!'],
            ['title' => 'Booking Extension Confirmation', 'identifier' => 'booking-extension-confirm', 'role' => 'customer', 'type' => 'booking', 'subject' => 'Rental Booking Extension Approved for #{{$booking_id}}'],
            ['title' => 'Admin System Health Digest', 'identifier' => 'admin-system-health', 'role' => 'admin', 'type' => 'system', 'subject' => 'Daily Fleet Operations & Reservation Digest'],
        ];

        foreach ($extraTemplates as $extra) {
            $extra['is_active'] = 1;
            $extra['description'] = '<p>Notification regarding ' . $extra['title'] . ' for registered users.</p>';
            $extra['message_content'] = 'Notification: ' . $extra['title'] . '.';
            EmailTemplate::updateOrCreate(['identifier' => $extra['identifier']], $extra);
        }
    }
}
