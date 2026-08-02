<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = [
                [
                    'name' => 'Administrator',
                    'username' => 'admin',
                    'email' => 'admin@karyatama.test',
                    'phone' => '081234560001',
                    'role' => UserRole::ADMIN->value,
                    'is_active' => true,
                ],
                [
                    'name' => 'Budi Santoso',
                    'username' => 'teknisi.budi',
                    'email' => 'budi@karyatama.test',
                    'phone' => '081234560002',
                    'role' => UserRole::TECHNICIAN->value,
                    'is_active' => true,
                ],
                [
                    'name' => 'Andi Pratama',
                    'username' => 'teknisi.andi',
                    'email' => 'andi@karyatama.test',
                    'phone' => '081234560003',
                    'role' => UserRole::TECHNICIAN->value,
                    'is_active' => true,
                ],
                [
                    'name' => 'Rizky Maulana',
                    'username' => 'teknisi.rizky',
                    'email' => 'rizky@karyatama.test',
                    'phone' => '081234560004',
                    'role' => UserRole::TECHNICIAN->value,
                    'is_active' => true,
                ],
                [
                    'name' => 'Dedi Setiawan',
                    'username' => 'teknisi.dedi',
                    'email' => 'dedi@karyatama.test',
                    'phone' => '081234560005',
                    'role' => UserRole::TECHNICIAN->value,
                    'is_active' => false,
                ],
            ];

            foreach ($users as $user) {
                User::query()->updateOrCreate(
                    [
                        'username' => $user['username'],
                    ],
                    [
                        ...$user,
                        'password' => Hash::make('password'),
                    ],
                );
            }
        });
    }
}
