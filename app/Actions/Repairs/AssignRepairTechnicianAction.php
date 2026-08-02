<?php

namespace App\Actions\Repairs;

use App\Enums\UserRole;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignRepairTechnicianAction
{
    /**
     * @param array{
     *     technician_id: int,
     *     reason?: string|null
     * } $data
     */
    public function execute(
        Repair $repair,
        array $data,
        User $admin,
    ): Repair {
        return DB::transaction(function () use (
            $repair,
            $data,
            $admin,
        ): Repair {
            /** @var Repair|null $lockedRepair */
            $lockedRepair = Repair::query()
                ->whereKey($repair->id)
                ->lockForUpdate()
                ->first();

            if ($lockedRepair === null) {
                throw ValidationException::withMessages([
                    'technician_id' =>
                    'Tugas reparasi tidak ditemukan.',
                ]);
            }

            /** @var User|null $newTechnician */
            $newTechnician = User::query()
                ->whereKey($data['technician_id'])
                ->lockForUpdate()
                ->first();

            if (
                $newTechnician === null
                || $newTechnician->role !== UserRole::TECHNICIAN
            ) {
                throw ValidationException::withMessages([
                    'technician_id' =>
                    'User yang dipilih bukan teknisi.',
                ]);
            }

            if (! $newTechnician->is_active) {
                throw ValidationException::withMessages([
                    'technician_id' =>
                    'Teknisi yang dipilih sedang tidak aktif.',
                ]);
            }

            $oldTechnicianId = $lockedRepair->technician_id;
            $reason = $data['reason'] ?? null;

            if ($oldTechnicianId === $newTechnician->id) {
                throw ValidationException::withMessages([
                    'technician_id' =>
                    'Teknisi yang dipilih sudah menangani tugas ini.',
                ]);
            }

            if (
                $oldTechnicianId !== null
                && ($reason === null || trim($reason) === '')
            ) {
                throw ValidationException::withMessages([
                    'reason' =>
                    'Alasan pergantian teknisi wajib diisi.',
                ]);
            }

            $changedAt = now();
            $oldTechnician = null;

            if ($oldTechnicianId !== null) {
                /** @var User|null $oldTechnician */
                $oldTechnician = User::query()
                    ->whereKey($oldTechnicianId)
                    ->first();

                /** @var RepairAssignment|null $activeAssignment */
                $activeAssignment = RepairAssignment::query()
                    ->where('repair_id', $lockedRepair->id)
                    ->whereNull('ended_at')
                    ->lockForUpdate()
                    ->latest('assigned_at')
                    ->latest('id')
                    ->first();

                if ($activeAssignment === null) {
                    throw ValidationException::withMessages([
                        'technician_id' =>
                        'Histori penugasan aktif tidak ditemukan. Muat ulang halaman dan periksa data tugas.',
                    ]);
                }

                if (
                    $activeAssignment->technician_id
                    !== $oldTechnicianId
                ) {
                    throw ValidationException::withMessages([
                        'technician_id' =>
                        'Teknisi aktif tidak sesuai dengan histori penugasan.',
                    ]);
                }

                $activeAssignment->update([
                    'ended_at' => $changedAt,
                    'ended_by' => $admin->id,
                    'end_reason' => trim((string) $reason),
                ]);
            } else {
                $activeAssignmentExists =
                    RepairAssignment::query()
                    ->where('repair_id', $lockedRepair->id)
                    ->whereNull('ended_at')
                    ->lockForUpdate()
                    ->exists();

                if ($activeAssignmentExists) {
                    throw ValidationException::withMessages([
                        'technician_id' =>
                        'Tugas memiliki histori penugasan aktif yang tidak sesuai.',
                    ]);
                }
            }

            RepairAssignment::query()->create([
                'repair_id' => $lockedRepair->id,
                'technician_id' => $newTechnician->id,
                'assigned_by' => $admin->id,
                'assigned_at' => $changedAt,
                'ended_at' => null,
                'ended_by' => null,
                'end_reason' => null,
            ]);

            $lockedRepair->update([
                'technician_id' => $newTechnician->id,
            ]);

            $description = $oldTechnician === null
                ? sprintf(
                    'Teknisi %s ditetapkan untuk menangani tugas reparasi.',
                    $newTechnician->name,
                )
                : sprintf(
                    'Penugasan dialihkan dari %s kepada %s. Alasan: %s',
                    $oldTechnician->name,
                    $newTechnician->name,
                    trim((string) $reason),
                );

            RepairLog::query()->create([
                'repair_id' => $lockedRepair->id,
                'status' => $lockedRepair->status,
                'description' => $description,
                'updated_by' => $admin->id,
                'created_at' => $changedAt,
            ]);

            return $lockedRepair->refresh();
        });
    }
}
