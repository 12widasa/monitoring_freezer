<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Test',
            'username' => 'admin_test',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'password' => 'admin-password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_a_technician(): void
    {
        $technician = $this->createTechnician();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'name' => 'Teknisi Diperbarui',
                    'username' => 'teknisi_diperbarui',
                    'email' => 'teknisi.updated@example.com',
                    'phone' => '+6281234500000',
                    'password' => '',
                ],
            );

        $response
            ->assertOk()
            ->assertJsonPath(
                'message',
                'Pengguna berhasil diperbarui.',
            )
            ->assertJsonPath(
                'user.id',
                $technician->id,
            )
            ->assertJsonPath(
                'user.name',
                'Teknisi Diperbarui',
            )
            ->assertJsonPath(
                'user.role',
                UserRole::TECHNICIAN->value,
            );

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'name' => 'Teknisi Diperbarui',
            'username' => 'teknisi_diperbarui',
            'email' => 'teknisi.updated@example.com',
            'phone' => '+6281234500000',
            'role' => UserRole::TECHNICIAN->value,
        ]);

        $this->assertDatabaseMissing('customers', [
            'user_id' => $technician->id,
        ]);
    }

    public function test_admin_can_update_a_customer_and_company_profile(): void
    {
        $customerUser = $this->createCustomer();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $customerUser),
                [
                    'name' => 'Pelanggan Diperbarui',
                    'username' => 'pelanggan_diperbarui',
                    'email' => 'pelanggan.updated@example.com',
                    'phone' => '+60111111111',
                    'company_name' => 'PT Perusahaan Diperbarui',
                    'company_phone' => '+622199999999',
                    'address' => 'Jl. Perusahaan Baru No. 20',
                    'password' => '',
                ],
            );

        $response
            ->assertOk()
            ->assertJsonPath(
                'user.role',
                UserRole::CUSTOMER->value,
            );

        $this->assertDatabaseHas('users', [
            'id' => $customerUser->id,
            'name' => 'Pelanggan Diperbarui',
            'username' => 'pelanggan_diperbarui',
            'email' => 'pelanggan.updated@example.com',
            'phone' => '+60111111111',
            'role' => UserRole::CUSTOMER->value,
        ]);

        $this->assertDatabaseHas('customers', [
            'user_id' => $customerUser->id,
            'company_name' => 'PT Perusahaan Diperbarui',
            'phone' => '+622199999999',
            'address' => 'Jl. Perusahaan Baru No. 20',
        ]);
    }

    public function test_empty_password_preserves_the_existing_password(): void
    {
        $technician = $this->createTechnician();

        $originalPasswordHash = $technician->password;

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'name' => $technician->name,
                    'username' => $technician->username,
                    'email' => $technician->email,
                    'phone' => $technician->phone,
                    'password' => '',
                ],
            );

        $response->assertOk();

        $technician->refresh();

        $this->assertSame(
            $originalPasswordHash,
            $technician->password,
        );

        $this->assertTrue(
            Hash::check('old-password', $technician->password),
        );
    }

    public function test_filled_password_replaces_the_existing_password(): void
    {
        $technician = $this->createTechnician();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'name' => $technician->name,
                    'username' => $technician->username,
                    'email' => $technician->email,
                    'phone' => $technician->phone,
                    'password' => '1',
                ],
            );

        $response->assertOk();

        $technician->refresh();

        $this->assertTrue(
            Hash::check('1', $technician->password),
        );

        $this->assertFalse(
            Hash::check('old-password', $technician->password),
        );
    }

    public function test_user_can_keep_their_existing_username_and_email(): void
    {
        $technician = $this->createTechnician();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'name' => 'Nama Tetap Valid',
                    'username' => $technician->username,
                    'email' => $technician->email,
                    'phone' => $technician->phone,
                    'password' => '',
                ],
            );

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'name' => 'Nama Tetap Valid',
            'username' => 'existing_technician',
            'email' => 'existing.technician@example.com',
        ]);
    }

    public function test_duplicate_username_and_email_from_another_user_are_rejected(): void
    {
        $technician = $this->createTechnician();

        User::create([
            'name' => 'Pengguna Lain',
            'username' => 'other_user',
            'email' => 'other@example.com',
            'phone' => '082222222222',
            'password' => 'password',
            'role' => UserRole::TECHNICIAN,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'name' => 'Nama Baru',
                    'username' => 'other_user',
                    'email' => 'other@example.com',
                    'phone' => '083333333333',
                    'password' => '',
                ],
            );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'username',
                'email',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'username' => 'existing_technician',
            'email' => 'existing.technician@example.com',
        ]);
    }

    public function test_customer_company_fields_are_required_when_updating(): void
    {
        $customerUser = $this->createCustomer();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $customerUser),
                [
                    'name' => 'Customer Tidak Lengkap',
                    'username' => 'customer_incomplete',
                    'email' => 'incomplete@example.com',
                    'phone' => '081234567899',
                    'company_name' => '',
                    'company_phone' => '',
                    'address' => '',
                    'password' => '',
                ],
            );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'company_name',
                'company_phone',
                'address',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $customerUser->id,
            'username' => 'existing_customer',
        ]);

        $this->assertDatabaseHas('customers', [
            'user_id' => $customerUser->id,
            'company_name' => 'PT Existing Customer',
        ]);
    }

    public function test_role_from_request_cannot_change_the_user_role(): void
    {
        $technician = $this->createTechnician();

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $technician),
                [
                    'role' => UserRole::CUSTOMER->value,
                    'name' => 'Teknisi Tetap',
                    'username' => 'teknisi_tetap',
                    'email' => 'teknisi.tetap@example.com',
                    'phone' => '081234567898',
                    'company_name' => 'PT Tidak Boleh Dibuat',
                    'company_phone' => '0215550188',
                    'address' => 'Alamat tidak boleh digunakan',
                    'password' => '',
                ],
            );

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'role' => UserRole::TECHNICIAN->value,
        ]);

        $this->assertDatabaseMissing('customers', [
            'user_id' => $technician->id,
        ]);
    }

    public function test_admin_account_cannot_be_updated_through_this_endpoint(): void
    {
        $targetAdmin = User::create([
            'name' => 'Admin Target',
            'username' => 'admin_target',
            'email' => 'admin.target@example.com',
            'phone' => '081111111111',
            'password' => 'admin-password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->patchJson(
                route('admin.users.update', $targetAdmin),
                [
                    'name' => 'Admin Diubah',
                    'username' => 'admin_diubah',
                    'email' => 'admin.diubah@example.com',
                    'phone' => '082222222222',
                    'password' => '',
                ],
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetAdmin->id,
            'name' => 'Admin Target',
            'username' => 'admin_target',
            'email' => 'admin.target@example.com',
        ]);
    }

    public function test_guest_cannot_update_a_user(): void
    {
        $technician = $this->createTechnician();

        $response = $this->patchJson(
            route('admin.users.update', $technician),
            [
                'name' => 'Unauthorized Update',
                'username' => 'unauthorized_update',
                'email' => 'unauthorized.update@example.com',
                'phone' => '081234567897',
                'password' => '',
            ],
        );

        $response->assertUnauthorized();

        $this->assertDatabaseHas('users', [
            'id' => $technician->id,
            'username' => 'existing_technician',
        ]);
    }

    public function test_non_admin_cannot_update_a_user(): void
    {
        $targetTechnician = $this->createTechnician();

        $otherTechnician = User::create([
            'name' => 'Teknisi Login',
            'username' => 'technician_login',
            'email' => 'technician.login@example.com',
            'phone' => '081234567896',
            'password' => 'password',
            'role' => UserRole::TECHNICIAN,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($otherTechnician)
            ->patchJson(
                route('admin.users.update', $targetTechnician),
                [
                    'name' => 'Forbidden Update',
                    'username' => 'forbidden_update',
                    'email' => 'forbidden.update@example.com',
                    'phone' => '081234567895',
                    'password' => '',
                ],
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetTechnician->id,
            'username' => 'existing_technician',
        ]);
    }

    private function createTechnician(): User
    {
        return User::create([
            'name' => 'Existing Technician',
            'username' => 'existing_technician',
            'email' => 'existing.technician@example.com',
            'phone' => '081234567891',
            'password' => 'old-password',
            'role' => UserRole::TECHNICIAN,
            'is_active' => true,
        ]);
    }

    private function createCustomer(): User
    {
        $user = User::create([
            'name' => 'Existing Customer',
            'username' => 'existing_customer',
            'email' => 'existing.customer@example.com',
            'phone' => '081234567892',
            'password' => 'old-password',
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        Customer::create([
            'user_id' => $user->id,
            'company_name' => 'PT Existing Customer',
            'phone' => '0215550100',
            'address' => 'Jl. Existing Customer No. 1',
        ]);

        return $user;
    }
}
