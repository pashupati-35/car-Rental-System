<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        $owner = Owner::first();

        $adminId = $admin ? $admin->id : 1;
        $ownerId = $owner ? $owner->id : 1;

        $customers = [
            ['name' => 'Aayush Adhikari', 'first_name' => 'Aayush', 'last_name' => 'Adhikari', 'email' => 'customer@gmail.com', 'phone' => '+977-9812345678', 'address' => 'Lalitpur, Nepal', 'gender' => 'male'],
            ['name' => 'Sandesh Karki', 'first_name' => 'Sandesh', 'last_name' => 'Karki', 'email' => 'sandesh.karki@gmail.com', 'phone' => '+977-9812345601', 'address' => 'Baluwatar, Kathmandu', 'gender' => 'male'],
            ['name' => 'Shristi Pradhan', 'first_name' => 'Shristi', 'last_name' => 'Pradhan', 'email' => 'shristi.pradhan@gmail.com', 'phone' => '+977-9812345602', 'address' => 'Jhamsikhel, Lalitpur', 'gender' => 'female'],
            ['name' => 'Pradeep Thapa', 'first_name' => 'Pradeep', 'last_name' => 'Thapa', 'email' => 'pradeep.thapa@gmail.com', 'phone' => '+977-9812345603', 'address' => 'Lakeside, Pokhara', 'gender' => 'male'],
            ['name' => 'Anusha Shrestha', 'first_name' => 'Anusha', 'last_name' => 'Shrestha', 'email' => 'anusha.shrestha@gmail.com', 'phone' => '+977-9812345604', 'address' => 'Suryabinayak, Bhaktapur', 'gender' => 'female'],
            ['name' => 'Kushal KC', 'first_name' => 'Kushal', 'last_name' => 'KC', 'email' => 'kushal.kc@gmail.com', 'phone' => '+977-9812345605', 'address' => 'Bharatpur, Chitwan', 'gender' => 'male'],
            ['name' => 'Sabina Tamang', 'first_name' => 'Sabina', 'last_name' => 'Tamang', 'email' => 'sabina.tamang@gmail.com', 'phone' => '+977-9812345606', 'address' => 'Boudha, Kathmandu', 'gender' => 'female'],
            ['name' => 'Bishal Gurung', 'first_name' => 'Bishal', 'last_name' => 'Gurung', 'email' => 'bishal.gurung@gmail.com', 'phone' => '+977-9812345607', 'address' => 'Pokhara-8, Kaski', 'gender' => 'male'],
            ['name' => 'Nirjala Rijal', 'first_name' => 'Nirjala', 'last_name' => 'Rijal', 'email' => 'nirjala.rijal@gmail.com', 'phone' => '+977-9812345608', 'address' => 'Kalanki, Kathmandu', 'gender' => 'female'],
            ['name' => 'Rupesh Yadav', 'first_name' => 'Rupesh', 'last_name' => 'Yadav', 'email' => 'rupesh.yadav@gmail.com', 'phone' => '+977-9812345609', 'address' => 'Janakpur, Dhanusha', 'gender' => 'male'],
            ['name' => 'Pooja Pandey', 'first_name' => 'Pooja', 'last_name' => 'Pandey', 'email' => 'pooja.pandey@gmail.com', 'phone' => '+977-9812345610', 'address' => 'Butwal, Rupandehi', 'gender' => 'female'],
            ['name' => 'Ashish Magar', 'first_name' => 'Ashish', 'last_name' => 'Magar', 'email' => 'ashish.magar@gmail.com', 'phone' => '+977-9812345611', 'address' => 'Dharan, Sunsari', 'gender' => 'male'],
            ['name' => 'Alina Maharjan', 'first_name' => 'Alina', 'last_name' => 'Maharjan', 'email' => 'alina.maharjan@gmail.com', 'phone' => '+977-9812345612', 'address' => 'Mangalbazar, Lalitpur', 'gender' => 'female'],
            ['name' => 'Subash Bista', 'first_name' => 'Subash', 'last_name' => 'Bista', 'email' => 'subash.bista@gmail.com', 'phone' => '+977-9812345613', 'address' => 'Dhangadhi, Kailali', 'gender' => 'male'],
            ['name' => 'Sweta Gautam', 'first_name' => 'Sweta', 'last_name' => 'Gautam', 'email' => 'sweta.gautam@gmail.com', 'phone' => '+977-9812345614', 'address' => 'Baneshwor, Kathmandu', 'gender' => 'female'],
            ['name' => 'Roshan Paudel', 'first_name' => 'Roshan', 'last_name' => 'Paudel', 'email' => 'roshan.paudel@gmail.com', 'phone' => '+977-9812345615', 'address' => 'Hetauda, Makwanpur', 'gender' => 'male'],
            ['name' => 'Manita Sunar', 'first_name' => 'Manita', 'last_name' => 'Sunar', 'email' => 'manita.sunar@gmail.com', 'phone' => '+977-9812345616', 'address' => 'Nepalgunj, Banke', 'gender' => 'female'],
            ['name' => 'Dipen Joshi', 'first_name' => 'Dipen', 'last_name' => 'Joshi', 'email' => 'dipen.joshi@gmail.com', 'phone' => '+977-9812345617', 'address' => 'Biratnagar, Morang', 'gender' => 'male'],
            ['name' => 'Prabina Rai', 'first_name' => 'Prabina', 'last_name' => 'Rai', 'email' => 'prabina.rai@gmail.com', 'phone' => '+977-9812345618', 'address' => 'Damak, Jhapa', 'gender' => 'female'],
            ['name' => 'Hemant Chaudhary', 'first_name' => 'Hemant', 'last_name' => 'Chaudhary', 'email' => 'hemant.chaudhary@gmail.com', 'phone' => '+977-9812345619', 'address' => 'Birgunj, Parsa', 'gender' => 'male'],
        ];

        foreach ($customers as $index => $c) {
            Customer::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'first_name' => $c['first_name'],
                    'last_name' => $c['last_name'],
                    'phone_number' => $c['phone'],
                    'mobile' => $c['phone'],
                    'address' => $c['address'],
                    'gender' => $c['gender'],
                    'password' => Hash::make('password'),
                    'admin_id' => $adminId,
                    'owner_id' => $ownerId,
                    'unique_identifier' => 'CUS-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'user_type' => 'customer',
                    'approval_status' => 'approved',
                    'is_submitted' => true,
                    'has_email_access' => true,
                    'theme_style' => 'light',
                ]
            );
        }
    }
}
