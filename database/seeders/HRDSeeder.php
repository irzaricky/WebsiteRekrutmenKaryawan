<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\HRDDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HRDSeeder extends Seeder
{
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
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            HRDDetail::create([
                'user_id' => $user->id,
                'nik' => '1212121212121212',
                'address' => null,
                'birth_date' => null,
            ]);
        }
    }
}