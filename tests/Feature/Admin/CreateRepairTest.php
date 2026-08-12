<?php

namespace Tests\Feature\Admin;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateRepairTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private Customer $customer;

    private Freezer $freezer;

    private ServiceIntake $intake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Reparasi',
            'username' => 'admin_repair_test',
            'email' => 'admin.repair@example.com',
            'phone' => '081234567801',
            'role' => UserRole::ADMIN,
        ]);

        $this->technician = $this->createUser([
            'name' => 'Teknisi Reparasi',
            'username' => 'technician_repair_test',
            'email' => 'technician.repair@example.com',
            'phone' => '081234567802',
            'role' => UserRole::TECHNICIAN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Reparasi',
            'username' => 'customer_repair_test',
            'email' => 'customer.repair@example.com',
            'phone' => '081234567803',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Reparasi',
            'phone' => '0241234567',
            'address' => 'Jl. Pengujian Reparasi No. 1',
        ]);

        $this->freezer = $this->createFreezer();

        $this->intake = $this->createIntake(
            freezer: $this->freezer,
            status: VerificationStatus::VERIFIED,
        );
    }

    public function test_admin_can_create_repair_from_verified_intake(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => $this->technician->id,
                ],
            );

        $repair = Repair::query()->first();

        $this->assertNotNull($repair);

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHas(
                'success',
                'Tugas reparasi berhasil dibuat.',
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'id' => $repair->id,
            'freezer_id' => $this->freezer->id,
            'service_intake_id' => $this->intake->id,
            'technician_id' => $this->technician->id,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::QUEUED->value,
            'initial_analysis' => null,
        ]);
    }

    public function test_creating_repair_with_technician_creates_active_assignment_and_log(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => $this->technician->id,
                ],
            );

        $response->assertSessionHasNoErrors();

        $repair = Repair::query()->firstOrFail();

        $assignment = RepairAssignment::query()
            ->where('repair_id', $repair->id)
            ->first();

        $this->assertNotNull($assignment);

        $this->assertDatabaseHas('repair_assignments', [
            'id' => $assignment->id,
            'repair_id' => $repair->id,
            'technician_id' => $this->technician->id,
            'assigned_by' => $this->admin->id,
            'ended_at' => null,
            'ended_by' => null,
            'end_reason' => null,
        ]);

        $this->assertSame(
            $repair->created_at->format('Y-m-d H:i:s'),
            $assignment->assigned_at->format('Y-m-d H:i:s'),
        );

        $this->assertDatabaseHas('repair_logs', [
            'repair_id' => $repair->id,
            'status' => RepairStatus::QUEUED->value,
            'description' => sprintf(
                'Teknisi %s ditetapkan untuk menangani tugas reparasi.',
                $this->technician->name,
            ),
            'updated_by' => $this->admin->id,
        ]);
    }

    public function test_creating_repair_without_technician_does_not_create_assignment_or_assignment_log(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,
                ],
            );

        $response->assertSessionHasNoErrors();

        $repair = Repair::query()->firstOrFail();

        $this->assertNull($repair->technician_id);
        $this->assertDatabaseCount('repair_assignments', 0);
        $this->assertDatabaseCount('repair_logs', 0);

        $this->assertFalse(
            RepairLog::query()
                ->where('repair_id', $repair->id)
                ->where(
                    'description',
                    'like',
                    'Teknisi % ditetapkan untuk menangani tugas reparasi.',
                )
                ->exists(),
        );
    }

    public function test_repair_can_be_created_without_technician(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => '',
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'freezer_id' => $this->freezer->id,
            'service_intake_id' => $this->intake->id,
            'technician_id' => null,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::QUEUED->value,
            'initial_analysis' => null,
        ]);
    }

    public function test_freezer_id_is_derived_from_service_intake(): void
    {
        $otherFreezer = $this->createFreezer([
            'brand' => 'Polytron',
            'model' => 'SCN-200',
            'serial_number' => 'SERIAL-OTHER-001',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,

                    // Field manipulasi ini harus diabaikan.
                    'freezer_id' => $otherFreezer->id,
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'service_intake_id' => $this->intake->id,
            'freezer_id' => $this->freezer->id,
        ]);

        $this->assertDatabaseMissing('repairs', [
            'service_intake_id' => $this->intake->id,
            'freezer_id' => $otherFreezer->id,
        ]);
    }

    public function test_admin_and_status_cannot_be_manipulated_from_request(): void
    {
        $otherAdmin = $this->createUser([
            'name' => 'Administrator Lain',
            'username' => 'other_admin_repair_test',
            'email' => 'other.admin.repair@example.com',
            'phone' => '081234567804',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,

                    // Field manipulasi ini harus diabaikan.
                    'admin_id' => $otherAdmin->id,
                    'status' => RepairStatus::COMPLETED->value,
                    'initial_analysis' => 'Analisis manipulasi.',
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'service_intake_id' => $this->intake->id,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::QUEUED->value,
            'initial_analysis' => null,
        ]);

        $this->assertDatabaseMissing('repairs', [
            'service_intake_id' => $this->intake->id,
            'admin_id' => $otherAdmin->id,
        ]);
    }

    public function test_pending_intake_cannot_be_used_to_create_repair(): void
    {
        $this->intake->update([
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['service_intake_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);
    }

    public function test_rejected_intake_cannot_be_used_to_create_repair(): void
    {
        $this->intake->update([
            'status_verifikasi' =>
            VerificationStatus::REJECTED,
            'rejection_reason' =>
            'Unit tidak sesuai dengan data.',
            'completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['service_intake_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);
    }

    public function test_only_latest_intake_can_be_used_to_create_repair(): void
    {
        $newestIntake = $this->createIntake(
            freezer: $this->freezer,
            status: VerificationStatus::VERIFIED,
            receivedAt: now()->addDay(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => null,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['service_intake_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);

        $this->assertDatabaseHas('service_intakes', [
            'id' => $newestIntake->id,
            'freezer_id' => $this->freezer->id,
        ]);
    }

    public function test_one_intake_cannot_have_more_than_one_repair(): void
    {
        Repair::create([
            'freezer_id' => $this->freezer->id,
            'service_intake_id' => $this->intake->id,
            'technician_id' => null,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::QUEUED,
            'initial_analysis' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => $this->technician->id,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['service_intake_id'],
                null,
                'createRepair',
            );

        $this->assertSame(
            1,
            Repair::query()
                ->where(
                    'service_intake_id',
                    $this->intake->id,
                )
                ->count(),
        );
    }

    public function test_selected_user_must_be_a_technician(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => $this->admin->id,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);
    }

    public function test_inactive_technician_cannot_be_assigned(): void
    {
        $this->technician->update([
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => $this->intake->id,
                    'technician_id' => $this->technician->id,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);
    }

    public function test_service_intake_is_required(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.repairs.index'))
            ->post(
                route('admin.repairs.store'),
                [
                    'service_intake_id' => '',
                    'technician_id' => null,
                ],
            );

        $response
            ->assertRedirect(route('admin.repairs.index'))
            ->assertSessionHasErrors(
                ['service_intake_id'],
                null,
                'createRepair',
            );

        $this->assertDatabaseCount('repairs', 0);
    }

    public function test_guest_cannot_create_repair(): void
    {
        $response = $this->post(
            route('admin.repairs.store'),
            [
                'service_intake_id' => $this->intake->id,
                'technician_id' => $this->technician->id,
            ],
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount('repairs', 0);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(
        array $overrides = [],
    ): User {
        return User::create([
            'name' => 'User Test',
            'username' => 'user_' . bin2hex(random_bytes(4)),
            'email' =>
            bin2hex(random_bytes(4)) . '@example.com',
            'phone' =>
            '08' . random_int(1000000000, 9999999999),
            'password' => 'password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
            ...$overrides,
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createFreezer(
        array $overrides = [],
    ): Freezer {
        $freezer = Freezer::create([
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => 'SERIAL-REPAIR-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
            'photo_path' => null,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'complaint_note' => 'Freezer tidak dingin.',
            'created_by' => $this->admin->id,
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
            ...$overrides,
        ]);

        $freezer->forceFill([
            'freezer_code' => sprintf(
                'FZ-%05d',
                $freezer->id,
            ),
        ])->save();

        return $freezer->refresh();
    }

    private function createIntake(
        Freezer $freezer,
        VerificationStatus $status,
        mixed $receivedAt = null,
    ): ServiceIntake {
        $receivedAt ??= now();

        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' =>
            'TEMP-' . bin2hex(random_bytes(8)),
            'complaint_note' => 'Freezer tidak dingin.',
            'condition_note' => null,
            'status_verifikasi' => $status,
            'rejection_reason' =>
            $status === VerificationStatus::REJECTED
                ? 'Unit ditolak saat verifikasi.'
                : null,
            'received_by' => $this->admin->id,
            'received_at' => $receivedAt,
            'verified_by' =>
            $status === VerificationStatus::PENDING_ARRIVAL
                ? null
                : $this->admin->id,
            'verified_at' =>
            $status === VerificationStatus::PENDING_ARRIVAL
                ? null
                : $receivedAt,
            'completed_at' =>
            $status === VerificationStatus::REJECTED
                ? $receivedAt
                : null,
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
