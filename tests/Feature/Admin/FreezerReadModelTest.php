<?php

namespace Tests\Feature\Admin;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreezerReadModelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Read Model',
            'username' => 'admin_read_model',
            'email' => 'admin.read.model@example.com',
            'phone' => '081234567810',
            'role' => UserRole::ADMIN,
        ]);

        $this->technician = $this->createUser([
            'name' => 'Teknisi Read Model',
            'username' => 'technician_read_model',
            'email' => 'technician.read.model@example.com',
            'phone' => '081234567811',
            'role' => UserRole::TECHNICIAN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Read Model',
            'username' => 'customer_read_model',
            'email' => 'customer.read.model@example.com',
            'phone' => '081234567812',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Read Model',
            'phone' => '0247654321',
            'address' => 'Jl. Pengujian Read Model No. 1',
        ]);
    }

    public function test_index_can_be_opened_with_latest_intake_relation(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Modena',
            'model' => 'MD-500',
            'serial_number' => 'READ-INDEX-001',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Keluhan terbaru untuk halaman index.',
            receivedAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.index'));

        $response
            ->assertOk()
            ->assertSee($freezer->freezer_code)
            ->assertSee('Sudah diverifikasi');

        $this->assertSame(
            $intake->id,
            $freezer->fresh()
                ->latestIntake()
                ->value('id'),
        );
    }

    public function test_verification_filter_uses_latest_intake_status(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'GEA',
            'model' => 'AB-600',
            'serial_number' => 'READ-FILTER-001',
        ]);

        $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::REJECTED,
            complaintNote: 'Keluhan penerimaan lama.',
            receivedAt: now()->subDay(),
        );

        $latestIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Keluhan penerimaan terbaru.',
            receivedAt: now(),
        );

        $verifiedResponse = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.index', [
                'verification_status' =>
                VerificationStatus::VERIFIED->value,
            ]));

        $verifiedResponse
            ->assertOk()
            ->assertSee($freezer->freezer_code);

        $rejectedResponse = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.index', [
                'verification_status' =>
                VerificationStatus::REJECTED->value,
            ]));

        $rejectedResponse
            ->assertOk()
            ->assertDontSee($freezer->freezer_code);

        $this->assertSame(
            VerificationStatus::VERIFIED,
            $latestIntake->status_verifikasi,
        );
    }

    public function test_pending_filter_uses_latest_intake_status(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Polytron',
            'model' => 'SCN-300',
            'serial_number' => 'READ-FILTER-002',
        ]);

        $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Penerimaan lama yang sudah diverifikasi.',
            receivedAt: now()->subDay(),
        );

        $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::PENDING_ARRIVAL,
            complaintNote: 'Penerimaan terbaru menunggu verifikasi.',
            receivedAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.index', [
                'verification_status' =>
                VerificationStatus::PENDING_ARRIVAL->value,
            ]));

        $response
            ->assertOk()
            ->assertSee($freezer->freezer_code)
            ->assertSee('Belum diverifikasi');
    }

    public function test_detail_displays_latest_intake_data(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Aqua',
            'model' => 'AQF-400',
            'serial_number' => 'READ-DETAIL-001',
        ]);

        $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::REJECTED,
            complaintNote: 'Keluhan penerimaan lama.',
            receivedAt: now()->subDay(),
            rejectionReason: 'Alasan penolakan penerimaan lama.',
        );

        $latestIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'KELUHAN LATEST INTAKE',
            receivedAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.show', $freezer));

        $response
            ->assertOk()
            ->assertSee($latestIntake->intake_code)
            ->assertSee('KELUHAN LATEST INTAKE')
            ->assertSee('Sudah diverifikasi');
    }

    public function test_detail_displays_all_service_intakes_newest_first(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Sharp',
            'model' => 'FRV-250',
            'serial_number' => 'READ-INTAKES-001',
        ]);

        $oldestIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::REJECTED,
            complaintNote: 'Keluhan penerimaan paling lama.',
            receivedAt: now()->subDays(2),
            rejectionReason: 'Nomor seri tidak sesuai.',
        );

        $middleIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Keluhan penerimaan kedua.',
            receivedAt: now()->subDay(),
        );

        $latestIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::PENDING_ARRIVAL,
            complaintNote: 'Keluhan penerimaan terbaru.',
            receivedAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.show', $freezer));

        $response
            ->assertOk()
            ->assertSee('3 penerimaan')
            ->assertSeeInOrder([
                $latestIntake->intake_code,
                $middleIntake->intake_code,
                $oldestIntake->intake_code,
            ])
            ->assertSee('Keluhan penerimaan terbaru.')
            ->assertSee('Keluhan penerimaan kedua.')
            ->assertSee('Keluhan penerimaan paling lama.')
            ->assertSee('Nomor seri tidak sesuai.');
    }

    public function test_detail_displays_repair_link_for_related_intake(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'RSA',
            'model' => 'CF-300',
            'serial_number' => 'READ-INTAKE-REPAIR-001',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Freezer tidak dingin.',
            receivedAt: now(),
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: RepairStatus::QUEUED,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.show', $freezer));

        $response
            ->assertOk()
            ->assertSee("Lihat reparasi #{$repair->id}")
            ->assertSee(
                route('admin.repairs.show', $repair),
                false,
            );
    }

    public function test_detail_displays_all_repairs_newest_first(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Midea',
            'model' => 'HS-400',
            'serial_number' => 'READ-REPAIRS-001',
        ]);

        $oldIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Keluhan reparasi lama.',
            receivedAt: now()->subDays(2),
        );

        $oldRepair = $this->createRepair(
            freezer: $freezer,
            intake: $oldIntake,
            status: RepairStatus::COMPLETED,
            initialAnalysis: 'ANALISIS REPARASI LAMA',
            createdAt: now()->subDays(2),
        );

        $latestIntake = $this->createIntake(
            freezer: $freezer,
            status: VerificationStatus::VERIFIED,
            complaintNote: 'Keluhan reparasi terbaru.',
            receivedAt: now(),
        );

        $latestRepair = $this->createRepair(
            freezer: $freezer,
            intake: $latestIntake,
            status: RepairStatus::REPAIRING,
            initialAnalysis: 'ANALISIS REPARASI TERBARU',
            createdAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.show', $freezer));

        $response
            ->assertOk()
            ->assertSee('2 reparasi')
            ->assertSeeInOrder([
                "Reparasi #{$latestRepair->id}",
                "Reparasi #{$oldRepair->id}",
            ])
            ->assertSee($latestIntake->intake_code)
            ->assertSee($oldIntake->intake_code)
            ->assertSee('ANALISIS REPARASI TERBARU')
            ->assertSee('ANALISIS REPARASI LAMA')
            ->assertSee('Sedang diperbaiki')
            ->assertSee('Perbaikan selesai');
    }

    public function test_detail_displays_empty_states_when_there_are_no_intakes_or_repairs(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Sanken',
            'model' => 'SN-200',
            'serial_number' => 'READ-EMPTY-001',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.freezers.show', $freezer));

        $response
            ->assertOk()
            ->assertSee('Penerimaan tidak tersedia')
            ->assertSee('Belum ada riwayat penerimaan')
            ->assertSee('Belum ada riwayat reparasi')
            ->assertSee('0 penerimaan')
            ->assertSee('0 reparasi');
    }

    public function test_guest_cannot_access_freezer_read_model_pages(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'GEA',
            'model' => 'AB-100',
            'serial_number' => 'READ-GUEST-001',
        ]);

        $this->get(route('admin.freezers.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.freezers.show', $freezer))
            ->assertRedirect(route('login'));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(array $overrides = []): User
    {
        return User::create([
            'name' => 'User Read Model',
            'username' => 'user_' . bin2hex(random_bytes(4)),
            'email' => bin2hex(random_bytes(4)) . '@example.com',
            'phone' => '08' . random_int(1000000000, 9999999999),
            'password' => 'password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
            ...$overrides,
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createFreezer(array $overrides = []): Freezer
    {
        $freezer = Freezer::create([
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' =>
            'SERIAL-' . bin2hex(random_bytes(5)),
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

    private function createIntake(
        Freezer $freezer,
        VerificationStatus $status,
        string $complaintNote,
        mixed $receivedAt,
        ?string $rejectionReason = null,
        ?string $conditionNote = null,
    ): ServiceIntake {
        $verified = $status !==
            VerificationStatus::PENDING_ARRIVAL;

        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' =>
            'TEMP-' . bin2hex(random_bytes(8)),
            'complaint_note' => $complaintNote,
            'condition_note' => $conditionNote,
            'status_verifikasi' => $status,
            'rejection_reason' =>
            $status === VerificationStatus::REJECTED
                ? ($rejectionReason
                    ?? 'Penerimaan ditolak.')
                : null,
            'received_by' => $this->admin->id,
            'received_at' => $receivedAt,
            'verified_by' =>
            $verified
                ? $this->admin->id
                : null,
            'verified_at' =>
            $verified
                ? $receivedAt
                : null,
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

    private function createRepair(
        Freezer $freezer,
        ServiceIntake $intake,
        RepairStatus $status,
        ?string $initialAnalysis = null,
        mixed $createdAt = null,
    ): Repair {
        $createdAt ??= now();

        $repair = Repair::create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => $this->technician->id,
            'admin_id' => $this->admin->id,
            'status' => $status,
            'initial_analysis' => $initialAnalysis,
        ]);

        $repair->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $repair->refresh();
    }
}
