<?php

namespace App\Actions\Freezers;

use App\Enums\VerificationStatus;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateFreezerAction
{
    public function execute(array $data, User $creator): Freezer
    {
        /** @var UploadedFile|null $photo */
        $photo = $data['photo'] ?? null;
        $photoPath = null;

        if ($photo instanceof UploadedFile) {
            $photoPath = $photo->store('freezers', 'public');
        }

        try {
            return DB::transaction(function () use (
                $data,
                $creator,
                $photoPath,
            ): Freezer {
                $freezer = Freezer::create([
                    'customer_id' => $data['customer_id'],
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                    'serial_number' => $data['serial_number'] ?? null,
                    'capacity_liter' => $data['capacity_liter'] ?? null,
                    'estimated_age' => $data['estimated_age'] ?? null,
                    'photo_path' => $photoPath,
                    'created_by' => $creator->id,
                ]);

                $freezer->forceFill([
                    'freezer_code' => sprintf(
                        'FZ-%05d',
                        $freezer->id,
                    ),
                ])->save();

                $intake = ServiceIntake::create([
                    'freezer_id' => $freezer->id,
                    'intake_code' => sprintf(
                        'TEMP-%d-%s',
                        $freezer->id,
                        bin2hex(random_bytes(4)),
                    ),
                    'complaint_note' => $data['complaint_note'] ?? null,
                    'condition_note' => null,
                    'status_verifikasi' =>
                    VerificationStatus::PENDING_ARRIVAL,
                    'rejection_reason' => null,
                    'received_by' => $creator->id,
                    'received_at' => now(),
                    'verified_by' => null,
                    'verified_at' => null,
                    'completed_at' => null,
                ]);

                $intake->forceFill([
                    'intake_code' => sprintf(
                        'IN-%05d',
                        $intake->id,
                    ),
                ])->save();

                return $freezer
                    ->refresh()
                    ->load('latestIntake');
            });
        } catch (Throwable $exception) {
            if ($photoPath !== null) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }
    }
}
