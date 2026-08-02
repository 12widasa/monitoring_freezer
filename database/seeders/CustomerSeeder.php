<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $customers = [
                [
                    'user' => [
                        'name' => 'PT Jaya Makmur Sentosa',
                        'username' => 'customer.jaya',
                        'email' => 'jaya@example.test',
                        'phone' => '081234561001',
                    ],
                    'profile' => [
                        'company_name' => 'PT Jaya Makmur Sentosa',
                        'phone' => '0247601001',
                        'address' => 'Jl. Industri Raya No. 12, Semarang',
                    ],
                ],
                [
                    'user' => [
                        'name' => 'CV Sumber Rejeki',
                        'username' => 'customer.sumber',
                        'email' => 'sumber@example.test',
                        'phone' => '081234561002',
                    ],
                    'profile' => [
                        'company_name' => 'CV Sumber Rejeki',
                        'phone' => '0247601002',
                        'address' => 'Jl. Majapahit No. 88, Semarang',
                    ],
                ],
                [
                    'user' => [
                        'name' => 'PT Berkah Pangan Abadi',
                        'username' => 'customer.berkah',
                        'email' => 'berkah@example.test',
                        'phone' => '081234561003',
                    ],
                    'profile' => [
                        'company_name' => 'PT Berkah Pangan Abadi',
                        'phone' => '0247601003',
                        'address' => 'Jl. Kaligawe No. 25, Semarang',
                    ],
                ],
            ];

            foreach ($customers as $data) {
                $user = User::query()->updateOrCreate(
                    [
                        'username' => $data['user']['username'],
                    ],
                    [
                        ...$data['user'],
                        'password' => Hash::make('password'),
                        'role' => UserRole::CUSTOMER->value,
                        'is_active' => true,
                    ],
                );

                Customer::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    $data['profile'],
                );
            }
        });
    }
}
