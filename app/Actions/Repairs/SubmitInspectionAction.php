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

class SubmitInspectionAction
{
    /**
     * @param array{
     *     description: string,
     *     photos?: array<int, \Illuminate\Http\UploadedFile>,
     *     components?: array<int, array{component_id: int, quantity: int, note?: string|null}>
     * } $data
     */
    public function execute(
        Repair $repair,
        array $data,
        User $technician,
    ): Repair {
        return DB::transaction(function () use ($repair, $data, $technician): Repair {
            if ($repair->status !== RepairStatus::INSPECTING) {
                throw ValidationException::withMessages([
                    'description' => 'Tugas ini belum atau sudah tidak dalam tahap pemeriksaan.',
                ]);
            }

            $log = RepairLog::query()->create([
                'repair_id' => $repair->id,
                'status' => RepairStatus::INSPECTING->value,
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

            foreach ($data['components'] ?? [] as $component) {
                RepairComponent::query()->create([
                    'repair_id' => $repair->id,
                    'component_id' => $component['component_id'],
                    'quantity' => $component['quantity'],
                    'status' => 'requested',
                    'note' => $component['note'] ?? null,
                    'added_by' => $technician->id,
                ]);
            }

            $repair->update(['status' => RepairStatus::REPAIRING->value]);

            return $repair->refresh();
        });
    }
}