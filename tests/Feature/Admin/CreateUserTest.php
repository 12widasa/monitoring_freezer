<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateUserTest extends TestCase
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

    public function test_admin_can_create_an_active_technician(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'Teknisi Baru',
                'username' => 'teknisi_baru',
                'email' => 'teknisi@example.com',
                'phone' => '+6281234567890',
                'password' => 'x',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath(
                'message',
                'Pengguna berhasil ditambahkan.'
            )
            ->assertJsonPath(
                'user.name',
                'Teknisi Baru'
            )
            ->assertJsonPath(
                'user.role',
                UserRole::TECHNICIAN->value
            );

        $this->assertDatabaseHas('users', [
            'name' => 'Teknisi Baru',
            'username' => 'teknisi_baru',
            'email' => 'teknisi@example.com',
            'phone' => '+6281234567890',
            'role' => UserRole::TECHNICIAN->value,
            'is_active' => true,
        ]);

        $technician = User::query()
            ->where('username', 'teknisi_baru')
            ->firstOrFail();

        $this->assertTrue(
            Hash::check('x', $technician->password)
        );

        $this->assertDatabaseMissing('customers', [
            'user_id' => $technician->id,
        ]);
    }

    public function test_admin_can_create_a_customer_with_company_profile(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::CUSTOMER->value,
                'name' => 'Pelanggan Baru',
                'username' => 'pelanggan_baru',
                'email' => 'pelanggan@example.com',
                'phone' => '+60123456789',
                'company_name' => 'PT Pelanggan Baru',
                'company_phone' => '+62215550188',
                'address' => 'Jl. Contoh No. 10',
                'password' => 'bebas',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath(
                'user.role',
                UserRole::CUSTOMER->value
            );

        $customerUser = User::query()
            ->where('username', 'pelanggan_baru')
            ->firstOrFail();

        $this->assertTrue($customerUser->is_active);

        $this->assertDatabaseHas('customers', [
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Baru',
            'phone' => '+62215550188',
            'address' => 'Jl. Contoh No. 10',
        ]);
    }

    public function test_customer_company_fields_are_required(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::CUSTOMER->value,
                'name' => 'Pelanggan Tidak Lengkap',
                'username' => 'customer_incomplete',
                'email' => 'incomplete@example.com',
                'phone' => '081234567890',
                'password' => 'x',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'company_name',
                'company_phone',
                'address',
            ]);

        $this->assertDatabaseMissing('users', [
            'username' => 'customer_incomplete',
        ]);
    }

    public function test_admin_role_cannot_be_created_from_this_endpoint(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::ADMIN->value,
                'name' => 'Admin Baru',
                'username' => 'admin_baru',
                'email' => 'adminbaru@example.com',
                'phone' => '081234567890',
                'password' => 'x',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role');

        $this->assertDatabaseMissing('users', [
            'username' => 'admin_baru',
        ]);
    }

    public function test_duplicate_username_and_email_are_rejected(): void
    {
        User::create([
            'name' => 'Existing User',
            'username' => 'existing_user',
            'email' => 'existing@example.com',
            'phone' => '081111111111',
            'password' => 'password',
            'role' => UserRole::TECHNICIAN,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'Duplicate User',
                'username' => 'existing_user',
                'email' => 'existing@example.com',
                'phone' => '082222222222',
                'password' => 'x',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'username',
                'email',
            ]);

        $this->assertDatabaseCount('users', 2);
    }

    public function test_phone_accepts_digits_and_optional_plus_at_the_start(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'International User',
                'username' => 'international_user',
                'email' => 'international@example.com',
                'phone' => '+442071234567',
                'password' => 'x',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'username' => 'international_user',
            'phone' => '+442071234567',
        ]);
    }

    public function test_phone_rejects_letters_and_misplaced_plus_signs(): void
    {
        foreach (
            [
                '0812abc345',
                '0812+345',
                '++62812345',
            ] as $index => $phone
        ) {
            $response = $this
                ->actingAs($this->admin)
                ->postJson(route('admin.users.store'), [
                    'role' => UserRole::TECHNICIAN->value,
                    'name' => "Invalid Phone {$index}",
                    'username' => "invalid_phone_{$index}",
                    'email' => "invalid{$index}@example.com",
                    'phone' => $phone,
                    'password' => 'x',
                ]);

            $response
                ->assertUnprocessable()
                ->assertJsonValidationErrors('phone');
        }

        $this->assertDatabaseMissing('users', [
            'username' => 'invalid_phone_0',
        ]);

        $this->assertDatabaseMissing('users', [
            'username' => 'invalid_phone_1',
        ]);

        $this->assertDatabaseMissing('users', [
            'username' => 'invalid_phone_2',
        ]);
    }

    public function test_password_only_needs_to_be_present(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'Short Password User',
                'username' => 'short_password',
                'email' => 'shortpassword@example.com',
                'phone' => '081234567890',
                'password' => '1',
            ]);

        $response->assertCreated();

        $user = User::query()
            ->where('username', 'short_password')
            ->firstOrFail();

        $this->assertTrue(
            Hash::check('1', $user->password)
        );
    }

    public function test_guest_cannot_create_a_user(): void
    {
        $response = $this->postJson(
            route('admin.users.store'),
            [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'Unauthorized User',
                'username' => 'unauthorized_user',
                'email' => 'unauthorized@example.com',
                'phone' => '081234567890',
                'password' => 'x',
            ]
        );

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('users', [
            'username' => 'unauthorized_user',
        ]);
    }

    public function test_non_admin_cannot_create_a_user(): void
    {
        $technician = User::create([
            'name' => 'Existing Technician',
            'username' => 'existing_technician',
            'email' => 'existing.technician@example.com',
            'phone' => '081234567891',
            'password' => 'password',
            'role' => UserRole::TECHNICIAN,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($technician)
            ->postJson(route('admin.users.store'), [
                'role' => UserRole::TECHNICIAN->value,
                'name' => 'Forbidden User',
                'username' => 'forbidden_user',
                'email' => 'forbidden@example.com',
                'phone' => '081234567892',
                'password' => 'x',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'username' => 'forbidden_user',
        ]);
    }
}
