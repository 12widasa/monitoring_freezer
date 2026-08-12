<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFreezerRequest extends FormRequest
{
    protected $errorBag = 'createFreezer';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id'),
            ],
            'brand' => [
                'required',
                'string',
                'max:50',
            ],
            'model' => [
                'required',
                'string',
                'max:50',
            ],
            'serial_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('freezers', 'serial_number'),
            ],
            'capacity_liter' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'estimated_age' => [
                'nullable',
                'string',
                Rule::in([
                    '<1 tahun',
                    '1-3 tahun',
                    '>3 tahun',
                ]),
            ],
            'complaint_note' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan wajib dipilih.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak valid.',

            'brand.required' => 'Merek freezer wajib diisi.',
            'brand.max' => 'Merek freezer maksimal 50 karakter.',

            'model.required' => 'Model freezer wajib diisi.',
            'model.max' => 'Model freezer maksimal 50 karakter.',

            'serial_number.unique' => 'Nomor seri sudah terdaftar.',
            'serial_number.max' => 'Nomor seri maksimal 100 karakter.',

            'capacity_liter.integer' => 'Kapasitas harus berupa angka.',
            'capacity_liter.min' => 'Kapasitas minimal 1 liter.',

            'estimated_age.in' => 'Perkiraan usia yang dipilih tidak valid.',

            'complaint_note.max' => 'Keluhan awal maksimal 5.000 karakter.',

            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'brand' => $this->filled('brand')
                ? trim((string) $this->input('brand'))
                : null,

            'model' => $this->filled('model')
                ? trim((string) $this->input('model'))
                : null,

            'serial_number' => $this->filled('serial_number')
                ? trim((string) $this->input('serial_number'))
                : null,

            'complaint_note' => $this->filled('complaint_note')
                ? trim((string) $this->input('complaint_note'))
                : null,
        ]);
    }
}
