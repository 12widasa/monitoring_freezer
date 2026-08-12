<?php

namespace App\Actions\ServiceIntakes;

use App\Enums\VerificationStatus;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VerifyServiceIntakeAction
{
    /**
     * @param array{
     *     verification_freezer_id: int|string,
     *     verification_result: string,
     *     condition_note?: string|null,
     *     rejection_reason?: string|null
     * } $data
     */
    public function execute(
        Freezer $freezer,
        array $data,
        User $verifier,
    ): ServiceIntake {
        return DB::transaction(function () use (
            $freezer,
            $data,
            $verifier,
        ): ServiceIntake {
            /** @var ServiceIntake|null $intake */
            $intake = ServiceIntake::query()
                ->where('freezer_id', $freezer->id)
                ->latest('received_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($intake === null) {
                throw ValidationException::withMessages([
                    'verification_result' =>
                    'Freezer tidak memiliki service intake aktif.',
                ]);
            }

            if (
                $intake->status_verifikasi
                !== VerificationStatus::PENDING_ARRIVAL
            ) {
                throw ValidationException::withMessages([
                    'verification_result' =>
                    'Service intake ini sudah pernah diverifikasi.',
                ]);
            }

            $verificationResult = VerificationStatus::from(
                $data['verification_result'],
            );

            $verifiedAt = now();

            $conditionNote = isset($data['condition_note'])
                && trim((string) $data['condition_note']) !== ''
                ? trim((string) $data['condition_note'])
                : null;

            if (
                $verificationResult
                === VerificationStatus::REJECTED
            ) {
                $rejectionReason = trim(
                    (string) ($data['rejection_reason'] ?? ''),
                );

                if ($rejectionReason === '') {
                    throw ValidationException::withMessages([
                        'rejection_reason' =>
                        'Alasan penolakan wajib diisi.',
                    ]);
                }

                $intake->update([
                    'status_verifikasi' =>
                    VerificationStatus::REJECTED,
                    'rejection_reason' => $rejectionReason,
                    'verified_by' => $verifier->id,
                    'verified_at' => $verifiedAt,
                    'completed_at' => $verifiedAt,
                    'condition_note' => $conditionNote,
                ]);

                return $intake->refresh();
            }

            $intake->update([
                'status_verifikasi' =>
                VerificationStatus::VERIFIED,
                'condition_note' => $conditionNote,
                'rejection_reason' => null,
                'verified_by' => $verifier->id,
                'verified_at' => $verifiedAt,
                'completed_at' => null,
            ]);

            return $intake->refresh();
        });
    }
}
