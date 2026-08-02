<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'role' => $data['role'],
                'is_active' => true,
            ]);

            if ($user->role === UserRole::CUSTOMER) {
                $user->customer()->create([
                    'company_name' => $data['company_name'],
                    'phone' => $data['company_phone'],
                    'address' => $data['address'],
                ]);
            }

            return $user;
        });
    }
}
