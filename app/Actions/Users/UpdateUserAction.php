<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $userData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $user->update($userData);

            if ($user->role === UserRole::CUSTOMER) {
                $user->customer()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'company_name' => $data['company_name'],
                        'phone' => $data['company_phone'],
                        'address' => $data['address'],
                    ],
                );
            }

            return $user->refresh()->load('customer');
        });
    }
}
