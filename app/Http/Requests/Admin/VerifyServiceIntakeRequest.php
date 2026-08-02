<?php

namespace App\Http\Requests\Admin;

use App\Enums\VerificationStatus;
use App\Models\Freezer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VerifyServiceIntakeRequest extends FormRequest
{
    protected $errorBag = 'verifyServiceIntake';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verification_freezer_id' => [
                'required',
                'integer',
            ],

            'verification_result' => [
                'required',
                Rule::in([
                    VerificationStatus::VERIFIED->value,
                    VerificationStatus::REJECTED->value,
                ]),
            ],

            'condition_note' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'rejection_reason' => [
                Rule::requiredIf(
                    fn(): bool =>
                    $this->input('verification_result')
                        === VerificationStatus::REJECTED->value,
                ),
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'verification_freezer_id.required' =>
            'Freezer yang akan diverifikasi tidak ditemukan.',

            'verification_result.required' =>
            'Pilih hasil verifikasi.',

            'verification_result.in' =>
            'Hasil verifikasi tidak valid.',

            'rejection_reason.required' =>
            'Alasan penolakan wajib diisi.',

            'rejection_reason.max' =>
            'Alasan penolakan maksimal 5.000 karakter.',

            'condition_note.max' =>
            'Kondisi unit maksimal 5.000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'condition_note' =>
            $this->filled('condition_note')
                ? trim((string) $this->input('condition_note'))
                : null,

            'rejection_reason' =>
            $this->filled('rejection_reason')
                ? trim((string) $this->input('rejection_reason'))
                : null,
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Freezer|null $freezer */
                $freezer = $this->route('freezer');

                if ($freezer === null) {
                    return;
                }

                if (
                    (int) $this->input('verification_freezer_id')
                    !== $freezer->id
                ) {
                    $validator->errors()->add(
                        'verification_result',
                        'Data freezer yang diverifikasi tidak sesuai.',
                    );

                    return;
                }

                $intake = $freezer->latestIntake;

                if ($intake === null) {
                    $validator->errors()->add(
                        'verification_result',
                        'Freezer tidak memiliki service intake aktif.',
                    );

                    return;
                }

                if (
                    $intake->status_verifikasi
                    !== VerificationStatus::PENDING_ARRIVAL
                ) {
                    $validator->errors()->add(
                        'verification_result',
                        'Service intake ini sudah pernah diverifikasi.',
                    );
                }
            },
        ];
    }
}
