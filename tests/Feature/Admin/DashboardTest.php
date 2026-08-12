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
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser([
            'name' => 'Administrator Dashboard',
            'username' => 'admin_dashboard',
            'email' => 'admin.dashboard@example.com',
            'phone' => '081234567831',
            'role' => UserRole::ADMIN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Dashboard',
            'username' => 'customer_dashboard',
            'email' => 'customer.dashboard@example.com',
            'phone' => '081234567832',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Dashboard',
            'phone' => '0247654331',
            'address' => 'Jl. Pengujian Dashboard No. 1',
        ]);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_open_dashboard_with_empty_summary(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('summary', [
                'active_repairs' => 0,
                'total_freezers' => 0,
                'freezers_created_this_month' => 0,
                'queued_repairs' => 0,
                'queued_created_today' => 0,
                'repairing_repairs' => 0,
                'repairing_updated_today' => 0,
                'completed_this_month' => 0,
                'completed_this_week' => 0,
            ]);
    }

    public function test_dashboard_displays_dynamic_summary_counts(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 03:00:00',
                'UTC',
            ),
        );

        /*
         * Waktu lokal pengujian:
         * 12 Agustus 2026, 10.00 WIB.
         */

        $this->createFreezer([
            'serial_number' => 'DASHBOARD-FREEZER-MONTH',
            'created_at' => '2026-08-02 03:00:00',
            'updated_at' => '2026-08-02 03:00:00',
        ]);

        $this->createFreezer([
            'serial_number' => 'DASHBOARD-FREEZER-PREVIOUS',
            'created_at' => '2026-07-20 03:00:00',
            'updated_at' => '2026-07-20 03:00:00',
        ]);

        $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'DASHBOARD-QUEUED-TODAY',
            createdAt: '2026-08-12 01:00:00',
            updatedAt: '2026-08-12 01:00:00',
        );

        $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'DASHBOARD-QUEUED-YESTERDAY',
            createdAt: '2026-08-10 03:00:00',
            updatedAt: '2026-08-10 03:00:00',
        );

        $this->createRepair(
            status: RepairStatus::INSPECTING,
            serialNumber: 'DASHBOARD-INSPECTING',
            createdAt: '2026-08-09 03:00:00',
            updatedAt: '2026-08-11 03:00:00',
        );

        $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'DASHBOARD-REPAIRING-TODAY',
            createdAt: '2026-08-08 03:00:00',
            updatedAt: '2026-08-12 02:00:00',
        );

        $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'DASHBOARD-REPAIRING-YESTERDAY',
            createdAt: '2026-08-07 03:00:00',
            updatedAt: '2026-08-10 03:00:00',
        );

        $this->createRepair(
            status: RepairStatus::COMPLETED,
            serialNumber: 'DASHBOARD-COMPLETED-WEEK',
            createdAt: '2026-08-01 03:00:00',
            updatedAt: '2026-08-11 03:00:00',
        );

        $this->createRepair(
            status: RepairStatus::COMPLETED,
            serialNumber: 'DASHBOARD-COMPLETED-MONTH',
            createdAt: '2026-07-20 03:00:00',
            updatedAt: '2026-08-02 03:00:00',
        );

        $this->createRepair(
            status: RepairStatus::COMPLETED,
            serialNumber: 'DASHBOARD-COMPLETED-PREVIOUS',
            createdAt: '2026-07-01 03:00:00',
            updatedAt: '2026-07-20 03:00:00',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('summary', [
                'active_repairs' => 5,
                'total_freezers' => 10,
                'freezers_created_this_month' => 7,
                'queued_repairs' => 2,
                'queued_created_today' => 1,
                'repairing_repairs' => 2,
                'repairing_updated_today' => 1,
                'completed_this_month' => 2,
                'completed_this_week' => 1,
            ]);
    }

    public function test_dashboard_periods_follow_asia_jakarta_timezone(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-01 17:30:00',
                'UTC',
            ),
        );

        /*
         * UTC:
         * 1 Agustus 2026, 17.30
         *
         * WIB:
         * 2 Agustus 2026, 00.30
         */

        $insideToday = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'DASHBOARD-WIB-INSIDE',
            createdAt: '2026-08-01 17:15:00',
            updatedAt: '2026-08-01 17:15:00',
        );

        $outsideToday = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'DASHBOARD-WIB-OUTSIDE',
            createdAt: '2026-08-01 16:59:59',
            updatedAt: '2026-08-01 16:59:59',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'summary',
                function (array $summary): bool {
                    return $summary['queued_repairs'] === 2
                        && $summary['queued_created_today'] === 1;
                },
            );

        $this->assertTrue(
            $insideToday->created_at->greaterThan(
                $outsideToday->created_at,
            ),
        );
    }

    public function test_dashboard_contains_quick_action_links(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee(
                route('admin.freezers.index'),
                false,
            )
            ->assertSee(
                route('admin.repairs.index'),
                false,
            )
            ->assertSee(
                route('admin.users.index'),
                false,
            );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_technician_cannot_open_admin_dashboard(): void
    {
        $technician = $this->createUser([
            'username' => 'technician_dashboard',
            'email' => 'technician.dashboard@example.com',
            'phone' => '081234567833',
            'role' => UserRole::TECHNICIAN,
        ]);

        $this
            ->actingAs($technician)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_customer_cannot_open_admin_dashboard(): void
    {
        $customer = $this->customer->user;

        $this
            ->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_displays_four_latest_freezers_in_order(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 03:00:00',
                'UTC',
            ),
        );

        $freezers = collect();

        for ($index = 1; $index <= 5; $index++) {
            $freezers->push(
                $this->createFreezer([
                    'brand' => 'Dashboard Brand ' . $index,
                    'model' => 'Model ' . $index,
                    'serial_number' => sprintf(
                        'DASHBOARD-LATEST-%02d',
                        $index,
                    ),
                    'created_at' => CarbonImmutable::parse(
                        sprintf(
                            '2026-08-%02d 03:00:00',
                            $index,
                        ),
                        'UTC',
                    ),
                    'updated_at' => CarbonImmutable::parse(
                        sprintf(
                            '2026-08-%02d 03:00:00',
                            $index,
                        ),
                        'UTC',
                    ),
                ]),
            );
        }

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'latestFreezers',
                function ($latestFreezers) use ($freezers): bool {
                    return $latestFreezers->count() === 4
                        && $latestFreezers->pluck('id')->all() === [
                            $freezers[4]->id,
                            $freezers[3]->id,
                            $freezers[2]->id,
                            $freezers[1]->id,
                        ];
                },
            )
            ->assertSeeInOrder([
                'DASHBOARD-LATEST-05',
                'DASHBOARD-LATEST-04',
                'DASHBOARD-LATEST-03',
                'DASHBOARD-LATEST-02',
            ])
            ->assertDontSee('DASHBOARD-LATEST-01');
    }

    public function test_dashboard_displays_latest_freezer_empty_state(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'latestFreezers',
                fn($latestFreezers): bool =>
                $latestFreezers->isEmpty(),
            )
            ->assertSee('Belum ada freezer')
            ->assertSee(
                'Freezer yang ditambahkan akan muncul di bagian ini.',
            );
    }

    public function test_latest_freezer_card_displays_dynamic_data_and_detail_link(): void
    {
        $freezer = $this->createFreezer([
            'brand' => 'Latest Dashboard Brand',
            'model' => 'Latest Dashboard Model',
            'serial_number' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee($freezer->freezer_code)
            ->assertSee('Latest Dashboard Brand')
            ->assertSee('Latest Dashboard Model')
            ->assertSee('No. seri:')
            ->assertSee('—')
            ->assertSee($this->customer->company_name)
            ->assertSee(
                route('admin.freezers.show', $freezer),
                false,
            )
            ->assertSee(
                route('admin.freezers.index'),
                false,
            );
    }

    public function test_dashboard_displays_four_latest_repair_activities_in_order(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 03:00:00',
                'UTC',
            ),
        );

        $repair = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'DASHBOARD-ACTIVITY-REPAIR',
            createdAt: '2026-08-01 03:00:00',
            updatedAt: '2026-08-01 03:00:00',
        );

        $activities = collect();

        for ($index = 1; $index <= 5; $index++) {
            $activities->push(
                RepairLog::create([
                    'repair_id' => $repair->id,
                    'status' => RepairStatus::QUEUED,
                    'description' =>
                    'Aktivitas dashboard nomor ' . $index,
                    'updated_by' => $this->admin->id,
                    'created_at' => CarbonImmutable::parse(
                        sprintf(
                            '2026-08-12 0%d:00:00',
                            $index,
                        ),
                        'UTC',
                    ),
                ]),
            );
        }

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'latestActivities',
                function ($latestActivities) use ($activities): bool {
                    return $latestActivities->count() === 4
                        && $latestActivities->pluck('id')->all() === [
                            $activities[4]->id,
                            $activities[3]->id,
                            $activities[2]->id,
                            $activities[1]->id,
                        ];
                },
            )
            ->assertSeeInOrder([
                'Aktivitas dashboard nomor 5',
                'Aktivitas dashboard nomor 4',
                'Aktivitas dashboard nomor 3',
                'Aktivitas dashboard nomor 2',
            ])
            ->assertDontSee('Aktivitas dashboard nomor 1');
    }

    public function test_latest_activity_displays_full_dynamic_data_and_repair_link(): void
    {
        $repair = $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'DASHBOARD-ACTIVITY-DYNAMIC',
            createdAt: now()->subDay(),
            updatedAt: now(),
        );

        $description =
            'Penugasan dialihkan kepada teknisi pengganti karena teknisi sebelumnya tidak dapat melanjutkan pekerjaan sesuai jadwal operasional.';

        RepairLog::create([
            'repair_id' => $repair->id,
            'status' => RepairStatus::REPAIRING,
            'description' => $description,
            'updated_by' => $this->admin->id,
            'created_at' => now(),
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Sedang diperbaiki')
            ->assertSee($description)
            ->assertSee($repair->freezer->freezer_code)
            ->assertSee($this->admin->name)
            ->assertSee(
                route('admin.repairs.show', $repair),
                false,
            );
    }

    public function test_dashboard_displays_latest_activity_empty_state(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'latestActivities',
                fn($latestActivities): bool =>
                $latestActivities->isEmpty(),
            )
            ->assertSee('Belum ada aktivitas')
            ->assertSee(
                'Pembaruan reparasi akan muncul di bagian ini.',
            );
    }

    public function test_unassigned_queued_repair_immediately_requires_attention(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 03:00:00',
                'UTC',
            ),
        );

        $repair = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-UNASSIGNED',
            createdAt: '2026-08-12 02:50:00',
            updatedAt: '2026-08-12 02:50:00',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'attentionRepairs',
                function ($attentionRepairs) use ($repair): bool {
                    return $attentionRepairs->count() === 1
                        && $attentionRepairs->first()['repair']->is($repair)
                        && $attentionRepairs->first()['type']
                        === 'unassigned';
                },
            )
            ->assertSee('Belum ditugaskan')
            ->assertSee('Belum memiliki teknisi.')
            ->assertSee($repair->freezer->freezer_code)
            ->assertSee(
                route('admin.repairs.show', $repair),
                false,
            )
            ->assertSee(
                route('admin.repairs.index'),
                false,
            );
    }

    public function test_assigned_queued_repair_requires_attention_after_three_hours(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 06:00:00',
                'UTC',
            ),
        );

        $technician = $this->createUser([
            'name' => 'Teknisi Attention',
            'username' => 'technician_attention',
            'email' => 'technician.attention@example.com',
            'phone' => '081234567899',
            'role' => UserRole::TECHNICIAN,
        ]);

        $overdueRepair = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-ASSIGNED-OVERDUE',
            createdAt: '2026-08-12 01:00:00',
            updatedAt: '2026-08-12 02:00:00',
        );

        $this->assignRepair(
            repair: $overdueRepair,
            technician: $technician,
            assignedAt: '2026-08-12 02:00:00',
        );

        $recentRepair = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-ASSIGNED-RECENT',
            createdAt: '2026-08-12 04:00:00',
            updatedAt: '2026-08-12 04:00:00',
        );

        $this->assignRepair(
            repair: $recentRepair,
            technician: $technician,
            assignedAt: '2026-08-12 04:00:00',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'attentionRepairs',
                function ($attentionRepairs) use (
                    $overdueRepair,
                    $recentRepair,
                ): bool {
                    $repairIds = $attentionRepairs
                        ->pluck('repair.id');

                    return $repairIds->contains($overdueRepair->id)
                        && ! $repairIds->contains($recentRepair->id)
                        && $attentionRepairs
                            ->firstWhere(
                                'repair.id',
                                $overdueRepair->id,
                            )['type'] === 'waiting_inspection';
                },
            )
            ->assertSee('Menunggu diperiksa')
            ->assertSee(
                'Belum diperiksa sejak ditugaskan.',
            )
            ->assertSee($overdueRepair->freezer->freezer_code);
    }

    public function test_repairing_repair_requires_attention_after_twenty_four_hours_without_update(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 06:00:00',
                'UTC',
            ),
        );

        $staleRepair = $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'ATTENTION-REPAIRING-STALE',
            createdAt: '2026-08-10 04:00:00',
            updatedAt: '2026-08-11 05:00:00',
        );

        $recentRepair = $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'ATTENTION-REPAIRING-RECENT',
            createdAt: '2026-08-12 01:00:00',
            updatedAt: '2026-08-12 05:00:00',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'attentionRepairs',
                function ($attentionRepairs) use (
                    $staleRepair,
                    $recentRepair,
                ): bool {
                    $repairIds = $attentionRepairs
                        ->pluck('repair.id');

                    return $repairIds->contains($staleRepair->id)
                        && ! $repairIds->contains($recentRepair->id)
                        && $attentionRepairs
                            ->firstWhere(
                                'repair.id',
                                $staleRepair->id,
                            )['type'] === 'stale_repair';
                },
            )
            ->assertSee('Sedang diperbaiki')
            ->assertSee('Tidak ada pembaruan selama')
            ->assertSee($staleRepair->freezer->freezer_code);
    }

    public function test_attention_repairs_follow_priority_and_are_limited_to_four_items(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse(
                '2026-08-12 06:00:00',
                'UTC',
            ),
        );

        $technician = $this->createUser([
            'name' => 'Teknisi Priority',
            'username' => 'technician_priority',
            'email' => 'technician.priority@example.com',
            'phone' => '081234567898',
            'role' => UserRole::TECHNICIAN,
        ]);

        $unassignedOldest = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-PRIORITY-U1',
            createdAt: '2026-08-10 01:00:00',
            updatedAt: '2026-08-10 01:00:00',
        );

        $unassignedNewest = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-PRIORITY-U2',
            createdAt: '2026-08-11 01:00:00',
            updatedAt: '2026-08-11 01:00:00',
        );

        $waitingOldest = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-PRIORITY-W1',
            createdAt: '2026-08-10 02:00:00',
            updatedAt: '2026-08-10 02:00:00',
        );

        $this->assignRepair(
            repair: $waitingOldest,
            technician: $technician,
            assignedAt: '2026-08-10 02:00:00',
        );

        $waitingNewest = $this->createRepair(
            status: RepairStatus::QUEUED,
            serialNumber: 'ATTENTION-PRIORITY-W2',
            createdAt: '2026-08-11 02:00:00',
            updatedAt: '2026-08-11 02:00:00',
        );

        $this->assignRepair(
            repair: $waitingNewest,
            technician: $technician,
            assignedAt: '2026-08-11 02:00:00',
        );

        $staleRepair = $this->createRepair(
            status: RepairStatus::REPAIRING,
            serialNumber: 'ATTENTION-PRIORITY-S1',
            createdAt: '2026-08-09 01:00:00',
            updatedAt: '2026-08-10 03:00:00',
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas(
                'attentionRepairs',
                function ($attentionRepairs) use (
                    $unassignedOldest,
                    $unassignedNewest,
                    $waitingOldest,
                    $waitingNewest,
                ): bool {
                    return $attentionRepairs->count() === 4
                        && $attentionRepairs
                        ->pluck('repair.id')
                        ->all() === [
                            $unassignedOldest->id,
                            $unassignedNewest->id,
                            $waitingOldest->id,
                            $waitingNewest->id,
                        ];
                },
            )
            ->assertSeeInOrder([
                $unassignedOldest->freezer->freezer_code,
                $unassignedNewest->freezer->freezer_code,
                $waitingOldest->freezer->freezer_code,
                $waitingNewest->freezer->freezer_code,
            ])
            ->assertDontSee($staleRepair->freezer->freezer_code);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(array $overrides = []): User
    {
        return User::create([
            'name' => 'User Dashboard',
            'username' => 'user_' . bin2hex(random_bytes(4)),
            'email' => bin2hex(random_bytes(4)) . '@example.com',
            'phone' => '08' . random_int(
                1000000000,
                9999999999,
            ),
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
            'DASHBOARD-' . bin2hex(random_bytes(5)),
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
            'photo_path' => null,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'complaint_note' => 'Keluhan dashboard.',
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
            'created_at' =>
            $overrides['created_at'] ??
                $freezer->created_at,
            'updated_at' =>
            $overrides['updated_at'] ??
                $freezer->updated_at,
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
        RepairStatus $status,
        string $serialNumber,
        mixed $createdAt,
        mixed $updatedAt,
    ): Repair {
        $freezer = $this->createFreezer([
            'serial_number' => $serialNumber,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $intake = $this->createIntake(
            freezer: $freezer,
            receivedAt: $createdAt,
        );

        $repair = Repair::create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => null,
            'admin_id' => $this->admin->id,
            'status' => $status,
            'initial_analysis' => null,
        ]);

        $repair->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ])->save();

        return $repair->refresh();
    }

    private function assignRepair(
        Repair $repair,
        User $technician,
        mixed $assignedAt,
    ): RepairAssignment {
        $repair->forceFill([
            'technician_id' => $technician->id,
            'updated_at' => $assignedAt,
        ])->save();

        return RepairAssignment::create([
            'repair_id' => $repair->id,
            'technician_id' => $technician->id,
            'assigned_by' => $this->admin->id,
            'assigned_at' => $assignedAt,
            'ended_at' => null,
            'ended_by' => null,
            'end_reason' => null,
        ]);
    }
}
