<?php

namespace App\Actions\Repairs;

use App\Enums\RepairStatus;
use App\Models\Repair;
use App\Models\RepairLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StartInspectionAction
{
    public function execute(Repair $repair, User $technician): Repair
    {
        return DB::transaction(function () use ($repair, $technician): Repair {
            if ($repair->status !== RepairStatus::QUEUED) {
                throw ValidationException::withMessages([
                    'status' => 'Tugas ini sudah tidak dalam status menunggu diperiksa.',
                ]);
            }

            RepairLog::query()->create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::INSPECTING->value,
                'description' => 'Pemeriksaan dimulai oleh teknisi.',
                'updated_by' => $technician->id,
                'created_at' => now(),
            ]);

            $repair->update(['status' => RepairStatus::INSPECTING->value]);

            return $repair->refresh();
        });
    }
}