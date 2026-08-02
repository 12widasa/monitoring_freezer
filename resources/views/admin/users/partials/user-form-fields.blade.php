@php
    $isEdit = $mode === 'edit';
    $prefix = $isEdit ? 'edit_user' : 'create_user';
    $customerFieldsId = $isEdit ? 'editUserCustomerFields' : 'createUserCustomerFields';

    $selectedRole = old('role', $defaultRole ?? 'technician');

    $fieldValue = static function (string $field) use ($values): mixed {
        return old($field, $values[$field] ?? '');
    };
@endphp

{{-- Peran pengguna --}}
<section class="space-y-4">
    <div>
        <h3 class="text-sm font-semibold text-slate-900">
            Peran pengguna
        </h3>

        <p class="mt-1 text-xs text-slate-500">
            Tentukan akses dan fungsi pengguna di dalam sistem.
        </p>
    </div>

    <fieldset id="{{ $prefix }}_role_group" tabindex="-1">
        <legend class="sr-only">
            Pilih peran pengguna
        </legend>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            {{-- Teknisi --}}
            <label class="block cursor-pointer">
                <input type="radio" name="role" value="technician" @checked($selectedRole === 'technician')
                    @disabled($isEdit) class="peer sr-only">

                <div
                    class="flex min-h-24 items-start gap-3 rounded border border-slate-200 bg-white p-4 transition-colors hover:border-blue-300 hover:bg-blue-50/40 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500">

                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded border border-emerald-100 bg-emerald-50 text-emerald-600">

                        <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">
                            Teknisi
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Menangani tugas pemeriksaan dan reparasi freezer.
                        </p>
                    </div>
                </div>
            </label>

            {{-- Pelanggan --}}
            <label class="block cursor-pointer">
                <input type="radio" name="role" value="customer" @checked($selectedRole === 'customer')
                    @disabled($isEdit) class="peer sr-only">

                <div
                    class="flex min-h-24 items-start gap-3 rounded border border-slate-200 bg-white p-4 transition-colors hover:border-blue-300 hover:bg-blue-50/40 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500">

                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded border border-amber-100 bg-amber-50 text-amber-600">

                        <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="M3 21h18M6 21V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v17M16 9h3a1 1 0 0 1 1 1v11"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />

                            <path d="M9 7h1M9 11h1M9 15h1M13 7h1M13 11h1M13 15h1" stroke="currentColor"
                                stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">
                            Pelanggan
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Memantau freezer dan perkembangan reparasi miliknya.
                        </p>
                    </div>
                </div>
            </label>
        </div>
    </fieldset>

    <x-form-error data-error-for="role" :messages="$formErrors->get('role')" />
</section>

<div class="border-t border-slate-200"></div>

{{-- Informasi akun --}}
<section class="space-y-4">
    <div>
        <h3 class="text-sm font-semibold text-slate-900">
            Informasi akun
        </h3>

        <p class="mt-1 text-xs text-slate-500">
            {{ $isEdit
                ? 'Perbarui identitas dan informasi kontak pengguna.'
                : 'Masukkan identitas dan informasi kontak pengguna.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="{{ $prefix }}_name" class="mb-2 block text-sm font-medium text-slate-900">
                Nama lengkap
                <span class="text-red-600">*</span>
            </label>

            <input id="{{ $prefix }}_name" name="name" type="text" required autocomplete="name"
                value="{{ $fieldValue('name') }}" placeholder="Contoh: Budi Santoso" @class([
                    'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                    'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                        'name'),
                    'border border-rose-500 focus:border-rose-500' => $formErrors->has('name'),
                ])>

            <x-form-error data-error-for="name" :messages="$formErrors->get('name')" />
        </div>

        <div>
            <label for="{{ $prefix }}_username" class="mb-2 block text-sm font-medium text-slate-900">
                Username
                <span class="text-red-600">*</span>
            </label>

            <input id="{{ $prefix }}_username" name="username" type="text" required autocomplete="username"
                value="{{ $fieldValue('username') }}" placeholder="Contoh: budisantoso" @class([
                    'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                    'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                        'username'),
                    'border border-rose-500 focus:border-rose-500' => $formErrors->has(
                        'username'),
                ])>

            <x-form-error data-error-for="username" :messages="$formErrors->get('username')" />
        </div>

        <div>
            <label for="{{ $prefix }}_email" class="mb-2 block text-sm font-medium text-slate-900">
                Email
                <span class="text-red-600">*</span>
            </label>

            <input id="{{ $prefix }}_email" name="email" type="email" required autocomplete="email"
                value="{{ $fieldValue('email') }}" placeholder="nama@perusahaan.co.id" @class([
                    'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                    'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                        'email'),
                    'border border-rose-500 focus:border-rose-500' => $formErrors->has('email'),
                ])>

            <x-form-error data-error-for="email" :messages="$formErrors->get('email')" />
        </div>

        <div>
            <label for="{{ $prefix }}_phone" class="mb-2 block text-sm font-medium text-slate-900">
                Nomor telepon
                <span class="text-red-600">*</span>
            </label>

            <input id="{{ $prefix }}_phone" name="phone" type="tel" required inputmode="tel"
                autocomplete="tel" maxlength="30" value="{{ $fieldValue('phone') }}"
                placeholder="Contoh: +6281234567890" @class([
                    'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                    'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                        'phone'),
                    'border border-rose-500 focus:border-rose-500' => $formErrors->has('phone'),
                ])>

            <x-form-error data-error-for="phone" :messages="$formErrors->get('phone')" />
        </div>
    </div>
