<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InactiveUserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_login_with_correct_credentials(): void
    {
        $user = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'inactive_technician',
            email: 'inactive.technician@example.com',
            isActive: false,
        );

        $response = $this->post('/login', [
            'identity' => $user->username,
            'password' => 'password',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors([
                'identity' => 'Email/Username atau kata sandi tidak cocok.',
            ]);

        $this->assertGuest();
    }

    public function test_active_technician_can_login_with_username(): void
    {
        $user = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'active_technician',
            email: 'active.technician@example.com',
        );

        $response = $this->post('/login', [
            'identity' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(
            route('technician.dashboard'),
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_active_customer_can_login_with_email(): void
    {
        $user = $this->createUser(
            role: UserRole::CUSTOMER,
            username: 'active_customer',
            email: 'active.customer@example.com',
        );

        $response = $this->post('/login', [
            'identity' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(
            route('customer.monitoring'),
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_is_logged_out_after_being_deactivated(): void
    {
        $user = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'deactivated_session',
            email: 'deactivated.session@example.com',
        );

        $this->actingAs($user);

        $user->update([
            'is_active' => false,
        ]);

        $response = $this->get(
            route('technician.dashboard'),
        );

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'identity' => 'Akun Anda sedang dinonaktifkan.',
            ]);

        $this->assertGuest();
    }

    public function test_active_authenticated_user_can_continue_accessing_portal(): void
    {
        $user = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'active_session',
            email: 'active.session@example.com',
        );

        $response = $this
            ->actingAs($user)
            ->get(route('technician.dashboard'));

        $response->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_admin_session_is_also_terminated(): void
    {
        $admin = $this->createUser(
            role: UserRole::ADMIN,
            username: 'inactive_admin',
            email: 'inactive.admin@example.com',
            isActive: true,
        );

        $this->actingAs($admin);

        $admin->update([
            'is_active' => false,
        ]);

        $response = $this->get(
            route('admin.dashboard'),
        );

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    private function createUser(
        UserRole $role,
        string $username,
        string $email,
        bool $isActive = true,
    ): User {
        return User::create([
            'name' => match ($role) {
                UserRole::ADMIN => 'Administrator Test',
                UserRole::TECHNICIAN => 'Teknisi Test',
                UserRole::CUSTOMER => 'Pelanggan Test',
            },
            'username' => $username,
            'email' => $email,
            'phone' => '081234567890',
            'password' => 'password',
            'role' => $role,
            'is_active' => $isActive,
        ]);
    }
}
