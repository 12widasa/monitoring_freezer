<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser(
            role: UserRole::ADMIN,
            username: 'admin_test',
            email: 'admin@example.com',
        );
    }

    public function test_admin_can_deactivate_an_active_technician(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'technician_active',
            email: 'technician.active@example.com',
            isActive: true,
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.users.show', $technician))
            ->patch(route('admin.users.status.update', $technician));

        $response
            ->assertRedirect(route('admin.users.show', $technician))
            ->assertSessionHas(
                'success',
                'Akun pengguna berhasil dinonaktifkan.',
            );

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'is_active' => false,
        ]);

        $this->assertFalse(
            $technician->refresh()->is_active,
        );
    }

    public function test_admin_can_activate_an_inactive_customer(): void
    {
        $customer = $this->createUser(
            role: UserRole::CUSTOMER,
            username: 'customer_inactive',
            email: 'customer.inactive@example.com',
            isActive: false,
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.users.show', $customer))
            ->patch(route('admin.users.status.update', $customer));

        $response
            ->assertRedirect(route('admin.users.show', $customer))
            ->assertSessionHas(
                'success',
                'Akun pengguna berhasil diaktifkan.',
            );

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'is_active' => true,
        ]);

        $this->assertTrue(
            $customer->refresh()->is_active,
        );
    }

    public function test_admin_account_cannot_be_changed_through_status_endpoint(): void
    {
        $targetAdmin = $this->createUser(
            role: UserRole::ADMIN,
            username: 'target_admin',
            email: 'target.admin@example.com',
            isActive: true,
        );

        $response = $this
            ->actingAs($this->admin)
            ->patch(route('admin.users.status.update', $targetAdmin));

        $response->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $targetAdmin->id,
            'is_active' => true,
        ]);
    }

    public function test_technician_cannot_change_user_status(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'technician_actor',
            email: 'technician.actor@example.com',
        );

        $customer = $this->createUser(
            role: UserRole::CUSTOMER,
            username: 'customer_target',
            email: 'customer.target@example.com',
        );

        $response = $this
            ->actingAs($technician)
            ->patch(route('admin.users.status.update', $customer));

        $response->assertForbidden();

        $this->assertTrue(
            $customer->refresh()->is_active,
        );
    }

    public function test_customer_cannot_change_user_status(): void
    {
        $customer = $this->createUser(
            role: UserRole::CUSTOMER,
            username: 'customer_actor',
            email: 'customer.actor@example.com',
        );

        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'technician_target',
            email: 'technician.target@example.com',
        );

        $response = $this
            ->actingAs($customer)
            ->patch(route('admin.users.status.update', $technician));

        $response->assertForbidden();

        $this->assertTrue(
            $technician->refresh()->is_active,
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            username: 'guest_target',
            email: 'guest.target@example.com',
        );

        $response = $this->patch(
            route('admin.users.status.update', $technician),
        );

        $response->assertRedirect(route('login'));

        $this->assertTrue(
            $technician->refresh()->is_active,
        );
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
