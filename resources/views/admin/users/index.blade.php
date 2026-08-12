@extends('admin.layout')

@section('content')
    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">
            @include('admin.users.partials.user-summary')

            {{-- Daftar pengguna --}}
            <section class="mt-4 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                {{-- Filter peran --}}
                <nav class="border-b border-slate-200 px-4" aria-label="Filter peran pengguna">
                    <div class="-mb-px flex gap-6 overflow-x-auto text-sm font-medium">
                        <a href="{{ route(
                            'admin.users.index',
                            array_filter(
                                [
                                    'search' => $search,
                                    'status' => $selectedStatus,
                                    'sort' => $selectedSort !== 'newest' ? $selectedSort : null,
                                    'per_page' => $perPage,
                                ],
                                fn($value) => $value !== null && $value !== '',
                            ),
                        ) }}"
                            @class([
                                'inline-flex items-center gap-2 border-b-2 px-1 py-3.5',
                                'border-blue-600 text-blue-600' => $selectedRole === null,
                                'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' =>
                                    $selectedRole !== null,
                            ])>
                            Semua pengguna

                            <span @class([
                                'inline-flex min-w-6 items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-blue-50 text-blue-600' => $selectedRole === null,
                                'bg-slate-100 text-slate-600' => $selectedRole !== null,
                            ])>
                                {{ $totalUserCount }}
                            </span>
                        </a>

                        <a href="{{ route(
                            'admin.users.index',
                            array_filter(
                                [
                                    'role' => \App\Enums\UserRole::TECHNICIAN->value,
                                    'search' => $search,
                                    'status' => $selectedStatus,
                                    'sort' => $selectedSort !== 'newest' ? $selectedSort : null,
                                    'per_page' => $perPage,
                                ],
                                fn($value) => $value !== null && $value !== '',
                            ),
                        ) }}"
                            @class([
                                'inline-flex items-center gap-2 border-b-2 px-1 py-3.5',
                                'border-blue-600 text-blue-600' =>
                                    $selectedRole === \App\Enums\UserRole::TECHNICIAN->value,
                                'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' =>
                                    $selectedRole !== \App\Enums\UserRole::TECHNICIAN->value,
                            ])>
                            Teknisi

                            <span @class([
                                'inline-flex min-w-6 items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-blue-50 text-blue-600' =>
                                    $selectedRole === \App\Enums\UserRole::TECHNICIAN->value,
                                'bg-slate-100 text-slate-600' =>
                                    $selectedRole !== \App\Enums\UserRole::TECHNICIAN->value,
                            ])>
                                {{ $technicianCount }}
                            </span>
                        </a>

                        <a href="{{ route(
                            'admin.users.index',
                            array_filter(
                                [
                                    'role' => \App\Enums\UserRole::CUSTOMER->value,
                                    'search' => $search,
                                    'status' => $selectedStatus,
                                    'sort' => $selectedSort !== 'newest' ? $selectedSort : null,
                                    'per_page' => $perPage,
                                ],
                                fn($value) => $value !== null && $value !== '',
                            ),
                        ) }}"
                            @class([
                                'inline-flex items-center gap-2 border-b-2 px-1 py-3.5',
                                'border-blue-600 text-blue-600' =>
                                    $selectedRole === \App\Enums\UserRole::CUSTOMER->value,
                                'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' =>
                                    $selectedRole !== \App\Enums\UserRole::CUSTOMER->value,
                            ])>
                            Pelanggan

                            <span @class([
                                'inline-flex min-w-6 items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-blue-50 text-blue-600' =>
                                    $selectedRole === \App\Enums\UserRole::CUSTOMER->value,
                                'bg-slate-100 text-slate-600' =>
                                    $selectedRole !== \App\Enums\UserRole::CUSTOMER->value,
                            ])>
                                {{ $customerCount }}
                            </span>
                        </a>
                    </div>
                </nav>

                {{-- Toolbar tabel --}}
                @include('admin.users.partials.user-toolbar')

                {{-- Tabel pengguna --}}
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full whitespace-nowrap text-left text-sm text-slate-500">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">
                                    Pengguna
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    Peran
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    Kontak
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    Aktivitas
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    Status akun
                                </th>

                                <th scope="col" class="px-4 py-3">
                                    Terakhir aktif
                                </th>

                                <th scope="col" class="px-4 py-3 text-right">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $nameParts = preg_split('/\s+/', trim($user->name));
                                    $initials = collect($nameParts)
                                        ->filter()
                                        ->take(2)
                                        ->map(fn(string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                                        ->implode('');

                                    $isTechnician = $user->role === \App\Enums\UserRole::TECHNICIAN;
                                    $isCustomer = $user->role === \App\Enums\UserRole::CUSTOMER;
                                @endphp

                                <tr class="border-b border-slate-200 bg-white hover:bg-slate-50">
                                    {{-- Pengguna --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div @class([
                                                'flex size-10 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                                                'bg-blue-100 text-blue-700' => $isTechnician,
                                                'bg-amber-100 text-amber-700' => $isCustomer,
                                            ])>
                                                {{ $initials ?: '?' }}
                                            </div>

                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-slate-900">
                                                    {{ $user->name }}
                                                </p>

                                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                                    &#64;{{ $user->username }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Peran --}}
                                    <td class="px-4 py-3">
                                        @if ($isTechnician)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z"
                                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                </svg>

                                                Teknisi
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <path
                                                        d="M3 21h18M6 21V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v17M16 9h3a1 1 0 0 1 1 1v11"
                                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />

                                                    <path d="M9 7h1M9 11h1M9 15h1M13 7h1M13 11h1M13 15h1"
                                                        stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                                </svg>

                                                Pelanggan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kontak --}}
                                    <td class="px-4 py-3">
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <svg class="size-4 shrink-0 text-slate-400" aria-hidden="true"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <path
                                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.77.68 2.6a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.48-1.25a2 2 0 0 1 2.11-.45c.83.33 1.7.56 2.6.68A2 2 0 0 1 22 16.92Z"
                                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                </svg>

                                                <span class="font-medium text-slate-800">
                                                    {{ $user->phone ?: '—' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <svg class="size-4 shrink-0 text-slate-400" aria-hidden="true"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <rect x="3" y="5" width="18" height="14" rx="2"
                                                        stroke="currentColor" stroke-width="2" />

                                                    <path d="m3 7 9 6 9-6" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" />
                                                </svg>

                                                <span class="truncate text-xs text-slate-500">
                                                    {{ $user->email }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Aktivitas --}}
                                    <td class="px-4 py-3">
                                        @if ($isTechnician)
                                            <p class="font-medium text-slate-800">
                                                {{ $user->active_repairs_count }} tugas aktif
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ $user->completed_repairs_count }} tugas selesai
                                            </p>
                                        @else
                                            <p class="font-medium text-slate-800">
                                                {{ $user->customer?->freezers_count ?? 0 }} freezer terdaftar
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ $user->customer?->active_repairs_count ?? 0 }} reparasi aktif
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Status akun --}}
                                    <td class="px-4 py-3">
                                        @if ($user->is_active)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                </svg>

                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">
                                                <svg class="size-3.5" aria-hidden="true" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="M8 12h8" stroke="currentColor" stroke-linecap="round"
                                                        stroke-width="2" />
                                                </svg>

                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Terakhir aktif --}}
                                    <td class="px-4 py-3">
                                        @if ($user->last_active_at)
                                            <p class="font-medium text-slate-800">
                                                {{ $user->last_active_at->locale('id')->diffForHumans() }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ local_datetime($user->last_active_at, 'H.i') }}
                                            </p>
                                        @else
                                            <p class="font-medium text-slate-500">
                                                Belum pernah
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                —
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-3 text-right">
                                        <button id="userActionButton{{ $user->id }}"
                                            data-dropdown-toggle="userActionDropdown{{ $user->id }}"
                                            data-dropdown-placement="bottom-end" type="button"
                                            aria-label="Buka aksi pengguna {{ $user->name }}"
                                            class="inline-flex size-9 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">

                                            <svg class="size-5" aria-hidden="true" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.8" />
                                                <circle cx="12" cy="12" r="1.8" />
                                                <circle cx="12" cy="19" r="1.8" />
                                            </svg>
                                        </button>

                                        <div id="userActionDropdown{{ $user->id }}"
                                            class="z-50 hidden w-48 rounded border border-slate-200 bg-white p-1.5 text-left shadow-lg">

                                            <ul class="space-y-1 text-sm text-slate-700"
                                                aria-labelledby="userActionButton{{ $user->id }}">

                                                <li>
                                                    <a href="{{ route('admin.users.show', $user) }}"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left hover:bg-slate-100">

                                                        <svg class="size-4 text-slate-500" aria-hidden="true"
                                                            fill="none" viewBox="0 0 24 24">
                                                            <path
                                                                d="M2.06 12.35a1 1 0 0 1 0-.7C3.73 7.49 7.39 5 12 5s8.27 2.49 9.94 6.65a1 1 0 0 1 0 .7C20.27 16.51 16.61 19 12 19s-8.27-2.49-9.94-6.65Z"
                                                                stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" />

                                                            <circle cx="12" cy="12" r="3"
                                                                stroke="currentColor" stroke-width="2" />
                                                        </svg>

                                                        Lihat detail
                                                    </a>
                                                </li>

                                                <li>
                                                    @php
                                                        $editUserData = [
                                                            'id' => $user->id,
                                                            'role' => $user->role->value,
                                                            'name' => $user->name,
                                                            'username' => $user->username,
                                                            'email' => $user->email,
                                                            'phone' => $user->phone,
                                                            'company_name' => $user->customer?->company_name,
                                                            'company_phone' => $user->customer?->phone,
                                                            'address' => $user->customer?->address,
                                                        ];
                                                    @endphp

                                                    <button type="button" data-modal-target="editUserModal"
                                                        data-modal-toggle="editUserModal" data-edit-user
                                                        data-update-url="{{ route('admin.users.update', $user) }}"
                                                        data-user="{{ Illuminate\Support\Js::encode($editUserData) }}"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left hover:bg-slate-100">
                                                        <svg class="size-4 text-slate-500" aria-hidden="true"
                                                            fill="none" viewBox="0 0 24 24">
                                                            <path d="M12 20h9" stroke="currentColor"
                                                                stroke-linecap="round" stroke-width="2" />

                                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"
                                                                stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" />
                                                        </svg>

                                                        Edit pengguna
                                                    </button>
                                                </li>

                                                <li class="border-t border-slate-100 pt-1">
                                                    <form method="POST"
                                                        action="{{ route('admin.users.status.update', $user) }}"
                                                        onsubmit="return confirm(
            @js($user->is_active ? "Nonaktifkan akun {$user->name}?" : "Aktifkan kembali akun {$user->name}?")
        )">

                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit" @class([
                                                            'flex w-full items-center gap-2 rounded px-3 py-2 text-left',
                                                            'text-rose-700 hover:bg-rose-50' => $user->is_active,
                                                            'text-emerald-700 hover:bg-emerald-50' => !$user->is_active,
                                                        ])>

                                                            @if ($user->is_active)
                                                                <svg class="size-4 shrink-0" aria-hidden="true"
                                                                    fill="none" viewBox="0 0 24 24">
                                                                    <circle cx="12" cy="12" r="9"
                                                                        stroke="currentColor" stroke-width="2" />

                                                                    <path d="M8 12h8" stroke="currentColor"
                                                                        stroke-linecap="round" stroke-width="2" />
                                                                </svg>

                                                                Nonaktifkan akun
                                                            @else
                                                                <svg class="size-4 shrink-0" aria-hidden="true"
                                                                    fill="none" viewBox="0 0 24 24">
                                                                    <circle cx="12" cy="12" r="9"
                                                                        stroke="currentColor" stroke-width="2" />

                                                                    <path d="M12 8v8M8 12h8" stroke="currentColor"
                                                                        stroke-linecap="round" stroke-width="2" />
                                                                </svg>

                                                                Aktifkan akun
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada pengguna
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Tambahkan akun teknisi atau pelanggan untuk mulai menggunakan sistem.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer tabel --}}
                <x-table-pagination :paginator="$users" :action="route('admin.users.index')" :per-page="$perPage" :query="[
                    'search' => $search,
                    'status' => $selectedStatus,
                    'role' => $selectedRole,
                    'sort' => $selectedSort,
                ]"
                    item-label="pengguna" aria-label="Pagination pengguna" />
            </section>
        </div>

        @include('admin.users.partials.create-user')
        @include('admin.users.partials.edit-user')

    </main>
@endsection
