<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRepairRequest extends FormRequest
{
    protected $errorBag = 'createRepair';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_intake_id' => [
                'required',
                'integer',
                'exists:service_intakes,id',
            ],

            'technician_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'service_intake_id.required' =>
            'Service intake wajib dipilih.',

            'service_intake_id.integer' =>
            'Service intake tidak valid.',

            'service_intake_id.exists' =>
            'Service intake tidak ditemukan.',

            'technician_id.integer' =>
            'Teknisi yang dipilih tidak valid.',

            'technician_id.exists' =>
            'Teknisi yang dipilih tidak ditemukan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service_intake_id' =>
            $this->filled('service_intake_id')
                ? (int) $this->input('service_intake_id')
                : null,

            'technician_id' =>
            $this->filled('technician_id')
                ? (int) $this->input('technician_id')
                : null,
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $intake = ServiceIntake::query()
                    ->with('repair')
                    ->find($this->integer('service_intake_id'));

                if ($intake === null) {
                    return;
                }

                if (
                    $intake->status_verifikasi
                    !== VerificationStatus::VERIFIED
                ) {
                    $validator->errors()->add(
                        'service_intake_id',
                        'Hanya service intake yang sudah diverifikasi yang dapat dibuatkan reparasi.',
                    );

                    return;
                }

                if ($intake->repair !== null) {
                    $validator->errors()->add(
                        'service_intake_id',
                        'Service intake ini sudah memiliki tugas reparasi.',
                    );

                    return;
                }

                $latestIntakeId = ServiceIntake::query()
                    ->where('freezer_id', $intake->freezer_id)
                    ->latest('received_at')
                    ->latest('id')
                    ->value('id');

                if ($latestIntakeId !== $intake->id) {
                    $validator->errors()->add(
                        'service_intake_id',
                        'Reparasi hanya dapat dibuat dari service intake terbaru.',
                    );

                    return;
                }

                if (! $this->filled('technician_id')) {
                    return;
                }

                $technician = User::query()
                    ->find($this->integer('technician_id'));

                if (
                    $technician === null
                    || $technician->role !== UserRole::TECHNICIAN
                ) {
                    $validator->errors()->add(
                        'technician_id',
                        'User yang dipilih bukan teknisi.',
                    );

                    return;
                }

                if (! $technician->is_active) {
                    $validator->errors()->add(
                        'technician_id',
                        'Teknisi yang dipilih sedang tidak aktif.',
                    );
                }
            },
        ];
    }
}
