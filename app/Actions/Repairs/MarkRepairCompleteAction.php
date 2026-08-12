<?php

namespace App\Actions\Repairs;

use App\Enums\RepairStatus;
use App\Models\Repair;
use App\Models\RepairLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarkRepairCompleteAction
{
    public function execute(
        Repair $repair,
        array $data,
        User $technician,
    ): Repair {
        return DB::transaction(function () use ($repair, $data, $technician): Repair {
            if ($repair->status !== RepairStatus::REPAIRING) {
                throw ValidationException::withMessages([
                    'description' => 'Tugas ini belum dalam tahap perbaikan.',
                ]);
            }

            RepairLog::query()->create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::COMPLETED->value,
                'description' => $data['description'],
                'updated_by' => $technician->id,
                'created_at' => now(),
            ]);

            $repair->update(['status' => RepairStatus::COMPLETED->value]);

            if ($repair->activeAssignment !== null) {
                $repair->activeAssignment->update([
                    'ended_at' => now(),
                    'ended_by' => $technician->id,
                    'end_reason' => 'Reparasi selesai.',
                ]);
            }

            return $repair->refresh();
        });
    }
}