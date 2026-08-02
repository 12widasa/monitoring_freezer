<?php

namespace App\Actions\Repairs;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateRepairAction
{
    /**
     * @param array{
     *     service_intake_id: int,
     *     technician_id?: int|null
     * } $data
     */
    public function execute(
        array $data,
        User $admin,
    ): Repair {
        return DB::transaction(function () use (
            $data,
            $admin,
        ): Repair {
            /** @var ServiceIntake|null $intake */
            $intake = ServiceIntake::query()
                ->whereKey($data['service_intake_id'])
                ->lockForUpdate()
                ->first();

            if ($intake === null) {
                throw ValidationException::withMessages([
                    'service_intake_id' =>
                    'Service intake tidak ditemukan.',
                ]);
            }

            if (
                $intake->status_verifikasi
                !== VerificationStatus::VERIFIED
            ) {
                throw ValidationException::withMessages([
                    'service_intake_id' =>
                    'Hanya service intake yang sudah diverifikasi yang dapat dibuatkan reparasi.',
                ]);
            }

            $latestIntakeId = ServiceIntake::query()
                ->where('freezer_id', $intake->freezer_id)
                ->latest('received_at')
                ->latest('id')
                ->value('id');

            if ($latestIntakeId !== $intake->id) {
                throw ValidationException::withMessages([
                    'service_intake_id' =>
                    'Reparasi hanya dapat dibuat dari service intake terbaru.',
                ]);
            }

            $repairAlreadyExists = Repair::query()
                ->where('service_intake_id', $intake->id)
                ->exists();

            if ($repairAlreadyExists) {
                throw ValidationException::withMessages([
                    'service_intake_id' =>
                    'Service intake ini sudah memiliki tugas reparasi.',
                ]);
            }

            $technicianId = $data['technician_id'] ?? null;
            $technician = null;

            if ($technicianId !== null) {
                /** @var User|null $technician */
                $technician = User::query()
                    ->whereKey($technicianId)
                    ->lockForUpdate()
                    ->first();

                if (
                    $technician === null
                    || $technician->role !== UserRole::TECHNICIAN
                ) {
                    throw ValidationException::withMessages([
                        'technician_id' =>
                        'User yang dipilih bukan teknisi.',
                    ]);
                }

                if (! $technician->is_active) {
                    throw ValidationException::withMessages([
                        'technician_id' =>
                        'Teknisi yang dipilih sedang tidak aktif.',
                    ]);
                }
            }

            $repair = Repair::query()->create([
                'freezer_id' => $intake->freezer_id,
                'service_intake_id' => $intake->id,
                'technician_id' => $technicianId,
                'admin_id' => $admin->id,
                'status' => RepairStatus::QUEUED,
                'initial_analysis' => null,
            ]);

            if ($technician !== null) {
                $assignedAt = $repair->created_at ?? now();

                RepairAssignment::query()->create([
                    'repair_id' => $repair->id,
                    'technician_id' => $technician->id,
                    'assigned_by' => $admin->id,
                    'assigned_at' => $assignedAt,
                    'ended_at' => null,
                    'ended_by' => null,
                    'end_reason' => null,
                ]);

                RepairLog::query()->create([
                    'repair_id' => $repair->id,
                    'status' => $repair->status,
                    'description' => sprintf(
                        'Teknisi %s ditetapkan untuk menangani tugas reparasi.',
                        $technician->name,
                    ),
                    'updated_by' => $admin->id,
                    'created_at' => $assignedAt,
                ]);
            }

            return $repair;
        });
    }
}
