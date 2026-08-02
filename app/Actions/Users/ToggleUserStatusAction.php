<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;

class ToggleUserStatusAction
{
    public function execute(User $user): User
    {
        abort_unless(
            in_array($user->role, [
                UserRole::TECHNICIAN,
                UserRole::CUSTOMER,
            ], true),
            404,
        );

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return $user->refresh();
    }
}
