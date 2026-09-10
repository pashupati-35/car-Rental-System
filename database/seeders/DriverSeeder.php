<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Owner;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = Owner::all();
        $firstOwnerId = $owners->first() ? $owners->first()->id : 1;

        $driverData = [
            ['name' => 'Bikram Thapa', 'phone' => '+977-9801122334', 'email' => 'bikram.driver@gmail.com', 'license' => 'DL-01-98762', 'exp' => '8', 'status' => 'active'],
            ['name' => 'Ramesh Chaudhary', 'phone' => '+977-9809988776', 'email' => 'ramesh.driver@gmail.com', 'license' => 'DL-02-54321', 'exp' => '5', 'status' => 'active'],
            ['name' => 'Santosh Shrestha', 'phone' => '+977-9801122001', 'email' => 'santosh.driver@gmail.com', 'license' => 'DL-03-10293', 'exp' => '10', 'status' => 'active'],
            ['name' => 'Dilip Rai', 'phone' => '+977-9801122002', 'email' => 'dilip.driver@gmail.com', 'license' => 'DL-04-49201', 'exp' => '7', 'status' => 'active'],
            ['name' => 'Khem Bahadur Gurung', 'phone' => '+977-9801122003', 'email' => 'khem.driver@gmail.com', 'license' => 'DL-05-39201', 'exp' => '12', 'status' => 'active'],
            ['name' => 'Prem Prakash KC', 'phone' => '+977-9801122004', 'email' => 'prem.driver@gmail.com', 'license' => 'DL-06-88201', 'exp' => '4', 'status' => 'active'],
            ['name' => 'Ganesh Magar', 'phone' => '+977-9801122005', 'email' => 'ganesh.driver@gmail.com', 'license' => 'DL-07-77201', 'exp' => '6', 'status' => 'active'],
            ['name' => 'Suraj Tamang', 'phone' => '+977-9801122006', 'email' => 'suraj.driver@gmail.com', 'license' => 'DL-08-66201', 'exp' => '9', 'status' => 'active'],
            ['name' => 'Bijay Nepali', 'phone' => '+977-9801122007', 'email' => 'bijay.driver@gmail.com', 'license' => 'DL-09-55201', 'exp' => '3', 'status' => 'active'],
            ['name' => 'Kamal Adhikari', 'phone' => '+977-9801122008', 'email' => 'kamal.driver@gmail.com', 'license' => 'DL-10-44201', 'exp' => '11', 'status' => 'active'],
            ['name' => 'Nirajan Basnet', 'phone' => '+977-9801122009', 'email' => 'nirajan.driver@gmail.com', 'license' => 'DL-11-33201', 'exp' => '5', 'status' => 'active'],
            ['name' => 'Roshan Lama', 'phone' => '+977-9801122010', 'email' => 'roshan.driver@gmail.com', 'license' => 'DL-12-22201', 'exp' => '7', 'status' => 'active'],
            ['name' => 'Dipendra Shah', 'phone' => '+977-9801122011', 'email' => 'dipendra.driver@gmail.com', 'license' => 'DL-13-11201', 'exp' => '8', 'status' => 'active'],
            ['name' => 'Rajendra Mahato', 'phone' => '+977-9801122012', 'email' => 'rajendra.driver@gmail.com', 'license' => 'DL-14-99101', 'exp' => '6', 'status' => 'active'],
            ['name' => 'Suman Pokharel', 'phone' => '+977-9801122013', 'email' => 'suman.driver@gmail.com', 'license' => 'DL-15-88101', 'exp' => '10', 'status' => 'active'],
            ['name' => 'Balaram Karki', 'phone' => '+977-9801122014', 'email' => 'balaram.driver@gmail.com', 'license' => 'DL-16-77101', 'exp' => '14', 'status' => 'active'],
            ['name' => 'Anup Rijal', 'phone' => '+977-9801122015', 'email' => 'anup.driver@gmail.com', 'license' => 'DL-17-66101', 'exp' => '4', 'status' => 'active'],
            ['name' => 'Tek Bahadur Budha', 'phone' => '+977-9801122016', 'email' => 'tek.driver@gmail.com', 'license' => 'DL-18-55101', 'exp' => '9', 'status' => 'active'],
            ['name' => 'Bishnu Dhakal', 'phone' => '+977-9801122017', 'email' => 'bishnu.driver@gmail.com', 'license' => 'DL-19-44101', 'exp' => '6', 'status' => 'active'],
            ['name' => 'Arun Sunar', 'phone' => '+977-9801122018', 'email' => 'arun.driver@gmail.com', 'license' => 'DL-20-33101', 'exp' => '5', 'status' => 'active'],
        ];

        foreach ($driverData as $index => $item) {
            $ownerId = isset($owners[$index % count($owners)]) ? $owners[$index % count($owners)]->id : $firstOwnerId;

            Driver::updateOrCreate(
                ['license_number' => $item['license']],
                [
                    'owner_id' => $ownerId,
                    'name' => $item['name'],
                    'phone' => $item['phone'],
                    'email' => $item['email'],
                    'experience_years' => $item['exp'],
                    'status' => $item['status'],
                ]
            );
        }
    }
}
