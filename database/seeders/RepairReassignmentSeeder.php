<?php

namespace Database\Seeders;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RepairReassignmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::query()
                ->where('username', 'admin')
                ->first();

            $newTechnician = User::query()
                ->where('username', 'teknisi.rizky')
                ->where('role', UserRole::TECHNICIAN->value)
                ->where('is_active', true)
                ->first();

            $repair = Repair::query()
                ->whereHas('serviceIntake', function ($query): void {
                    $query->where('intake_code', 'IN-00005');
                })
                ->lockForUpdate()
                ->first();

            if (
                $admin === null
                || $newTechnician === null
                || $repair === null
            ) {
                throw new RuntimeException(
                    'Data prerequisite reassignment belum lengkap.',
                );
            }

            $currentAssignment = RepairAssignment::query()
                ->where('repair_id', $repair->id)
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->latest('assigned_at')
                ->latest('id')
                ->first();

            if ($currentAssignment === null) {
                throw new RuntimeException(
                    'Repair belum memiliki assignment aktif.',
                );
            }

            if (
                $currentAssignment->technician_id
                === $newTechnician->id
            ) {
                return;
            }

            $endedAt = now()->subHours(3);
            $assignedAt = now()->subHours(3);

            $currentAssignment->update([
                'ended_at' => $endedAt,
                'ended_by' => $admin->id,
                'end_reason' =>
                'Penugasan dialihkan untuk penanganan lanjutan.',
            ]);

            RepairAssignment::query()->create([
                'repair_id' => $repair->id,
                'technician_id' => $newTechnician->id,
                'assigned_by' => $admin->id,
                'assigned_at' => $assignedAt,
                'ended_at' => null,
                'ended_by' => null,
                'end_reason' => null,
            ]);

            $repair->update([
                'technician_id' => $newTechnician->id,
                'status' => RepairStatus::REPAIRING->value,
            ]);

            RepairLog::query()->create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::REPAIRING->value,
                'description' =>
                'Teknisi dialihkan dari Andi Pratama ke Rizky Maulana untuk penanganan lanjutan.',
                'updated_by' => $admin->id,
                'created_at' => $assignedAt,
            ]);
        });
    }
}
