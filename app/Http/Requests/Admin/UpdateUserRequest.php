<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    protected $errorBag = 'editUser';

    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user instanceof User
            && in_array($user->role, [
                UserRole::TECHNICIAN,
                UserRole::CUSTOMER,
            ], true);
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        $isCustomer = $user->role === UserRole::CUSTOMER;

        return [
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
                Rule::unique('users', 'username')->ignore($user),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user),
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:30',
            ],

            'company_name' => [
                Rule::requiredIf($isCustomer),
                'nullable',
                'string',
                'max:100',
            ],

            'company_phone' => [
                Rule::requiredIf($isCustomer),
                'nullable',
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:30',
            ],

            'address' => [
                Rule::requiredIf($isCustomer),
                'nullable',
                'string',
                'max:5000',
            ],

            'password' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
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

            'company_name.required' => 'Nama perusahaan wajib diisi untuk pelanggan.',
            'company_name.max' => 'Nama perusahaan maksimal 100 karakter.',

            'company_phone.required' => 'Nomor telepon perusahaan wajib diisi untuk pelanggan.',
            'company_phone.regex' => 'Nomor telepon perusahaan hanya boleh berisi angka dan tanda + di awal.',
            'company_phone.max' => 'Nomor telepon perusahaan maksimal 30 karakter.',

            'address.required' => 'Alamat perusahaan wajib diisi untuk pelanggan.',
            'address.max' => 'Alamat perusahaan maksimal 5.000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
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

            'password' => $this->filled('password')
                ? (string) $this->input('password')
                : null,
        ]);
    }
}
