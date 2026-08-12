<?php

namespace Tests\Feature\Admin;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateRepairTechnicianTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $oldTechnician;

    private User $newTechnician;

    private Customer $customer;

    private Freezer $freezer;

    private ServiceIntake $serviceIntake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Assignment',
            'username' => 'admin_assignment_test',
            'email' => 'admin.assignment@example.com',
            'phone' => '081234567811',
            'role' => UserRole::ADMIN,
        ]);

        $this->oldTechnician = $this->createUser([
            'name' => 'Teknisi Lama',
            'username' => 'old_technician_test',
            'email' => 'old.technician@example.com',
            'phone' => '081234567812',
            'role' => UserRole::TECHNICIAN,
        ]);

        $this->newTechnician = $this->createUser([
            'name' => 'Teknisi Baru',
            'username' => 'new_technician_test',
            'email' => 'new.technician@example.com',
            'phone' => '081234567813',
            'role' => UserRole::TECHNICIAN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Assignment',
            'username' => 'customer_assignment_test',
            'email' => 'customer.assignment@example.com',
            'phone' => '081234567814',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Assignment',
            'phone' => '0241234599',
            'address' => 'Jl. Pengujian Assignment No. 1',
        ]);

        $this->freezer = $this->createFreezer();

        $this->serviceIntake = $this->createVerifiedIntake(
            $this->freezer,
        );
    }

    public function test_admin_can_assign_technician_to_unassigned_repair(): void
    {
        $repair = $this->createRepair();

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->newTechnician->id,
                    'reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHas(
                'success',
                'Teknisi berhasil ditetapkan.',
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('repairs', [
            'id' => $repair->id,
            'technician_id' => $this->newTechnician->id,
            'status' => RepairStatus::QUEUED->value,
        ]);

        $this->assertDatabaseHas('repair_assignments', [
            'repair_id' => $repair->id,
            'technician_id' => $this->newTechnician->id,
            'assigned_by' => $this->admin->id,
            'ended_at' => null,
            'ended_by' => null,
            'end_reason' => null,
        ]);

        $this->assertDatabaseHas('repair_logs', [
            'repair_id' => $repair->id,
            'status' => RepairStatus::QUEUED->value,
            'description' =>
            'Teknisi Teknisi Baru ditetapkan untuk menangani tugas reparasi.',
            'updated_by' => $this->admin->id,
        ]);

        $this->assertSame(
            1,
            RepairAssignment::query()
                ->where('repair_id', $repair->id)
                ->whereNull('ended_at')
                ->count(),
        );
    }

    public function test_admin_can_replace_technician_with_reason(): void
    {
        $repair = $this->createRepair([
            'technician_id' => $this->oldTechnician->id,
            'status' => RepairStatus::REPAIRING,
        ]);

        $oldAssignment = $this->createAssignment(
            repair: $repair,
            technician: $this->oldTechnician,
        );

        $reason =
            'Teknisi sebelumnya sakit dan tidak dapat melanjutkan pekerjaan.';

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->newTechnician->id,
                    'reason' => $reason,
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHas(
                'success',
                'Teknisi berhasil diganti.',
            )
            ->assertSessionHasNoErrors();

        $oldAssignment->refresh();
        $repair->refresh();

        $this->assertNotNull($oldAssignment->ended_at);

        $this->assertTrue(
            $oldAssignment->ended_at->greaterThanOrEqualTo(
                $oldAssignment->assigned_at,
            ),
            'Waktu berakhir assignment tidak boleh lebih awal dari waktu mulai.',
        );

        $this->assertSame(
            $this->admin->id,
            $oldAssignment->ended_by,
        );

        $this->assertSame(
            $reason,
            $oldAssignment->end_reason,
        );

        $this->assertSame(
            $this->newTechnician->id,
            $repair->technician_id,
        );

        $this->assertSame(
            RepairStatus::REPAIRING,
            $repair->status,
        );

        $newAssignment = RepairAssignment::query()
            ->where('repair_id', $repair->id)
            ->where('technician_id', $this->newTechnician->id)
            ->whereNull('ended_at')
            ->firstOrFail();

        $this->assertSame(
            $this->admin->id,
            $newAssignment->assigned_by,
        );

        $this->assertNull($newAssignment->ended_at);
        $this->assertNull($newAssignment->ended_by);
        $this->assertNull($newAssignment->end_reason);

        $this->assertTrue(
            $newAssignment->assigned_at->greaterThanOrEqualTo(
                $oldAssignment->ended_at,
            ),
            'Assignment baru tidak boleh dimulai sebelum assignment lama berakhir.',
        );

        $this->assertSame(
            1,
            RepairAssignment::query()
                ->where('repair_id', $repair->id)
                ->whereNull('ended_at')
                ->count(),
        );

        $this->assertDatabaseHas('repair_logs', [
            'repair_id' => $repair->id,
            'status' => RepairStatus::REPAIRING->value,
            'description' => sprintf(
                'Penugasan dialihkan dari %s kepada %s. Alasan: %s',
                $this->oldTechnician->name,
                $this->newTechnician->name,
                $reason,
            ),
            'updated_by' => $this->admin->id,
        ]);
    }

    public function test_replacement_requires_reason(): void
    {
        $repair = $this->createRepair([
            'technician_id' => $this->oldTechnician->id,
        ]);

        $assignment = $this->createAssignment(
            repair: $repair,
            technician: $this->oldTechnician,
        );

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->newTechnician->id,
                    'reason' => '   ',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHasErrors(
                ['reason'],
                null,
                'updateRepairTechnician',
            );

        $this->assertAssignmentUnchanged(
            repair: $repair,
            assignment: $assignment,
        );
    }

    public function test_current_technician_cannot_be_selected_again(): void
    {
        $repair = $this->createRepair([
            'technician_id' => $this->oldTechnician->id,
        ]);

        $assignment = $this->createAssignment(
            repair: $repair,
            technician: $this->oldTechnician,
        );

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->oldTechnician->id,
                    'reason' => 'Percobaan memilih teknisi sama.',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'updateRepairTechnician',
            );

        $this->assertAssignmentUnchanged(
            repair: $repair,
            assignment: $assignment,
        );
    }

    public function test_selected_user_must_be_a_technician(): void
    {
        $repair = $this->createRepair();

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->admin->id,
                    'reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'updateRepairTechnician',
            );

        $this->assertNull($repair->refresh()->technician_id);
        $this->assertDatabaseCount('repair_assignments', 0);
        $this->assertDatabaseCount('repair_logs', 0);
    }

    public function test_inactive_technician_cannot_be_assigned(): void
    {
        $repair = $this->createRepair();

        $this->newTechnician->update([
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => $this->newTechnician->id,
                    'reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'updateRepairTechnician',
            );

        $this->assertNull($repair->refresh()->technician_id);
        $this->assertDatabaseCount('repair_assignments', 0);
        $this->assertDatabaseCount('repair_logs', 0);
    }

    public function test_technician_is_required(): void
    {
        $repair = $this->createRepair();

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.repairs.technician.update',
                    $repair,
                ),
                [
                    'technician_id' => '',
                    'reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.repairs.show', $repair),
            )
            ->assertSessionHasErrors(
                ['technician_id'],
                null,
                'updateRepairTechnician',
            );

        $this->assertNull($repair->refresh()->technician_id);
        $this->assertDatabaseCount('repair_assignments', 0);
        $this->assertDatabaseCount('repair_logs', 0);
    }

    public function test_guest_cannot_update_repair_technician(): void
    {
        $repair = $this->createRepair();

        $response = $this->patch(
            route(
                'admin.repairs.technician.update',
                $repair,
            ),
            [
                'technician_id' => $this->newTechnician->id,
                'reason' => '',
            ],
        );

        $response->assertRedirect(route('login'));

        $this->assertNull($repair->refresh()->technician_id);
        $this->assertDatabaseCount('repair_assignments', 0);
        $this->assertDatabaseCount('repair_logs', 0);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(
        array $overrides = [],
    ): User {
        return User::create([
            'name' => 'User Assignment Test',
            'username' =>
            'assignment_' . bin2hex(random_bytes(4)),
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
            'serial_number' => 'SERIAL-ASSIGNMENT-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
            'photo_path' => null,
            'created_by' => $this->admin->id,
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function createRepair(
        array $overrides = [],
    ): Repair {
        return Repair::create([
            'freezer_id' => $this->freezer->id,
            'service_intake_id' =>
            $this->serviceIntake->id,
            'technician_id' => null,
            'admin_id' => $this->admin->id,
            'status' => RepairStatus::QUEUED,
            'initial_analysis' => null,
            ...$overrides,
        ]);
    }

    private function createAssignment(
        Repair $repair,
        User $technician,
    ): RepairAssignment {
        return RepairAssignment::create([
            'repair_id' => $repair->id,
            'technician_id' => $technician->id,
            'assigned_by' => $this->admin->id,
            'assigned_at' => now()->subDay(),
            'ended_at' => null,
            'ended_by' => null,
            'end_reason' => null,
        ]);
    }

    private function assertAssignmentUnchanged(
        Repair $repair,
        RepairAssignment $assignment,
    ): void {
        $repair->refresh();
        $assignment->refresh();

        $this->assertSame(
            $this->oldTechnician->id,
            $repair->technician_id,
        );

        $this->assertNull($assignment->ended_at);
        $this->assertNull($assignment->ended_by);
        $this->assertNull($assignment->end_reason);

        $this->assertSame(
            1,
            RepairAssignment::query()
                ->where('repair_id', $repair->id)
                ->count(),
        );

        $this->assertDatabaseCount('repair_logs', 0);
    }
}
