<?php

namespace Tests\Feature\Admin;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairLog;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser(
            role: UserRole::ADMIN,
            name: 'Administrator Test',
            username: 'admin_test',
            email: 'admin@example.com',
        );
    }

    public function test_admin_can_view_technician_detail(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Detail',
            username: 'teknisi_detail',
            email: 'teknisi.detail@example.com',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $technician));

        $response
            ->assertOk()
            ->assertViewIs('admin.users.show')
            ->assertViewHas('user', function (User $viewUser) use ($technician): bool {
                return $viewUser->is($technician);
            })
            ->assertViewHas('activities')
            ->assertSeeText('Teknisi Detail')
            ->assertSeeText('teknisi_detail')
            ->assertSeeText('teknisi.detail@example.com')
            ->assertSeeText('Teknisi')
            ->assertSeeText('Informasi Akun')
            ->assertSeeText('Informasi Operasional')
            ->assertSeeText('Aktivitas Terbaru');
    }

    public function test_admin_can_view_customer_detail_and_company_information(): void
    {
        $customerUser = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pelanggan Detail',
            username: 'pelanggan_detail',
            email: 'pelanggan.detail@example.com',
        );

        Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Detail',
            'address' => 'Jl. Pelanggan Detail No. 10',
            'phone' => '0215550100',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $customerUser));

        $response
            ->assertOk()
            ->assertViewIs('admin.users.show')
            ->assertSeeText('Pelanggan Detail')
            ->assertSeeText('Pelanggan')
            ->assertSeeText('Informasi Perusahaan')
            ->assertSeeText('PT Pelanggan Detail')
            ->assertSeeText('Jl. Pelanggan Detail No. 10')
            ->assertSeeText('0215550100');
    }

    public function test_empty_technician_detail_displays_empty_activity_state(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Tanpa Aktivitas',
            username: 'teknisi_kosong',
            email: 'teknisi.kosong@example.com',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $technician));

        $response
            ->assertOk()
            ->assertSeeText('Belum ada aktivitas')
            ->assertSeeText('Belum ada aktivitas reparasi')
            ->assertSeeText(
                'Aktivitas akan tampil setelah terdapat pembaruan reparasi.',
            );

        $this->assertCount(
            0,
            $response->viewData('activities'),
        );

        $this->assertSame(
            0,
            $response->viewData('user')->active_repairs_count,
        );

        $this->assertSame(
            0,
            $response->viewData('user')->completed_repairs_count,
        );
    }

    public function test_empty_customer_detail_contains_zero_operational_counts(): void
    {
        $customerUser = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pelanggan Tanpa Unit',
            username: 'pelanggan_kosong',
            email: 'pelanggan.kosong@example.com',
        );

        Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Tanpa Unit',
            'address' => 'Jl. Tanpa Unit No. 1',
            'phone' => '0215550200',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $customerUser));

        $response->assertOk();

        /** @var User $viewUser */
        $viewUser = $response->viewData('user');

        $this->assertNotNull($viewUser->customer);
        $this->assertSame(0, $viewUser->customer->freezers_count);
        $this->assertSame(0, $viewUser->customer->active_repairs_count);
        $this->assertSame(0, $viewUser->customer->completed_repairs_count);

        $response
            ->assertSeeText('Informasi Operasional')
            ->assertSeeText('Belum ada aktivitas reparasi');
    }

    public function test_technician_activity_is_loaded_from_repair_logs_updated_by_that_technician(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Aktivitas',
            username: 'teknisi_aktivitas',
            email: 'teknisi.aktivitas@example.com',
        );

        $customerUser = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pemilik Freezer',
            username: 'pemilik_freezer',
            email: 'pemilik.freezer@example.com',
        );

        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pemilik Freezer',
            'address' => 'Jl. Freezer No. 5',
            'phone' => '0215550300',
        ]);

        $freezer = $this->createFreezer(
            customer: $customer,
            serialNumber: 'FRZ-TECH-001',
        );

        $intake = $this->createVerifiedIntake($freezer);

        $repair = Repair::create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => $technician->id,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::INSPECTING,
            'initial_analysis' => 'Pemeriksaan awal unit.',
        ]);

        RepairLog::create([
            'repair_id' => $repair->id,
            'status' => RepairStatus::INSPECTING,
            'description' => 'Teknisi memeriksa sistem pendingin.',
            'updated_by' => $technician->id,
            'created_at' => now(),
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $technician));

        $response
            ->assertOk()
            ->assertSeeText('FRZ-TECH-001')
            ->assertSeeText('Teknisi memeriksa sistem pendingin.')
            ->assertSeeText('Aktivitas reparasi terakhir');

        $this->assertCount(
            1,
            $response->viewData('activities'),
        );

        $this->assertSame(
            $repair->id,
            $response->viewData('activities')->first()->repair_id,
        );
    }

    public function test_customer_activity_only_uses_repairs_for_customer_freezers(): void
    {
        $customerUser = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pelanggan Aktivitas',
            username: 'pelanggan_aktivitas',
            email: 'pelanggan.aktivitas@example.com',
        );

        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Aktivitas',
            'address' => 'Jl. Aktivitas No. 7',
            'phone' => '0215550400',
        ]);

        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Customer',
            username: 'teknisi_customer',
            email: 'teknisi.customer@example.com',
        );

        $ownedFreezer = $this->createFreezer(
            customer: $customer,
            serialNumber: 'FRZ-CUSTOMER-001',
        );

        $ownedIntake = $this->createVerifiedIntake($ownedFreezer);

        $ownedRepair = Repair::create([
            'freezer_id' => $ownedFreezer->id,
            'service_intake_id' => $ownedIntake->id,
            'technician_id' => $technician->id,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::REPAIRING,
            'initial_analysis' => 'Kerusakan kompresor.',
        ]);

        RepairLog::create([
            'repair_id' => $ownedRepair->id,
            'status' => RepairStatus::REPAIRING,
            'description' => 'Kompresor sedang diganti.',
            'updated_by' => $technician->id,
            'created_at' => now(),
        ]);

        $otherCustomerUser = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pelanggan Lain',
            username: 'pelanggan_lain',
            email: 'pelanggan.lain@example.com',
        );

        $otherCustomer = Customer::create([
            'user_id' => $otherCustomerUser->id,
            'company_name' => 'PT Pelanggan Lain',
            'address' => 'Jl. Lain No. 8',
            'phone' => '0215550500',
        ]);

        $otherFreezer = $this->createFreezer(
            customer: $otherCustomer,
            serialNumber: 'FRZ-OTHER-001',
        );

        $otherIntake = $this->createVerifiedIntake($otherFreezer);

        $otherRepair = Repair::create([
            'freezer_id' => $otherFreezer->id,
            'service_intake_id' => $otherIntake->id,
            'technician_id' => $technician->id,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::REPAIRING,
            'initial_analysis' => 'Unit pelanggan lain.',
        ]);

        RepairLog::create([
            'repair_id' => $otherRepair->id,
            'status' => RepairStatus::REPAIRING,
            'description' => 'Aktivitas milik pelanggan lain.',
            'updated_by' => $technician->id,
            'created_at' => now()->addMinute(),
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $customerUser));

        $response
            ->assertOk()
            ->assertSeeText('FRZ-CUSTOMER-001')
            ->assertSeeText('Kompresor sedang diganti.')
            ->assertDontSeeText('FRZ-OTHER-001')
            ->assertDontSeeText('Aktivitas milik pelanggan lain.');

        $this->assertCount(
            1,
            $response->viewData('activities'),
        );

        $this->assertSame(
            $ownedRepair->id,
            $response->viewData('activities')->first()->repair_id,
        );
    }

    public function test_admin_account_cannot_be_opened_through_user_detail_endpoint(): void
    {
        $targetAdmin = $this->createUser(
            role: UserRole::ADMIN,
            name: 'Admin Target',
            username: 'admin_target',
            email: 'admin.target@example.com',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.users.show', $targetAdmin));

        $response->assertNotFound();
    }

    public function test_technician_and_customer_cannot_open_admin_user_detail(): void
    {
        $target = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Target Pengguna',
            username: 'target_pengguna',
            email: 'target.pengguna@example.com',
        );

        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Actor',
            username: 'teknisi_actor_show',
            email: 'teknisi.actor.show@example.com',
        );

        $customer = $this->createUser(
            role: UserRole::CUSTOMER,
            name: 'Pelanggan Actor',
            username: 'pelanggan_actor_show',
            email: 'pelanggan.actor.show@example.com',
        );

        $this
            ->actingAs($technician)
            ->get(route('admin.users.show', $target))
            ->assertForbidden();

        $this
            ->actingAs($customer)
            ->get(route('admin.users.show', $target))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_user_detail(): void
    {
        $technician = $this->createUser(
            role: UserRole::TECHNICIAN,
            name: 'Teknisi Guest Target',
            username: 'guest_show_target',
            email: 'guest.show.target@example.com',
        );

        $response = $this->get(
            route('admin.users.show', $technician),
        );

        $response->assertRedirect(route('login'));
    }

    private function createUser(
        UserRole $role,
        string $name,
        string $username,
        string $email,
        bool $isActive = true,
    ): User {
        return User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => '081234567890',
            'password' => 'password',
            'role' => $role,
            'is_active' => $isActive,
        ]);
    }

    private function createFreezer(
        Customer $customer,
        string $serialNumber,
    ): Freezer {
        $freezer = Freezer::create([
            'customer_id' => $customer->id,
            'brand' => 'Test Brand',
            'model' => 'Test Model',
            'serial_number' => $serialNumber,
            'capacity_liter' => 500,
            'estimated_age' => '2 tahun',
            'created_by' => $customer->user_id,
        ]);

        $freezer->forceFill([
            'freezer_code' => sprintf(
                'FZ-%05d',
                $freezer->id,
            ),
        ])->save();

        return $freezer->refresh();
    }

    private function createVerifiedIntake(
        Freezer $freezer,
    ): ServiceIntake {
        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' =>
            'TMP-' . bin2hex(random_bytes(8)),
            'complaint_note' => 'Freezer tidak dingin.',
            'condition_note' => 'Unit telah diperiksa.',
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'received_by' => $this->admin->id,
            'received_at' => now(),
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
            'completed_at' => null,
        ]);

        $intake->forceFill([
            'intake_code' => sprintf(
                'IN-%05d',
                $intake->id,
            ),
        ])->save();

        return $intake->refresh();
    }
}
