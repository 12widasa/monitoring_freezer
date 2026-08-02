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

class RepairIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Repair Index',
            'username' => 'admin_repair_index',
            'email' => 'admin.repair.index@example.com',
            'phone' => '081234567821',
            'role' => UserRole::ADMIN,
        ]);

        $this->technician = $this->createUser([
            'name' => 'Teknisi Repair Index',
            'username' => 'technician_repair_index',
            'email' => 'technician.repair.index@example.com',
            'phone' => '081234567822',
            'role' => UserRole::TECHNICIAN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Repair Index',
            'username' => 'customer_repair_index',
            'email' => 'customer.repair.index@example.com',
            'phone' => '081234567823',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Repair Index',
            'phone' => '0247654322',
            'address' => 'Jl. Pengujian Repair Index No. 1',
        ]);
    }

    public function test_admin_can_open_repair_index_with_dynamic_repair_data(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Modena',
            'model' => 'MD-500',
            'serial_number' => 'REPAIR-INDEX-001',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: now(),
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: RepairStatus::QUEUED,
            technician: $this->technician,
            initialAnalysis: 'Menunggu pemeriksaan awal teknisi.',
        );

        $repairCode = sprintf(
            'TR-%s-%04d',
            $repair->created_at->format('Y'),
            $repair->id,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index'));

        $response
            ->assertOk()
            ->assertSee($repairCode)
            ->assertSee($freezer->freezer_code)
            ->assertSee($freezer->brand)
            ->assertSee($freezer->model)
            ->assertSee($this->customer->company_name)
            ->assertSee($this->technician->name)
            ->assertSee('Menunggu diperiksa')
            ->assertSee('Menunggu pemeriksaan awal teknisi.')
            ->assertSee(
                route('admin.repairs.show', $repair),
                false,
            );
    }

    public function test_repair_index_displays_newest_repair_first(): void
    {
        $oldFreezer = $this->createFreezer([
            'serial_number' => 'REPAIR-INDEX-OLD',
        ]);

        $oldIntake = $this->createIntake(
            freezer: $oldFreezer,
            receivedAt: now()->subDay(),
        );

        $oldRepair = $this->createRepair(
            freezer: $oldFreezer,
            intake: $oldIntake,
            status: RepairStatus::COMPLETED,
            technician: $this->technician,
            createdAt: now()->subDay(),
        );

        $newFreezer = $this->createFreezer([
            'serial_number' => 'REPAIR-INDEX-NEW',
        ]);

        $newIntake = $this->createIntake(
            freezer: $newFreezer,
            receivedAt: now(),
        );

        $newRepair = $this->createRepair(
            freezer: $newFreezer,
            intake: $newIntake,
            status: RepairStatus::REPAIRING,
            technician: $this->technician,
            createdAt: now(),
        );

        $oldRepairCode = $this->repairCode($oldRepair);
        $newRepairCode = $this->repairCode($newRepair);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index'));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                $newRepairCode,
                $oldRepairCode,
            ]);
    }

    public function test_unassigned_repair_displays_unassigned_status(): void
    {
        $freezer = $this->createFreezer([
            'serial_number' => 'REPAIR-INDEX-UNASSIGNED',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: now(),
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: RepairStatus::QUEUED,
            technician: null,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index'));

        $response
            ->assertOk()
            ->assertSee($this->repairCode($repair))
            ->assertSee('Belum ditugaskan');
    }

    public function test_repair_summary_matches_database_state(): void
    {
        $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: null,
            serialNumber: 'SUMMARY-UNASSIGNED',
        );

        $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'SUMMARY-QUEUED',
        );

        $this->createRepairForStatus(
            RepairStatus::INSPECTING,
            technician: $this->technician,
            serialNumber: 'SUMMARY-INSPECTING',
        );

        $this->createRepairForStatus(
            RepairStatus::REPAIRING,
            technician: $this->technician,
            serialNumber: 'SUMMARY-REPAIRING',
        );

        $this->createRepairForStatus(
            RepairStatus::COMPLETED,
            technician: $this->technician,
            serialNumber: 'SUMMARY-COMPLETED',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index'));

        $response
            ->assertOk()
            ->assertViewHas('repairSummary', [
                'active' => 4,
                'unassigned' => 1,
                'queued' => 1,
                'inspecting' => 1,
                'repairing' => 1,
            ]);
    }

    public function test_search_can_find_repair_by_display_code_and_id(): void
    {
        $freezer = $this->createFreezer([
            'serial_number' => 'SEARCH-REPAIR-CODE',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: now(),
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: RepairStatus::QUEUED,
            technician: $this->technician,
        );

        $otherFreezer = $this->createFreezer([
            'serial_number' => 'SEARCH-REPAIR-OTHER',
        ]);

        $otherIntake = $this->createIntake(
            freezer: $otherFreezer,
            receivedAt: now(),
        );

        $otherRepair = $this->createRepair(
            freezer: $otherFreezer,
            intake: $otherIntake,
            status: RepairStatus::QUEUED,
            technician: $this->technician,
        );

        $codeResponse = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'search' => $this->repairCode($repair),
            ]));

        $codeResponse
            ->assertOk()
            ->assertSee($this->repairCode($repair))
            ->assertDontSee($this->repairCode($otherRepair));

        $idResponse = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'search' => (string) $repair->id,
            ]));

        $idResponse
            ->assertOk()
            ->assertSee($this->repairCode($repair))
            ->assertDontSee($this->repairCode($otherRepair));
    }

    public function test_search_can_find_repair_by_freezer_and_customer_data(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'SearchBrandUnique',
            'model' => 'SearchModelUnique',
            'serial_number' => 'SEARCH-SERIAL-UNIQUE',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: now(),
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: RepairStatus::QUEUED,
            technician: $this->technician,
        );

        foreach (
            [
                $freezer->freezer_code,
                'SEARCH-SERIAL-UNIQUE',
                'SearchBrandUnique',
                'SearchModelUnique',
                $this->customer->company_name,
            ] as $search
        ) {
            $this
                ->actingAs($this->admin)
                ->get(route('admin.repairs.index', [
                    'search' => $search,
                ]))
                ->assertOk()
                ->assertSee($this->repairCode($repair));
        }
    }

    public function test_status_filter_only_displays_matching_repairs(): void
    {
        $queuedRepair = $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'FILTER-STATUS-QUEUED',
        );

        $repairingRepair = $this->createRepairForStatus(
            RepairStatus::REPAIRING,
            technician: $this->technician,
            serialNumber: 'FILTER-STATUS-REPAIRING',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'status' => RepairStatus::REPAIRING->value,
            ]));

        $response
            ->assertOk()
            ->assertSee($this->repairCode($repairingRepair))
            ->assertDontSee($this->repairCode($queuedRepair));
    }

    public function test_technician_filter_only_displays_assigned_repairs(): void
    {
        $otherTechnician = $this->createUser([
            'name' => 'Teknisi Filter Lain',
            'username' => 'technician_filter_other',
            'email' => 'technician.filter.other@example.com',
            'phone' => '081234567824',
            'role' => UserRole::TECHNICIAN,
        ]);

        $matchingRepair = $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'FILTER-TECH-MATCH',
        );

        $otherRepair = $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $otherTechnician,
            serialNumber: 'FILTER-TECH-OTHER',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'technician_id' => $this->technician->id,
            ]));

        $response
            ->assertOk()
            ->assertSee($this->repairCode($matchingRepair))
            ->assertDontSee($this->repairCode($otherRepair));
    }

    public function test_customer_filter_only_displays_customer_repairs(): void
    {
        $matchingRepair = $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'FILTER-CUSTOMER-MATCH',
        );

        $otherCustomerUser = $this->createUser([
            'name' => 'Pelanggan Filter Lain',
            'username' => 'customer_filter_other',
            'email' => 'customer.filter.other@example.com',
            'phone' => '081234567825',
            'role' => UserRole::CUSTOMER,
        ]);

        $otherCustomer = Customer::create([
            'user_id' => $otherCustomerUser->id,
            'company_name' => 'PT Pelanggan Filter Lain',
            'phone' => '0247654323',
            'address' => 'Jl. Pelanggan Filter Lain',
        ]);

        $otherFreezer = $this->createFreezer([
            'customer_id' => $otherCustomer->id,
            'serial_number' => 'FILTER-CUSTOMER-OTHER',
        ]);

        $otherIntake = $this->createIntake(
            freezer: $otherFreezer,
            receivedAt: now(),
        );

        $otherRepair = $this->createRepair(
            freezer: $otherFreezer,
            intake: $otherIntake,
            status: RepairStatus::QUEUED,
            technician: $this->technician,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'customer_id' => $this->customer->id,
            ]));

        $response
            ->assertOk()
            ->assertSee($this->repairCode($matchingRepair))
            ->assertDontSee($this->repairCode($otherRepair));
    }

    public function test_per_page_limits_repairs_and_preserves_query_string(): void
    {
        for ($index = 1; $index <= 12; $index++) {
            $this->createRepairForStatus(
                RepairStatus::QUEUED,
                technician: $this->technician,
                serialNumber: sprintf(
                    'PAGINATION-%02d',
                    $index,
                ),
            );
        }

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'status' => RepairStatus::QUEUED->value,
                'technician_id' => $this->technician->id,
                'customer_id' => $this->customer->id,
                'per_page' => 10,
            ]));

        $response
            ->assertOk()
            ->assertViewHas('repairs', function ($repairs): bool {
                return $repairs->count() === 10
                    && $repairs->total() === 12
                    && $repairs->perPage() === 10;
            })
            ->assertSee('Menampilkan')
            ->assertSee('1–10')
            ->assertSee('12');

        $repairs = $response->viewData('repairs');

        $this->assertStringContainsString(
            'status=' . RepairStatus::QUEUED->value,
            $repairs->nextPageUrl(),
        );

        $this->assertStringContainsString(
            'technician_id=' . $this->technician->id,
            $repairs->nextPageUrl(),
        );

        $this->assertStringContainsString(
            'customer_id=' . $this->customer->id,
            $repairs->nextPageUrl(),
        );

        $this->assertStringContainsString(
            'per_page=10',
            $repairs->nextPageUrl(),
        );
    }

    public function test_per_page_accepts_25_and_50(): void
    {
        foreach ([25, 50] as $perPage) {
            $response = $this
                ->actingAs($this->admin)
                ->get(route('admin.repairs.index', [
                    'per_page' => $perPage,
                ]));

            $response
                ->assertOk()
                ->assertViewHas(
                    'perPage',
                    $perPage,
                )
                ->assertViewHas(
                    'repairs',
                    fn($repairs): bool =>
                    $repairs->perPage() === $perPage,
                );
        }
    }

    public function test_invalid_per_page_falls_back_to_10(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'per_page' => 999,
            ]));

        $response
            ->assertOk()
            ->assertViewHas('perPage', 10)
            ->assertViewHas(
                'repairs',
                fn($repairs): bool =>
                $repairs->perPage() === 10,
            );
    }

    public function test_invalid_status_is_ignored(): void
    {
        $repair = $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'INVALID-STATUS-VISIBLE',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'status' => 'invalid-status',
            ]));

        $response
            ->assertOk()
            ->assertViewHas(
                'filters',
                fn(array $filters): bool =>
                $filters['status'] === '',
            )
            ->assertSee($this->repairCode($repair));
    }

    public function test_empty_filter_result_displays_empty_state(): void
    {
        $this->createRepairForStatus(
            RepairStatus::QUEUED,
            technician: $this->technician,
            serialNumber: 'EMPTY-FILTER-QUEUED',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.index', [
                'status' => RepairStatus::COMPLETED->value,
            ]));

        $response
            ->assertOk()
            ->assertSee('Belum ada tugas reparasi')
            ->assertSee(
                'Tugas reparasi yang dibuat akan muncul di sini.',
            )
            ->assertSee(
                'Tidak ada tugas reparasi untuk ditampilkan',
            );
    }

    public function test_guest_cannot_access_repair_index(): void
    {
        $this->get(route('admin.repairs.index'))
            ->assertRedirect(route('login'));
    }

    private function repairCode(Repair $repair): string
    {
        return sprintf(
            'TR-%s-%04d',
            $repair->created_at->format('Y'),
            $repair->id,
        );
    }

    private function createRepairForStatus(
        RepairStatus $status,
        ?User $technician,
        string $serialNumber,
    ): Repair {
        $freezer = $this->createFreezer([
            'serial_number' => $serialNumber,
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: now(),
        );

        return $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            status: $status,
            technician: $technician,
        );
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(array $overrides = []): User
    {
        return User::create([
            'name' => 'User Repair Index',
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
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'complaint_note' => 'Keluhan legacy freezer.',
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
        mixed $receivedAt,
    ): ServiceIntake {
        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' =>
            'TEMP-' . bin2hex(random_bytes(8)),
            'complaint_note' => 'Freezer tidak dingin.',
            'condition_note' => null,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'received_by' => $this->admin->id,
            'received_at' => $receivedAt,
            'verified_by' => $this->admin->id,
            'verified_at' => $receivedAt,
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

    private function createRepair(
        Freezer $freezer,
        ServiceIntake $intake,
        RepairStatus $status,
        ?User $technician,
        ?string $initialAnalysis = null,
        mixed $createdAt = null,
    ): Repair {
        $createdAt ??= now();

        $repair = Repair::create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => $technician?->id,
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
