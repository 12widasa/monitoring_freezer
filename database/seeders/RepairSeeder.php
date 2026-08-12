<?php

namespace Database\Seeders;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RepairSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::query()
                ->where('username', 'admin')
                ->first();

            if ($admin === null) {
                throw new RuntimeException(
                    'Admin belum tersedia. Jalankan UserSeeder terlebih dahulu.',
                );
            }

            $technicians = User::query()
                ->where('role', UserRole::TECHNICIAN->value)
                ->where('is_active', true)
                ->orderBy('id')
                ->get();

            if ($technicians->count() < 2) {
                throw new RuntimeException(
                    'Minimal dua teknisi aktif diperlukan.',
                );
            }

            $intakes = ServiceIntake::query()
                ->where(
                    'status_verifikasi',
                    VerificationStatus::VERIFIED->value,
                )
                ->whereDoesntHave('repair')
                ->orderBy('id')
                ->get();

            if ($intakes->count() < 3) {
                throw new RuntimeException(
                    'Minimal tiga intake verified tanpa repair diperlukan.',
                );
            }

            $records = [
                [
                    'intake' => $intakes[0],
                    'technician' => null,
                    'status' => RepairStatus::QUEUED,
                    'initial_analysis' => null,
                    'created_at' => now()->subHours(2),
                    'log_description' =>
                    'Tugas reparasi dibuat dan menunggu penugasan teknisi.',
                ],
                [
                    'intake' => $intakes[1],
                    'technician' => $technicians[0],
                    'status' => RepairStatus::INSPECTING,
                    'initial_analysis' =>
                    'Pemeriksaan awal menunjukkan kemungkinan gangguan pada sistem defrost.',
                    'created_at' => now()->subDay(),
                    'log_description' =>
                    'Teknisi mulai melakukan pemeriksaan awal unit.',
                ],
                [
                    'intake' => $intakes[2],
                    'technician' => $technicians[1],
                    'status' => RepairStatus::REPAIRING,
                    'initial_analysis' =>
                    'Tekanan refrigeran rendah dan terdapat indikasi kebocoran.',
                    'created_at' => now()->subDays(2),
                    'log_description' =>
                    'Perbaikan kebocoran dan pengisian refrigeran sedang dilakukan.',
                ],
                [
                    'intake' => $intakes[3],
                    'technician' => $technicians[0],
                    'status' => RepairStatus::QUEUED,
                    'initial_analysis' => null,
                    'created_at' => now()->subHours(6),
                    'log_description' =>
                    'Tugas reparasi sudah ditugaskan dan menunggu pemeriksaan teknisi.',
                ],
            ];

            foreach ($records as $record) {
                /** @var ServiceIntake $intake */
                $intake = $record['intake'];

                $technician = $record['technician'];

                $repair = Repair::query()->updateOrCreate(
                    [
                        'service_intake_id' => $intake->id,
                    ],
                    [
                        'freezer_id' => $intake->freezer_id,
                        'technician_id' => $technician?->id,
                        'admin_id' => $admin->id,
                        'status' => $record['status']->value,
                        'initial_analysis' =>
                        $record['initial_analysis'],
                    ],
                );

                $repair->forceFill([
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['created_at'],
                ])->save();

                RepairLog::query()->updateOrCreate(
                    [
                        'repair_id' => $repair->id,
                        'status' => $record['status']->value,
                        'description' =>
                        $record['log_description'],
                    ],
                    [
                        'updated_by' =>
                        $technician?->id ?? $admin->id,
                        'created_at' => $record['created_at'],
                    ],
                );

                if ($technician !== null) {
                    RepairAssignment::query()->updateOrCreate(
                        [
                            'repair_id' => $repair->id,
                            'ended_at' => null,
                        ],
                        [
                            'technician_id' => $technician->id,
                            'assigned_by' => $admin->id,
                            'assigned_at' =>
                            $record['created_at'],
                            'ended_by' => null,
                            'end_reason' => null,
                        ],
                    );
                }
            }
        });
    }
}
