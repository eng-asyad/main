<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the $admins array with admin user data
        $admins = [
            [
                'name' => 'Admin1',
                'email' => 'admin1@gmail.com',
                'mobile' => '0911111111',
                'password' => Hash::make('1111111'),
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin2',
                'email' => 'admin2@gmail.com',
                'mobile' => '0922222222',
                'password' => Hash::make('2222222'),
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin3',
                'email' => 'admin3@gmail.com',
                'mobile' => '0933333333',
                'password' => Hash::make('3333333'),
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],        ];

        // Insert each admin user data into the users table
        foreach ($admins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']], // Conditions for finding the record
                $adminData // Data to use for creating the record if it doesn't exist
            );  
         }
    }
}
