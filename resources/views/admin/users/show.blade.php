@extends('admin.layout')

@section('content')
    @php
        $nameParts = preg_split('/\s+/', trim($user->name));

        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn(string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        $isTechnician = $user->role === \App\Enums\UserRole::TECHNICIAN;
        $isCustomer = $user->role === \App\Enums\UserRole::CUSTOMER;

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

    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">

            {{-- Navigasi dan judul --}}
            <header>
                <div>
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-blue-700">

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" />
                        </svg>

                        Kembali ke Manajemen Pengguna
                    </a>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900">
                        Detail Pengguna
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi akun, aktivitas, dan data operasional pengguna.
                    </p>
                </div>
            </header>

            {{-- Profil dan informasi kontak --}}
            <div class="mt-4 grid items-stretch gap-4 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">

                {{-- Ringkasan pengguna --}}
                <section aria-label="Ringkasan pengguna"
                    class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="flex flex-1 flex-col gap-5 p-4 sm:p-5 lg:flex-row lg:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-4">
                            <div @class([
                                'flex size-20 shrink-0 items-center justify-center rounded-full text-2xl font-semibold',
                                'bg-blue-100 text-blue-700' => $isTechnician,
                                'bg-amber-100 text-amber-700' => $isCustomer,
                            ])>
                                {{ $initials ?: '?' }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-xl font-bold text-slate-950">
                                    {{ $user->name }}
                                </h2>

                                <p class="mt-1 truncate text-sm text-slate-500">
                                    &#64;{{ $user->username }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded border px-2.5 py-1 text-xs font-medium',
                                        'border-emerald-200 bg-emerald-50 text-emerald-700' => $isTechnician,
                                        'border-amber-200 bg-amber-50 text-amber-700' => $isCustomer,
                                    ])>
                                        @if ($isTechnician)
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path
                                                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z"
                                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" />
                                            </svg>
                                        @else
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path
                                                    d="M3 21h18M6 21V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v17M16 9h3a1 1 0 0 1 1 1v11"
                                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" />

                                                <path d="M9 7h1M9 11h1M9 15h1M13 7h1M13 11h1M13 15h1" stroke="currentColor"
                                                    stroke-linecap="round" stroke-width="2" />
                                            </svg>
                                        @endif

                                        {{ $user->role->label() }}
                                    </span>

                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" />
                                            </svg>

                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">

                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="M8 12h8" stroke="currentColor" stroke-linecap="round"
                                                    stroke-width="2" />
                                            </svg>

                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-2"> <button type="button" data-modal-target="editUserModal"
                                data-modal-toggle="editUserModal" data-edit-user
                                data-update-url="{{ route('admin.users.update', $user) }}"
                                data-user="{{ Illuminate\Support\Js::encode($editUserData) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">

                                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path d="M12 20h9" stroke="currentColor" stroke-linecap="round" stroke-width="2" />

                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Edit data
                            </button>

                            <form method="POST" action="{{ route('admin.users.status.update', $user) }}"
                                onsubmit="return confirm(
        @js($user->is_active ? "Nonaktifkan akun {$user->name}?" : "Aktifkan kembali akun {$user->name}?")
    )">

                                @csrf
                                @method('PATCH')

                                <button type="submit" @class([
                                    'inline-flex w-full items-center justify-center gap-2 rounded border bg-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-4 sm:w-auto',
                                    'border-rose-300 text-rose-700 hover:bg-rose-50 focus:ring-rose-100' =>
                                        $user->is_active,
                                    'border-emerald-300 text-emerald-700 hover:bg-emerald-50 focus:ring-emerald-100' => !$user->is_active,
                                ])>

                                    @if ($user->is_active)
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="9" cy="7" r="4" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="M2 21a7 7 0 0 1 11.5-5.4M16 16l5 5M21 16l-5 5" stroke="currentColor"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>

                                        Nonaktifkan akun
                                    @else
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="9" cy="7" r="4" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="M2 21a7 7 0 0 1 11.5-5.4M17 16v6M14 19h6" stroke="currentColor"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>

                                        Aktifkan akun
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </section>

                {{-- Informasi kontak --}}
                <section class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi Kontak
                        </h2>
                    </div>

                    <div class="flex flex-1 flex-col justify-center gap-4 p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-500">

                                <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="m3 7 9 6 9-6" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">
                                    Email
                                </p>

                                <p class="mt-0.5 truncate text-sm font-medium text-slate-800">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-500">

                                <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.77.68 2.6a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.48-1.25a2 2 0 0 1 2.11-.45c.83.33 1.7.56 2.6.68A2 2 0 0 1 22 16.92Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs text-slate-500">
                                    Nomor telepon
                                </p>

                                <p class="mt-0.5 break-all text-sm font-medium text-slate-800">
                                    {{ $user->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Informasi akun, operasional, dan aktivitas --}}
            <div class="mt-4 grid grid-cols-1 items-stretch gap-4 md:grid-cols-2 xl:grid-cols-12">

                {{-- Informasi akun --}}
                <section @class([
                    'flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm md:col-span-1',
                    'xl:col-span-2' => $isCustomer,
                    'xl:col-span-3' => $isTechnician,
                ])>
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi Akun
                        </h2>
                    </div>

                    <div class="flex-1 p-4">
                        <dl
                            class="grid gap-x-5 gap-y-4 text-sm sm:grid-cols-[minmax(130px,0.8fr)_minmax(0,1fr)] xl:grid-cols-1">

                            <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-x-4 xl:grid-cols-1 xl:gap-y-1">
                                <dt class="text-slate-500">
                                    Role
                                </dt>

                                <dd class="font-medium text-slate-800">
                                    {{ $user->role->label() }}
                                </dd>
                            </div>

                            <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-x-4 xl:grid-cols-1 xl:gap-y-1">
                                <dt class="text-slate-500">
                                    Tanggal akun dibuat
                                </dt>

                                <dd class="font-medium text-slate-800">
                                    {{ local_datetime($user->created_at, 'j F Y', translated: true) }}
                                </dd>
                            </div>

                            <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-x-4 xl:grid-cols-1 xl:gap-y-1">
                                <dt class="text-slate-500">
                                    Terakhir aktif
                                </dt>

                                <dd class="font-medium text-slate-800">
                                    @if ($user->last_active_at)
                                        {{ $user->last_active_at->locale('id')->diffForHumans() }},
                                        {{ local_datetime($user->last_active_at, 'H.i') }}
                                    @else
                                        Belum pernah
                                    @endif
                                </dd>
                            </div>

                            <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-x-4 xl:grid-cols-1 xl:gap-y-1">
                                <dt class="text-slate-500">
                                    Status akun
                                </dt>

                                <dd>
                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" />
                                            </svg>

                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-rose-700">
                                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="M8 12h8" stroke="currentColor" stroke-linecap="round"
                                                    stroke-width="2" />
                                            </svg>

                                            Nonaktif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </section>

                @if ($isCustomer)
                    {{-- Informasi perusahaan --}}
                    <section
                        class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm md:col-span-1 xl:col-span-3">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                            <h2 class="text-sm font-semibold text-slate-900">
                                Informasi Perusahaan
                            </h2>
                        </div>

                        <div class="flex-1 p-4">
                            @if ($user->customer)
                                <dl class="space-y-4 text-sm">
                                    <div>
                                        <dt class="text-slate-500">
                                            Nama perusahaan
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $user->customer->company_name }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-slate-500">
                                            Telepon perusahaan
                                        </dt>

                                        <dd class="mt-1 break-all font-medium text-slate-800">
                                            {{ $user->customer->phone }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-slate-500">
                                            Alamat perusahaan
                                        </dt>

                                        <dd class="mt-1 whitespace-pre-line font-medium leading-6 text-slate-800">
                                            {{ $user->customer->address }}
                                        </dd>
                                    </div>
                                </dl>
                            @else
                                <div class="flex h-full min-h-36 items-center justify-center text-center">
                                    <div>
                                        <svg class="mx-auto size-8 text-slate-300" aria-hidden="true" fill="none"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M3 21h18M6 21V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v17M16 9h3a1 1 0 0 1 1 1v11"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" />
                                        </svg>

                                        <p class="mt-2 text-sm font-medium text-slate-700">
                                            Profil perusahaan belum tersedia
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Informasi operasional --}}
                <section @class([
                    'flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm md:col-span-1',
                    'xl:col-span-3',
                ])>

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi Operasional
                        </h2>
                    </div>

                    <div class="flex-1 space-y-4 p-4">

                        @if ($isTechnician)
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-blue-50 text-blue-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />

                                        <rect x="9" y="2" width="6" height="4" rx="1"
                                            stroke="currentColor" stroke-width="2" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-slate-950">
                                        {{ $user->active_repairs_count }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Tugas aktif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-emerald-50 text-emerald-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-slate-950">
                                        {{ $user->completed_repairs_count }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Tugas selesai
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-blue-50 text-blue-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M12 2v20M5 5l14 14M19 5 5 19M2 12h20" stroke="currentColor"
                                            stroke-linecap="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-slate-950">
                                        {{ $user->customer?->freezers_count ?? 0 }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Freezer terdaftar
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-amber-50 text-amber-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-slate-950">
                                        {{ $user->customer?->active_repairs_count ?? 0 }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Reparasi aktif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-emerald-50 text-emerald-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-slate-950">
                                        {{ $user->customer?->completed_repairs_count ?? 0 }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Reparasi selesai
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="border-t-[1.6px] border-slate-100 pt-4">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 size-5 shrink-0 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                <div>
                                    <p class="text-xs text-slate-500">
                                        Aktivitas reparasi terakhir
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-slate-800">
                                        @if ($activities->isNotEmpty())
                                            {{ $activities->first()->created_at->locale('id')->diffForHumans() }}
                                        @else
                                            Belum ada aktivitas
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Aktivitas terbaru --}}
                <section @class([
                    'flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm md:col-span-2',
                    'xl:col-span-4' => $isCustomer,
                    'xl:col-span-6' => $isTechnician,
                ])>

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Aktivitas Terbaru
                        </h2>
                    </div>

                    <div class="flex-1 p-4">
                        @if ($activities->isNotEmpty())
                            <ol
                                class="relative ms-2 before:absolute before:inset-y-3 before:start-0 before:w-px before:bg-slate-200">

                                @foreach ($activities as $activity)
                                    @php
                                        $freezer = $activity->repair?->freezer;

                                        $freezerIdentity =
                                            $freezer?->serial_number ?:
                                            collect([$freezer?->brand, $freezer?->model])
                                                ->filter()
                                                ->implode(' ') ?:
                                            'Unit freezer';
                                    @endphp

                                    <li @class([
                                        'relative ms-5 py-3',
                                        'border-b-[1.6px] border-slate-100' => !$loop->last,
                                    ])>
                                        @if ($loop->first)
                                            <span
                                                class="absolute -start-[27px] top-[13px] z-10 flex size-4 items-center justify-center rounded-full border-2 border-blue-600 bg-white">

                                                <span class="size-1.5 rounded-full bg-blue-600"></span>
                                            </span>
                                        @else
                                            <span
                                                class="absolute -start-[25px] top-[15px] z-10 size-3 rounded-full bg-blue-600 ring-2 ring-white">
                                            </span>
                                        @endif

                                        <div
                                            class="grid gap-1 text-sm sm:grid-cols-[130px_minmax(0,1fr)] sm:gap-x-4 xl:grid-cols-[120px_minmax(0,1fr)]">

                                            <time class="text-slate-500"
                                                datetime="{{ $activity->created_at->toIso8601String() }}">

                                                {{ $activity->created_at->locale('id')->diffForHumans() }}
                                            </time>

                                            <div class="min-w-0">
                                                <p @class([
                                                    'text-slate-800',
                                                    'font-semibold' => $loop->first,
                                                    'font-medium' => !$loop->first,
                                                ])>
                                                    {{ $activity->description }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $activity->status->label() }}
                                                    <span aria-hidden="true">·</span>
                                                    {{ $freezerIdentity }}

                                                    @if ($isCustomer && $activity->updater)
                                                        <span aria-hidden="true">·</span>
                                                        {{ $activity->updater->name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <div class="flex min-h-48 items-center justify-center text-center">
                                <div>
                                    <div
                                        class="mx-auto flex size-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                                        <svg class="size-6" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path
                                                d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" />

                                            <rect x="9" y="2" width="6" height="4" rx="1"
                                                stroke="currentColor" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-800">
                                        Belum ada aktivitas reparasi
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Aktivitas akan tampil setelah terdapat pembaruan reparasi.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </main>

    @include('admin.users.partials.edit-user')
@endsection
