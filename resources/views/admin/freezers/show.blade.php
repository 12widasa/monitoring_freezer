@extends('admin.layout')

@section('title', "Detail {$freezer->freezer_code} - Karyatama System")

@section('content')
    @php
        $latestIntake = $freezer->latestIntake;
        $repair = $latestIntake?->repair;
        $repairStatus = $repair?->status;
        $verificationStatus = $latestIntake?->status_verifikasi;

        $indexQuery = request()->only(['search', 'repair_status', 'verification_status', 'sort', 'per_page', 'page']);

        $repairBadgeClass = match ($repairStatus) {
            \App\Enums\RepairStatus::QUEUED => 'border-amber-200 bg-amber-50 text-amber-700',

            \App\Enums\RepairStatus::INSPECTING => 'border-blue-200 bg-blue-50 text-blue-700',

            \App\Enums\RepairStatus::REPAIRING => 'border-amber-200 bg-amber-50 text-amber-700',

            \App\Enums\RepairStatus::COMPLETED => 'border-emerald-200 bg-emerald-50 text-emerald-700',

            default => 'border-slate-200 bg-slate-100 text-slate-700',
        };

        $repairLabel = match ($repairStatus) {
            \App\Enums\RepairStatus::QUEUED => 'Menunggu diperiksa',
            \App\Enums\RepairStatus::INSPECTING => 'Sedang diperiksa',
            \App\Enums\RepairStatus::REPAIRING => 'Sedang diperbaiki',
            \App\Enums\RepairStatus::COMPLETED => 'Perbaikan selesai',
            default => 'Belum ada reparasi',
        };

        $verificationBadgeClass = match ($verificationStatus) {
            \App\Enums\VerificationStatus::VERIFIED => 'border-emerald-200 bg-emerald-50 text-emerald-700',

            \App\Enums\VerificationStatus::REJECTED => 'border-rose-200 bg-rose-50 text-rose-700',

            \App\Enums\VerificationStatus::PENDING_ARRIVAL => 'border-amber-200 bg-amber-50 text-amber-700',

            default => 'border-slate-200 bg-slate-100 text-slate-600',
        };

        $verificationLabel = match ($verificationStatus) {
            \App\Enums\VerificationStatus::VERIFIED => 'Sudah diverifikasi',

            \App\Enums\VerificationStatus::REJECTED => 'Ditolak',

            \App\Enums\VerificationStatus::PENDING_ARRIVAL => 'Belum diverifikasi',

            default => 'Penerimaan tidak tersedia',
        };

        $photoUrl = $freezer->photo_path ? \Illuminate\Support\Facades\Storage::url($freezer->photo_path) : null;
    @endphp

    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">

            {{-- Navigasi --}}
            <header>
                <a href="{{ route('admin.freezers.index', $indexQuery) }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-blue-700">

                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" />
                    </svg>

                    Kembali ke Manajemen Freezer
                </a>

                <div class="mt-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                                {{ $freezer->freezer_code }}
                            </h1>

                            <span
                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $verificationBadgeClass }}">
                                {{ $verificationLabel }}
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $freezer->brand }} {{ $freezer->model }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($verificationStatus === \App\Enums\VerificationStatus::PENDING_ARRIVAL)
                            <button type="button" data-modal-target="verifyFreezerModal"
                                data-modal-toggle="verifyFreezerModal" data-verify-freezer
                                data-freezer-id="{{ $freezer->id }}" data-freezer-code="{{ $freezer->freezer_code }}"
                                data-verification-url="{{ route('admin.freezers.verification.update', [
                                    'freezer' => $freezer,
                                    ...$indexQuery,
                                ]) }}"
                                class="inline-flex items-center justify-center gap-2 rounded bg-blue-700 px-3 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">

                                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Verifikasi freezer
                            </button>
                        @endif

                        @if ($eligibleIntakes->isNotEmpty())
                            <button type="button" data-modal-target="createRepairModal"
                                data-modal-toggle="createRepairModal"
                                class="inline-flex items-center justify-center gap-2 rounded bg-blue-700 px-3 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">

                                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Buat reparasi
                            </button>
                        @endif
                    </div>
                </div>
            </header>

            {{-- Foto dan informasi utama --}}
            <section class="mt-4">
                <div class="grid items-stretch gap-4 lg:grid-cols-[minmax(320px,0.85fr)_minmax(0,1.15fr)]">

                    {{-- Foto freezer --}}
                    <section class="flex min-h-0 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                        <div class="flex w-full p-4">
                            @if ($photoUrl)
                                <div
                                    class="flex min-h-72 w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 p-3 lg:min-h-[34rem]">

                                    <img src="{{ $photoUrl }}" alt="Foto freezer {{ $freezer->freezer_code }}"
                                        class="max-h-[42rem] max-w-full rounded object-contain">
                                </div>
                            @else
                                <div
                                    class="flex min-h-72 w-full flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center lg:min-h-[34rem]">

                                    <svg class="size-10 text-slate-400" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">

                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor"
                                            stroke-width="2" />

                                        <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m21 15-5-5L5 21" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    <p class="mt-3 text-sm font-medium text-slate-700">
                                        Foto freezer belum tersedia
                                    </p>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Informasi di sebelah kanan --}}
                    <div class="grid min-w-0 gap-4">

                        {{-- Informasi unit --}}
                        <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-sm font-semibold text-slate-900">
                                        Informasi Unit
                                    </h2>

                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $repairBadgeClass }}">

                                        {{ $repairLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 sm:p-5">
                                <div class="mb-5 flex items-center gap-3">
                                    <div
                                        class="flex size-11 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">

                                        <svg class="size-6" aria-hidden="true" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            viewBox="0 0 24 24">

                                            <line x1="2" x2="22" y1="12" y2="12" />
                                            <line x1="12" x2="12" y1="2" y2="22" />
                                            <path d="m20 16-4-4 4-4" />
                                            <path d="m4 8 4 4-4 4" />
                                            <path d="m16 4-4 4-4-4" />
                                            <path d="m8 20 4-4 4 4" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-lg font-bold text-slate-950">
                                            {{ $freezer->freezer_code }}
                                        </p>

                                        <p class="mt-0.5 truncate text-sm text-slate-500">
                                            {{ $freezer->brand }} {{ $freezer->model }}
                                        </p>
                                    </div>
                                </div>

                                <dl class="grid gap-x-6 gap-y-5 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Kode freezer
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->freezer_code }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Nomor seri
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->serial_number ?: 'Tidak tersedia' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Merek
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->brand }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Model
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->model }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Kapasitas
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->capacity_liter
                                                ? number_format($freezer->capacity_liter, 0, ',', '.') . ' liter'
                                                : 'Tidak tersedia' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Perkiraan usia
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $freezer->estimated_age ?: 'Tidak tersedia' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Ditambahkan
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ local_datetime($freezer->created_at, 'd F Y, H.i', translated: true) }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Terakhir diperbarui
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ local_datetime($freezer->updated_at, 'd F Y, H.i', translated: true) }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </section>

                        {{-- Informasi pemilik --}}
                        <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                                <h2 class="text-sm font-semibold text-slate-900">
                                    Informasi Pemilik
                                </h2>
                            </div>

                            <div class="grid gap-5 p-4 text-sm sm:grid-cols-2 sm:p-5">
                                <div>
                                    <p class="text-xs text-slate-500">
                                        Perusahaan
                                    </p>

                                    <p class="mt-1 font-medium text-slate-800">
                                        {{ $freezer->customer->company_name }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-500">
                                        Nomor telepon
                                    </p>

                                    <p class="mt-1 font-medium text-slate-800">
                                        {{ $freezer->customer->phone ?: 'Tidak tersedia' }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs text-slate-500">
                                        Alamat
                                    </p>

                                    <p class="mt-1 whitespace-pre-line font-medium leading-6 text-slate-800">
                                        {{ $freezer->customer->address ?: 'Tidak tersedia' }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            {{-- Informasi utama --}}
            <div class="mt-4 grid items-start gap-4 lg:grid-cols-12">

                {{-- Keluhan penerimaan terbaru --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm lg:col-span-7">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold text-slate-900">
                                Keluhan Penerimaan Terbaru
                            </h2>

                            @if ($latestIntake)
                                <span
                                    class="inline-flex items-center rounded border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">

                                    {{ $latestIntake->intake_code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4">
                        @if ($latestIntake)
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $latestIntake->complaint_note ?: 'Belum ada keluhan yang dicatat pada penerimaan ini.' }}
                            </p>

                            <div class="mt-4 border-t border-slate-100 pt-3">
                                <p class="text-xs text-slate-500">
                                    Diterima pada
                                    {{ local_datetime($latestIntake->received_at, 'd F Y, H.i', translated: true) }}

                                    @if ($latestIntake->receiver)
                                        oleh {{ $latestIntake->receiver->name }}
                                    @endif
                                </p>
                            </div>
                        @else
                            <p class="text-sm leading-6 text-slate-500">
                                Freezer ini belum memiliki catatan penerimaan unit.
                            </p>
                        @endif
                    </div>
                </section>

                {{-- Status verifikasi --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm lg:col-span-5">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Status Verifikasi
                        </h2>
                    </div>

                    <div class="p-4">
                        @if ($latestIntake)
                            <div @class([
                                'rounded border p-4',
                                'border-amber-200 bg-amber-50 text-amber-800' =>
                                    $verificationStatus === \App\Enums\VerificationStatus::PENDING_ARRIVAL,
                            
                                'border-emerald-200 bg-emerald-50 text-emerald-800' =>
                                    $verificationStatus === \App\Enums\VerificationStatus::VERIFIED,
                            
                                'border-rose-200 bg-rose-50 text-rose-800' =>
                                    $verificationStatus === \App\Enums\VerificationStatus::REJECTED,
                            ])>

                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <p class="text-sm font-semibold">
                                        {{ $verificationLabel }}
                                    </p>

                                    <span class="text-xs font-medium">
                                        {{ $latestIntake->intake_code }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm leading-6">
                                    @if ($verificationStatus === \App\Enums\VerificationStatus::PENDING_ARRIVAL)
                                        Unit masih menunggu pemeriksaan dan pencocokan data fisik.
                                    @elseif ($verificationStatus === \App\Enums\VerificationStatus::VERIFIED)
                                        Data freezer telah dicocokkan dengan unit fisik.
                                    @else
                                        Data freezer ditolak saat proses pemeriksaan fisik.
                                    @endif
                                </p>

                                @if ($verificationStatus === \App\Enums\VerificationStatus::REJECTED)
                                    <div class="mt-3 border-t border-rose-200 pt-3">
                                        <p class="text-xs font-medium">
                                            Alasan penolakan
                                        </p>

                                        <p class="mt-1 whitespace-pre-line text-sm">
                                            {{ $latestIntake->rejection_reason ?: 'Alasan penolakan tidak dicatat.' }}
                                        </p>
                                    </div>
                                @endif

                                @if ($latestIntake->verified_at)
                                    <div class="mt-3 border-t border-current/20 pt-3">
                                        <p class="text-xs">
                                            Diproses pada
                                            {{ local_datetime($latestIntake->verified_at, 'd F Y, H.i', translated: true) }}

                                            @if ($latestIntake->verifier)
                                                oleh {{ $latestIntake->verifier->name }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="rounded border border-slate-200 bg-slate-50 p-4 text-slate-600">

                                <p class="text-sm font-semibold">
                                    Penerimaan tidak tersedia
                                </p>

                                <p class="mt-1 text-sm leading-6">
                                    Freezer ini belum memiliki catatan penerimaan unit.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Riwayat penerimaan unit --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm lg:col-span-12">

                    <div
                        class="flex flex-wrap items-start justify-between gap-3 border-b-[1.6px] border-slate-100 px-4 py-3">

                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">
                                Riwayat Penerimaan Unit
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Daftar setiap kedatangan freezer untuk pemeriksaan atau
                                reparasi.
                            </p>
                        </div>

                        <span
                            class="inline-flex items-center rounded border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">

                            {{ $freezer->serviceIntakes->count() }} penerimaan
                        </span>
                    </div>

                    <div class="p-4">
                        @forelse ($freezer->serviceIntakes as $intake)
                            @php
                                $intakeStatus = $intake->status_verifikasi;

                                $intakeBadgeClass = match ($intakeStatus) {
                                    \App\Enums\VerificationStatus::VERIFIED
                                        => 'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    \App\Enums\VerificationStatus::REJECTED
                                        => 'border-rose-200 bg-rose-50 text-rose-700',

                                    default => 'border-amber-200 bg-amber-50 text-amber-700',
                                };

                                $intakeStatusLabel = match ($intakeStatus) {
                                    \App\Enums\VerificationStatus::VERIFIED => 'Sudah diverifikasi',

                                    \App\Enums\VerificationStatus::REJECTED => 'Ditolak',

                                    default => 'Menunggu verifikasi',
                                };

                                $intakeRepair = $intake->repair;
                            @endphp

                            <article @class([
                                'rounded border p-4',
                                'mt-3' => !$loop->first,
                                'border-blue-200 bg-blue-50/40' => $loop->first,
                                'border-slate-200 bg-slate-50' => !$loop->first,
                            ])>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $intake->intake_code }}
                                            </p>

                                            @if ($loop->first)
                                                <span
                                                    class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700">

                                                    Terbaru
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Diterima pada
                                            {{ local_datetime($intake->received_at, 'd F Y, H.i', translated: true) }}

                                            @if ($intake->receiver)
                                                oleh {{ $intake->receiver->name }}
                                            @endif
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex w-fit items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $intakeBadgeClass }}">

                                        {{ $intakeStatusLabel }}
                                    </span>
                                </div>

                                <dl class="mt-4 grid gap-4 text-sm md:grid-cols-2">
                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Keluhan
                                        </dt>

                                        <dd class="mt-1 whitespace-pre-line font-medium leading-6 text-slate-800">

                                            {{ $intake->complaint_note ?: 'Tidak ada keluhan yang dicatat.' }}
                                        </dd>
                                    </div>

                                    @if ($intake->condition_note)
                                        <div>
                                            <dt class="text-xs text-slate-500">
                                                Kondisi saat diterima
                                            </dt>

                                            <dd class="mt-1 whitespace-pre-line font-medium leading-6 text-slate-800">

                                                {{ $intake->condition_note }}
                                            </dd>
                                        </div>
                                    @endif
                                </dl>

                                @if ($intakeStatus === \App\Enums\VerificationStatus::REJECTED)
                                    <div class="mt-4 rounded border border-rose-200 bg-rose-50 p-3 text-rose-800">

                                        <p class="text-xs font-semibold">
                                            Alasan penolakan
                                        </p>

                                        <p class="mt-1 whitespace-pre-line text-sm leading-6">
                                            {{ $intake->rejection_reason ?: 'Alasan penolakan tidak dicatat.' }}
                                        </p>
                                    </div>
                                @endif

                                <div
                                    class="mt-4 flex flex-col gap-3 border-t border-slate-200 pt-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div class="text-xs text-slate-500">
                                        @if ($intake->verified_at)
                                            Diverifikasi pada
                                            {{ local_datetime($intake->verified_at, 'd F Y, H.i', translated: true) }}

                                            @if ($intake->verifier)
                                                oleh {{ $intake->verifier->name }}
                                            @endif
                                        @else
                                            Belum diproses verifikasi
                                        @endif
                                    </div>

                                    @if ($intakeRepair)
                                        <a href="{{ route('admin.repairs.show', $intakeRepair) }}"
                                            class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-700 hover:text-blue-800">

                                            Lihat reparasi #{{ $intakeRepair->id }}

                                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                                <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </a>
                                    @elseif ($intakeStatus === \App\Enums\VerificationStatus::VERIFIED)
                                        <span class="text-xs font-medium text-amber-700">

                                            Belum dibuatkan reparasi
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">
                                            Tidak memiliki reparasi
                                        </span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div
                                class="flex flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">

                                <svg class="size-9 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">

                                    <path d="M6 3h12v18H6zM9 8h6M9 12h6M9 16h4" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                <p class="mt-3 text-sm font-semibold text-slate-800">
                                    Belum ada riwayat penerimaan
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Freezer ini belum memiliki catatan penerimaan unit.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Riwayat reparasi --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm lg:col-span-12">

                    <div
                        class="flex flex-wrap items-start justify-between gap-3 border-b-[1.6px] border-slate-100 px-4 py-3">

                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">
                                Riwayat Reparasi
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Daftar seluruh penanganan reparasi freezer ini.
                            </p>
                        </div>

                        <span
                            class="inline-flex items-center rounded border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">

                            {{ $freezer->repairs->count() }} reparasi
                        </span>
                    </div>

                    <div class="p-4">
                        @forelse ($freezer->repairs as $repairItem)
                            @php
                                $repairItemStatus = $repairItem->status;

                                $repairItemBadgeClass = match ($repairItemStatus) {
                                    \App\Enums\RepairStatus::QUEUED => 'border-amber-200 bg-amber-50 text-amber-700',

                                    \App\Enums\RepairStatus::INSPECTING => 'border-blue-200 bg-blue-50 text-blue-700',

                                    \App\Enums\RepairStatus::REPAIRING => 'border-amber-200 bg-amber-50 text-amber-700',

                                    \App\Enums\RepairStatus::COMPLETED
                                        => 'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    default => 'border-slate-200 bg-slate-100 text-slate-700',
                                };

                                $repairItemLabel = match ($repairItemStatus) {
                                    \App\Enums\RepairStatus::QUEUED => 'Menunggu diperiksa',

                                    \App\Enums\RepairStatus::INSPECTING => 'Sedang diperiksa',

                                    \App\Enums\RepairStatus::REPAIRING => 'Sedang diperbaiki',

                                    \App\Enums\RepairStatus::COMPLETED => 'Perbaikan selesai',

                                    default => 'Status tidak tersedia',
                                };
                            @endphp

                            <article @class([
                                'rounded border p-4',
                                'mt-3' => !$loop->first,
                                'border-blue-200 bg-blue-50/40' => $loop->first,
                                'border-slate-200 bg-slate-50' => !$loop->first,
                            ])>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-900">
                                                Reparasi #{{ $repairItem->id }}
                                            </p>

                                            @if ($loop->first)
                                                <span
                                                    class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700">

                                                    Terbaru
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Dibuat pada
                                            {{ local_datetime($repairItem->created_at, 'd F Y, H.i', translated: true) }}
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex w-fit items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $repairItemBadgeClass }}">

                                        {{ $repairItemLabel }}
                                    </span>
                                </div>

                                <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Penerimaan unit
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $repairItem->serviceIntake?->intake_code ?: 'Tidak tersedia' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Teknisi
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $repairItem->technician?->name ?: 'Belum ditugaskan' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Admin pencatat
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $repairItem->admin?->name ?: 'Tidak tersedia' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Status
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ $repairItemLabel }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-slate-500">
                                            Terakhir diperbarui
                                        </dt>

                                        <dd class="mt-1 font-medium text-slate-800">
                                            {{ local_datetime($repairItem->updated_at, 'd F Y, H.i', translated: true) }}
                                        </dd>
                                    </div>

                                    <div class="md:col-span-2 xl:col-span-3">
                                        <dt class="text-xs text-slate-500">
                                            Analisis awal
                                        </dt>

                                        <dd class="mt-1 whitespace-pre-line font-medium leading-6 text-slate-800">

                                            {{ $repairItem->initial_analysis ?: 'Belum ada analisis awal.' }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-4 flex justify-end border-t border-slate-200 pt-3">

                                    <a href="{{ route('admin.repairs.show', $repairItem) }}"
                                        class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-700 hover:text-blue-800">

                                        Lihat detail reparasi

                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div
                                class="flex flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">

                                <svg class="size-9 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">

                                    <path
                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />
                                </svg>

                                <p class="mt-3 text-sm font-semibold text-slate-800">
                                    Belum ada riwayat reparasi
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Freezer ini belum pernah memiliki proses reparasi.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>

    @include('admin.freezers.partials.verify-freezer')

    @include('admin.repairs.partials.create-repairs')
@endsection
