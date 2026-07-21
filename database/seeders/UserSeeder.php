<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@karyatama.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::ADMIN,
                'phone' => '081234567890',
            ],
            [
                'name' => 'Teknisi Utama',
                'username' => 'teknisi',
                'email' => 'teknisi@karyatama.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::TECHNICIAN,
                'phone' => '081234567891',
            ],
            [
                'name' => 'PT Magnum Indonesia',
                'username' => 'magnum',
                'email' => 'client@magnum.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '081234567892',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
