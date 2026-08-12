<?php

namespace Tests\Feature\Admin;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Component;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairComponent;
use App\Models\RepairLog;
use App\Models\RepairLogPhoto;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepairShowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Repair Show',
            'username' => 'admin_repair_show',
            'email' => 'admin.repair.show@example.com',
            'phone' => '081234567831',
            'role' => UserRole::ADMIN,
        ]);

        $this->technician = $this->createUser([
            'name' => 'Teknisi Repair Show',
            'username' => 'technician_repair_show',
            'email' => 'technician.repair.show@example.com',
            'phone' => '081234567832',
            'role' => UserRole::TECHNICIAN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Repair Show',
            'username' => 'customer_repair_show',
            'email' => 'customer.repair.show@example.com',
            'phone' => '081234567833',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Repair Show',
            'phone' => '0247654333',
            'address' => 'Jl. Pengujian Repair Show No. 1',
        ]);
    }

    public function test_admin_can_open_repair_show_with_dynamic_data(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Polytron',
            'model' => 'SCN-450',
            'serial_number' => 'REPAIR-SHOW-DYNAMIC-001',
            'capacity_liter' => 450,
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            complaintNote: 'Freezer hidup tetapi tidak menghasilkan suhu dingin.',
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: $this->technician,
            status: RepairStatus::INSPECTING,
            initialAnalysis: 'Kemungkinan terdapat masalah pada sistem refrigerasi.',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertViewIs('admin.repairs.show')
            ->assertViewHas(
                'repair',
                fn(Repair $viewRepair): bool =>
                $viewRepair->is($repair)
                    && $viewRepair->relationLoaded('freezer')
                    && $viewRepair->relationLoaded('serviceIntake')
                    && $viewRepair->relationLoaded('technician')
                    && $viewRepair->relationLoaded('admin')
                    && $viewRepair->relationLoaded('logs')
                    && $viewRepair->relationLoaded('components'),
            )
            ->assertSee($this->repairCode($repair))
            ->assertSee($freezer->freezer_code)
            ->assertSee($freezer->brand)
            ->assertSee($freezer->model)
            ->assertSee($freezer->serial_number)
            ->assertSee($this->customer->company_name)
            ->assertSee($this->admin->name)
            ->assertSee($this->technician->name)
            ->assertSee($repair->status->label())
            ->assertSee($repair->initial_analysis)
            ->assertSee($intake->complaint_note);
    }

    public function test_assigned_repair_displays_current_technician_and_reassignment_trigger(): void
    {
        $freezer = $this->createFreezer();

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: $this->technician,
            status: RepairStatus::REPAIRING,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertSee($this->technician->name)
            ->assertSee('Ganti Teknisi')
            ->assertSee(
                'data-current-technician-id="' .
                    $this->technician->id .
                    '"',
                false,
            )
            ->assertSee(
                'data-current-technician-name="' .
                    $this->technician->name .
                    '"',
                false,
            )
            ->assertSee(
                'data-update-url="' .
                    route(
                        'admin.repairs.technician.update',
                        $repair,
                    ) .
                    '"',
                false,
            );
    }

    public function test_unassigned_repair_displays_unassigned_state_and_assignment_trigger(): void
    {
        $freezer = $this->createFreezer([
            'serial_number' => 'REPAIR-SHOW-UNASSIGNED',
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: null,
            status: RepairStatus::QUEUED,
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertSee('Belum ditugaskan')
            ->assertSee('Tetapkan Teknisi')
            ->assertSee(
                'data-current-technician-id=""',
                false,
            )
            ->assertSee(
                'data-current-technician-name=""',
                false,
            )
            ->assertSee('Belum ada riwayat perkembangan');
    }

    public function test_repair_show_loads_progress_logs_in_newest_first_order(): void
    {
        $freezer = $this->createFreezer();

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: $this->technician,
            status: RepairStatus::REPAIRING,
        );

        $oldDescription =
            'Pemeriksaan awal sistem kelistrikan dilakukan.';

        $newDescription =
            'Kompresor diuji dan tekanan refrigeran diperiksa.';

        $this->createLog(
            repair: $repair,
            description: $oldDescription,
            createdAt: now()->subHour(),
        );

        $this->createLog(
            repair: $repair,
            description: $newDescription,
            createdAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertViewHas(
                'repair',
                fn(Repair $viewRepair): bool =>
                $viewRepair->logs
                    ->pluck('description')
                    ->values()
                    ->all() === [
                        $newDescription,
                        $oldDescription,
                    ],
            )
            ->assertSee($newDescription)
            ->assertSee($oldDescription)
            ->assertSee($this->technician->name);
    }

    public function test_repair_show_displays_technician_reassignment_log(): void
    {
        $oldTechnician = $this->createUser([
            'name' => 'Teknisi Lama Repair Show',
            'role' => UserRole::TECHNICIAN,
        ]);

        $freezer = $this->createFreezer();

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: $this->technician,
            status: RepairStatus::REPAIRING,
        );

        $reason =
            'Teknisi sebelumnya sakit dan tidak dapat melanjutkan pekerjaan.';

        $description = sprintf(
            'Penugasan dialihkan dari %s kepada %s. Alasan: %s',
            $oldTechnician->name,
            $this->technician->name,
            $reason,
        );

        $this->createLog(
            repair: $repair,
            description: $description,
            createdAt: now(),
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertSee('Riwayat Perkembangan')
            ->assertSee($description)
            ->assertSee('Oleh ' . $this->admin->name);
    }

    public function test_repair_show_displays_documentation_and_components(): void
    {
        $freezerPhotoPath =
            'freezers/repair-show-freezer.jpg';

        $logPhotoPath =
            'repairs/repair-show-progress.jpg';

        $freezer = $this->createFreezer([
            'photo_path' => $freezerPhotoPath,
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: $this->technician,
            status: RepairStatus::REPAIRING,
        );

        $log = $this->createLog(
            repair: $repair,
            description: 'Dokumentasi pemeriksaan kompresor.',
            createdAt: now(),
        );

        RepairLogPhoto::create([
            'repair_log_id' => $log->id,
            'photo_path' => $logPhotoPath,
            'sort_order' => 1,
            'created_at' => now(),
        ]);

        $component = Component::create([
            'name' => 'Kapasitor Kompresor Repair Show',
            'part_number' => 'CAP-RS-001',
            'unit' => 'pcs',
        ]);

        RepairComponent::create([
            'repair_id' => $repair->id,
            'component_id' => $component->id,
            'quantity' => 2,
            'status' => 'requested',
            'note' => 'Diperlukan untuk pengujian kompresor.',
            'added_by' => $this->admin->id,
            'installed_by' => null,
            'installed_at' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.repairs.show', $repair));

        $response
            ->assertOk()
            ->assertViewHas(
                'repair',
                fn(Repair $viewRepair): bool =>
                $viewRepair->components->count() === 1
                    && $viewRepair->components
                    ->first()
                    ?->relationLoaded('component')
                    && $viewRepair->logs
                    ->first()
                    ?->photos
                    ->count() === 1,
            )
            ->assertSee('Dokumentasi')
            ->assertSee('Foto Freezer')
            ->assertSee(
                Storage::url($freezerPhotoPath),
                false,
            )
            ->assertSee(
                Storage::url($logPhotoPath),
                false,
            )
            ->assertSee($log->description)
            ->assertSee('Komponen Reparasi')
            ->assertSee($component->name)
            ->assertSee($component->part_number);
    }

    public function test_guest_cannot_access_repair_show(): void
    {
        $freezer = $this->createFreezer();

        $intake = $this->createIntake(
            freezer: $freezer,
        );

        $repair = $this->createRepair(
            freezer: $freezer,
            intake: $intake,
            technician: null,
            status: RepairStatus::QUEUED,
        );

        $this->get(route('admin.repairs.show', $repair))
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(
        array $overrides = [],
    ): User {
        return User::create([
            'name' => 'User Repair Show',
            'username' =>
            'repair_show_' . bin2hex(random_bytes(4)),
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
            'serial_number' =>
            'SERIAL-SHOW-' . bin2hex(random_bytes(5)),
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
        string $complaintNote = 'Freezer tidak dingin.',
    ): ServiceIntake {
        $receivedAt = now()
            ->subDay()
            ->startOfSecond();

        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' =>
            'TEMP-' . bin2hex(random_bytes(8)),
            'complaint_note' => $complaintNote,
            'condition_note' =>
            'Kondisi fisik telah diperiksa.',
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
        ?User $technician,
        RepairStatus $status,
        ?string $initialAnalysis = null,
    ): Repair {
        return Repair::create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => $technician?->id,
            'admin_id' => $this->admin->id,
            'status' => $status,
            'initial_analysis' => $initialAnalysis,
        ]);
    }

    private function createLog(
        Repair $repair,
        string $description,
        mixed $createdAt,
    ): RepairLog {
        return RepairLog::create([
            'repair_id' => $repair->id,
            'status' => $repair->status,
            'description' => $description,
            'updated_by' => $this->admin->id,
            'created_at' => $createdAt,
        ]);
    }
}
