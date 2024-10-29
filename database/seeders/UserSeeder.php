<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'HRD User',
                'email' => 'hrd@example.com',
                'password' => Hash::make('password123'),
                'role' => 'HRD',
                'status' => 'active',
            ],
            [
                'name' => 'Candidate User',
                'email' => 'candidate@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Candidate',
                'status' => 'active',
            ],
            // Additional random users
            [
                'name' => 'Inactive Candidate',
                'email' => 'inactive.candidate@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Candidate',
                'status' => 'inactive',
            ],
            [
                'name' => 'Inactive HRD',
                'email' => 'inactive.hrd@example.com',
                'password' => Hash::make('password123'),
                'role' => 'HRD',
                'status' => 'inactive',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
