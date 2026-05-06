<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itDept = Department::firstOrCreate(['name' => 'IT Department', 'code' => 'IT']);
        $medDept = Department::firstOrCreate(['name' => 'Medical Services', 'code' => 'MED']);

        $dummyVendor = Vendor::firstOrCreate([
            'name' => 'PT. Medika Sejahtera',
            'email' => 'contact@medika.com',
            'address' => 'Surabaya, Indonesia'
        ]);

        $userData = [
            [
                'name' => 'Roni Setiawan',
                'email' => 'admin@procurement.com',
                'role' => 'Administrator',
                'dept' => $itDept->id
            ],
            [
                'name' => 'Staff IT',
                'email' => 'staff.it@procurement.com',
                'role' => 'Staff Department',
                'dept' => $itDept->id
            ],
            [
                'name' => 'Manager IT',
                'email' => 'manager.it@procurement.com',
                'role' => 'Manager',
                'dept' => $itDept->id
            ],
            [
                'name' => 'Procurement Officer',
                'email' => 'procurement@procurement.com',
                'role' => 'Procurement Officer',
                'dept' => null
            ],
            [
                'name' => 'Vendor Medika',
                'email' => 'vendor@medika.com',
                'role' => 'Vendor',
                'dept' => null
            ]
        ];

        foreach ($userData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'department_id' => $data['dept'],
                'email_verified_at' => now(),
            ]);

            $user->assignRole($data['role']);
        }
    }
}
