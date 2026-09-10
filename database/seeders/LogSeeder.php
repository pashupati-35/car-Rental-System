<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AiChat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Email Logs (20 records)
        $emailLogs = [];
        $emailSubjects = [
            'Welcome to Fleet Master Platform',
            'Your Car Rental Booking #1001 Confirmation',
            'New Owner Account Approval Notification',
            'Vehicle Inspection & Verification Passed',
            'Driver Verification Complete',
            'Monthly Rental Fleet Earnings Statement',
            'Password Reset Requested',
            'Payment Receipt for Booking #1002',
            'Security Alert: New Sign-in Detected',
            'Booking Cancellation Confirmation',
            'Car Service Schedule Reminder',
            'Customer Satisfaction Feedback Survey',
            'Driver Assigned for Booking #1005',
            'Refund Processed for Booking #1006',
            'Special Festive Discount 20% Off',
            'Updated Terms of Rental Agreement',
            'GPS Tracking Alert: Trip Started',
            'Vehicle Return Acknowledgement',
            'Booking Extended Successfully',
            'Admin System Backup Completed',
        ];

        for ($i = 0; $i < 20; $i++) {
            $emailLogs[] = [
                'from' => 'noreply@carrental.com',
                'to' => 'client' . ($i + 1) . '@example.com',
                'subject' => $emailSubjects[$i],
                'body' => '<p>Dear Customer / Partner,</p><p>This is an automated notification regarding: ' . $emailSubjects[$i] . '.</p><p>Thank you for choosing our fleet.</p>',
                'status' => 'sent',
                'transport' => 'smtp',
                'ip_address' => '127.0.0.1',
                'sent_at' => Carbon::now()->subHours($i * 3),
                'created_at' => Carbon::now()->subHours($i * 3),
                'updated_at' => Carbon::now()->subHours($i * 3),
            ];
        }
        DB::table('email_logs')->insert($emailLogs);

        // 2. Activity Logs (20 records)
        $activityLogs = [];
        $actions = [
            ['type' => 'login', 'desc' => 'Admin logged into portal from Web Console', 'table' => 'admins'],
            ['type' => 'create', 'desc' => 'New luxury car registered (BA-1-PA-1024)', 'table' => 'cars'],
            ['type' => 'update', 'desc' => 'Driver Bikram Thapa status changed to active', 'table' => 'drivers'],
            ['type' => 'create', 'desc' => 'Booking #1001 created by customer Aayush', 'table' => 'booking_car'],
            ['type' => 'update', 'desc' => 'Booking #1001 status changed to confirmed', 'table' => 'booking_car'],
            ['type' => 'create', 'desc' => 'Payment of $480.00 processed via card', 'table' => 'payments'],
            ['type' => 'create', 'desc' => 'New owner Rajesh Sharma registered', 'table' => 'owners'],
            ['type' => 'update', 'desc' => 'Owner profile approved by Super Admin', 'table' => 'owners'],
            ['type' => 'update', 'desc' => 'Site settings contact info updated', 'table' => 'site_settings'],
            ['type' => 'create', 'desc' => 'New FAQ added under Rental Guidelines', 'table' => 'faqs'],
            ['type' => 'create', 'desc' => 'Blog article published: Top 10 Road Trips in Nepal', 'table' => 'blogs'],
            ['type' => 'update', 'desc' => 'Car daily price adjusted for peak season', 'table' => 'cars'],
            ['type' => 'export', 'desc' => 'Monthly booking revenue report exported to Excel', 'table' => 'payments'],
            ['type' => 'update', 'desc' => 'Email template customer_booking_confirm modified', 'table' => 'email_templates'],
            ['type' => 'create', 'desc' => 'Career posting published: Fleet Support Officer', 'table' => 'careers'],
            ['type' => 'create', 'desc' => 'Customer enquiry received regarding Airport Transfer', 'table' => 'enquiries'],
            ['type' => 'update', 'desc' => 'Driver license verified and cleared', 'table' => 'drivers'],
            ['type' => 'update', 'desc' => 'Emergency popup activated on homepage', 'table' => 'popups'],
            ['type' => 'update', 'desc' => 'Car calendar schedule updated for Toyota Hilux', 'table' => 'car_calendar'],
            ['type' => 'logout', 'desc' => 'Admin session closed securely', 'table' => 'admins'],
        ];

        for ($i = 0; $i < 20; $i++) {
            $activityLogs[] = [
                'log_type' => $actions[$i]['type'],
                'description' => $actions[$i]['desc'],
                'table_name' => $actions[$i]['table'],
                'causer_type' => 'App\\Models\\Admin',
                'causer_id' => $adminId,
                'ip_address' => '192.168.1.' . (10 + $i),
                'properties' => json_encode(['source' => 'system_seeder', 'event_id' => $i + 1]),
                'created_at' => Carbon::now()->subHours($i * 2),
                'updated_at' => Carbon::now()->subHours($i * 2),
            ];
        }
        DB::table('activity_logs')->insert($activityLogs);

        // 3. System Logs (20 records)
        $logs = [];
        for ($i = 0; $i < 20; $i++) {
            $logs[] = [
                'title' => 'System Audit Log #' . ($i + 1) . ' - ' . $actions[$i]['desc'],
                'user_id' => $adminId,
                'admin_user_id' => $adminId,
                'log_date' => Carbon::now()->subHours($i * 4),
                'table_name' => $actions[$i]['table'],
                'table_id' => $i + 1,
                'log_type' => $actions[$i]['type'],
                'data' => json_encode(['status' => 'success', 'code' => 200, 'module' => $actions[$i]['table']]),
                'before_data' => json_encode(['status' => 'pending']),
                'after_data' => json_encode(['status' => 'processed']),
                'created_at' => Carbon::now()->subHours($i * 4),
                'updated_at' => Carbon::now()->subHours($i * 4),
            ];
        }
        DB::table('logs')->insert($logs);

        // 4. AI Chats (20 records)
        $aiPrompts = [
            'What are the best SUV options for a 4-day mountain trip to Pokhara?',
            'How can I book a self-drive car with insurance included?',
            'What documents are required for international tourists to rent a car?',
            'Can I hire a professional driver with the Toyota Fortuner?',
            'What is the cancellation and refund policy for car bookings?',
            'How are rental prices calculated per kilometer vs per day?',
            'Are fuel charges included in the daily rental rate?',
            'Do you provide 24/7 roadside assistance in case of a breakdown?',
            'Can I pick up the vehicle at Kathmandu Airport and drop it in Pokhara?',
            'What is the security deposit amount for luxury vehicles?',
            'How do I register as a fleet car owner to list my vehicles?',
            'What maintenance requirements must owner cars meet?',
            'Can I extend my ongoing rental booking through the mobile app?',
            'Are electric vehicles (EVs) available with charging station guides?',
            'What precautions should I take while driving on high-altitude mountain roads?',
            'Do you offer special corporate packages for long-term vehicle leasing?',
            'How quickly can a replacement car be arranged in emergency situations?',
            'What payment methods are supported on the checkout portal?',
            'Can I request child safety seats and rooftop luggage carriers?',
            'How does the driver rating and verification system work?',
        ];

        for ($i = 0; $i < 20; $i++) {
            AiChat::create([
                'user_id' => $adminId,
                'user_name' => 'Demo Customer / Admin',
                'session_id' => 'session_ai_' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'prompt' => $aiPrompts[$i],
                'response' => 'Based on your inquiry regarding "' . $aiPrompts[$i] . '", our fleet provides verified vehicles, certified drivers, and instant booking with 24/7 customer assistance.',
            ]);
        }
    }
}