</section>

{{-- Informasi perusahaan --}}
<div id="{{ $customerFieldsId }}" class="hidden space-y-6">
    <div class="border-t border-slate-200"></div>

    <section class="space-y-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">
                Informasi perusahaan
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Data ini hanya diperlukan untuk akun Pelanggan.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="{{ $prefix }}_company_name" class="mb-2 block text-sm font-medium text-slate-900">
                    Nama perusahaan
                    <span class="text-red-600">*</span>
                </label>

                <input id="{{ $prefix }}_company_name" name="company_name" type="text"
                    value="{{ $fieldValue('company_name') }}" placeholder="Contoh: PT. Magnum Ice Cream Indonesia"
                    @class([
                        'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                        'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                            'company_name'),
                        'border border-rose-500 focus:border-rose-500' => $formErrors->has(
                            'company_name'),
                    ])>

                <x-form-error data-error-for="company_name" :messages="$formErrors->get('company_name')" />
            </div>

            <div>
                <label for="{{ $prefix }}_company_phone" class="mb-2 block text-sm font-medium text-slate-900">
                    Telepon perusahaan
                    <span class="text-red-600">*</span>
                </label>

                <input id="{{ $prefix }}_company_phone" name="company_phone" type="tel" inputmode="tel"
                    autocomplete="tel" maxlength="30" value="{{ $fieldValue('company_phone') }}"
                    placeholder="Contoh: +62215550188" @class([
                        'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                        'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                            'company_phone'),
                        'border border-rose-500 focus:border-rose-500' => $formErrors->has(
                            'company_phone'),
                    ])>

                <x-form-error data-error-for="company_phone" :messages="$formErrors->get('company_phone')" />
            </div>

            <div class="md:col-span-2">
                <label for="{{ $prefix }}_address" class="mb-2 block text-sm font-medium text-slate-900">
                    Alamat perusahaan
                    <span class="text-red-600">*</span>
                </label>

                <textarea id="{{ $prefix }}_address" name="address" rows="3"
                    placeholder="Masukkan alamat lengkap perusahaan" @class([
                        'block w-full resize-y rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                        'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                            'address'),
                        'border border-rose-500 focus:border-rose-500' => $formErrors->has(
                            'address'),
                    ])>{{ $fieldValue('address') }}</textarea>

                <x-form-error data-error-for="address" :messages="$formErrors->get('address')" />
            </div>
        </div>
    </section>
</div>

<div class="border-t border-slate-200"></div>

{{-- Keamanan akun --}}
<section class="space-y-4">
    <div>
        <h3 class="text-sm font-semibold text-slate-900">
            Keamanan akun
        </h3>

        <p class="mt-1 text-xs text-slate-500">
            {{ $isEdit
                ? 'Kosongkan jika kata sandi tidak ingin diubah.'
                : 'Tentukan kata sandi awal yang digunakan untuk masuk ke sistem.' }}
        </p>
    </div>

    <div>
        <label for="{{ $prefix }}_password" class="mb-2 block text-sm font-medium text-slate-900">
            {{ $isEdit ? 'Kata sandi baru' : 'Kata sandi' }}

            @unless ($isEdit)
                <span class="text-red-600">*</span>
            @endunless
        </label>

        <input id="{{ $prefix }}_password" name="password" type="password" autocomplete="new-password"
            @required(!$isEdit) placeholder="{{ $isEdit ? 'Masukkan kata sandi baru' : 'Masukkan kata sandi' }}"
            @class([
                'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                'border border-slate-300 focus:border-blue-500' => !$formErrors->has(
                    'password'),
                'border border-rose-500 focus:border-rose-500' => $formErrors->has(
                    'password'),
            ])>

        <x-form-error data-error-for="password" :messages="$formErrors->get('password')" />
    </div>
</section>
