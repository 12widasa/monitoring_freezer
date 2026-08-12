<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\Repair;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Routing\UrlGenerator;

class UpdateRepairTechnicianRequest extends FormRequest
{
    protected $errorBag = 'updateRepairTechnician';

    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl(): string
    {
        /** @var Repair|null $repair */
        $repair = $this->route('repair');

        if ($repair === null) {
            return app(UrlGenerator::class)->previous();
        }

        return route(
            'admin.repairs.show',
            $repair,
        );
    }

    public function rules(): array
    {
        return [
            'technician_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'technician_id.required' =>
            'Teknisi wajib dipilih.',

            'technician_id.integer' =>
            'Teknisi yang dipilih tidak valid.',

            'technician_id.exists' =>
            'Teknisi yang dipilih tidak ditemukan.',

            'reason.required' =>
            'Alasan pergantian teknisi wajib diisi.',

            'reason.string' =>
            'Alasan pergantian teknisi tidak valid.',

            'reason.max' =>
            'Alasan pergantian teknisi maksimal 1000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $reason = $this->input('reason');

        $this->merge([
            'technician_id' =>
            $this->filled('technician_id')
                ? (int) $this->input('technician_id')
                : null,

            'reason' => is_string($reason)
                ? trim($reason)
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

                /** @var Repair|null $repair */
                $repair = $this->route('repair');

                if ($repair === null) {
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

                    return;
                }

                if (
                    $repair->technician_id !== null
                    && $repair->technician_id === $technician->id
                ) {
                    $validator->errors()->add(
                        'technician_id',
                        'Teknisi yang dipilih sudah menangani tugas ini.',
                    );

                    return;
                }

                if (
                    $repair->technician_id !== null
                    && ! $this->filled('reason')
                ) {
                    $validator->errors()->add(
                        'reason',
                        'Alasan pergantian teknisi wajib diisi.',
                    );
                }
            },
        ];
    }
}
