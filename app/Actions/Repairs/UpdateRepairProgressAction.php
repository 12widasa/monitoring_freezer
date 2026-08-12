<?php

namespace App\Actions\Repairs;

use App\Enums\RepairStatus;
use App\Models\Repair;
use App\Models\RepairComponent;
use App\Models\RepairLog;
use App\Models\RepairLogPhoto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateRepairProgressAction
{
    /**
     * @param array{
     *     description: string,
     *     photos?: array<int, \Illuminate\Http\UploadedFile>,
     *     installed_component_ids?: array<int, int>
     * } $data
     */
    public function execute(
        Repair $repair,
        array $data,
        User $technician,
    ): Repair {
        return DB::transaction(function () use ($repair, $data, $technician): Repair {
            if ($repair->status !== RepairStatus::REPAIRING) {
                throw ValidationException::withMessages([
                    'description' => 'Tugas ini belum atau sudah tidak dalam tahap perbaikan.',
                ]);
            }

            $log = RepairLog::query()->create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::COMPLETED->value,
                'description' => $data['description'],
                'updated_by' => $technician->id,
                'created_at' => now(),
            ]);

            foreach ($data['photos'] ?? [] as $index => $photo) {
                $photoPath = $photo->store('repairs', 'public');

                RepairLogPhoto::query()->create([
                    'repair_log_id' => $log->id,
                    'photo_path' => $photoPath,
                    'sort_order' => $index,
                    'created_at' => now(),
                ]);
            }

            $installedIds = $data['installed_component_ids'] ?? [];

            if ($installedIds !== []) {
                RepairComponent::query()
                    ->where('repair_id', $repair->id)
                    ->whereIn('id', $installedIds)
                    ->update([
                        'status' => 'installed',
                        'installed_by' => $technician->id,
                        'installed_at' => now(),
                    ]);
            }

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