<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    protected $errorBag = 'createUser';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                Rule::in([
                    UserRole::TECHNICIAN->value,
                    UserRole::CUSTOMER->value,
                ]),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'username' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username'),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:30',
            ],

            'company_name' => [
                'nullable',
                'required_if:role,' . UserRole::CUSTOMER->value,
                'string',
                'max:100',
            ],

            'company_phone' => [
                'nullable',
                'required_if:role,' . UserRole::CUSTOMER->value,
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:30',
            ],

            'address' => [
                'nullable',
                'required_if:role,' . UserRole::CUSTOMER->value,
                'string',
                'max:5000',
            ],

            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna yang dipilih tidak valid.',

            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 100 karakter.',

            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.unique' => 'Username sudah digunakan.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah digunakan.',

            'phone.required' => 'Nomor telepon pengguna wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda + di awal.',
            'phone.max' => 'Nomor telepon pengguna maksimal 30 karakter.',

            'company_name.required_if' => 'Nama perusahaan wajib diisi untuk pelanggan.',
            'company_name.max' => 'Nama perusahaan maksimal 100 karakter.',

            'company_phone.required_if' => 'Nomor telepon perusahaan wajib diisi untuk pelanggan.',
            'company_phone.regex' => 'Nomor telepon perusahaan hanya boleh berisi angka dan tanda + di awal.',
            'company_phone.max' => 'Nomor telepon perusahaan maksimal 30 karakter.',

            'address.required_if' => 'Alamat perusahaan wajib diisi untuk pelanggan.',
            'address.max' => 'Alamat perusahaan maksimal 5.000 karakter.',

            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => $this->filled('role')
                ? trim((string) $this->input('role'))
                : null,

            'name' => $this->filled('name')
                ? trim((string) $this->input('name'))
                : null,

            'username' => $this->filled('username')
                ? trim((string) $this->input('username'))
                : null,

            'email' => $this->filled('email')
                ? strtolower(trim((string) $this->input('email')))
                : null,

            'phone' => $this->filled('phone')
                ? trim((string) $this->input('phone'))
                : null,

            'company_name' => $this->filled('company_name')
                ? trim((string) $this->input('company_name'))
                : null,

            'company_phone' => $this->filled('company_phone')
                ? trim((string) $this->input('company_phone'))
                : null,

            'address' => $this->filled('address')
                ? trim((string) $this->input('address'))
                : null,
        ]);
    }
}
