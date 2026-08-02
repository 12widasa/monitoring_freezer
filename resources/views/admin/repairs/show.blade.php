@extends('admin.layout')

@section('content')
    @php
        $repairCode = sprintf('TR-%s-%04d', $repair->created_at->format('Y'), $repair->id);

        $isUnassigned = $repair->technician_id === null;

        $statusLabel = $isUnassigned ? 'Belum ditugaskan' : $repair->status->label();

        $statusBadgeClass = match (true) {
            $isUnassigned => 'border-rose-200 bg-rose-50 text-rose-700',

            $repair->status === \App\Enums\RepairStatus::QUEUED => 'border-amber-200 bg-amber-50 text-amber-700',

            $repair->status === \App\Enums\RepairStatus::INSPECTING => 'border-violet-200 bg-violet-50 text-violet-700',

            $repair->status === \App\Enums\RepairStatus::REPAIRING => 'border-blue-200 bg-blue-50 text-blue-700',

            $repair->status === \App\Enums\RepairStatus::COMPLETED
                => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        };

        $latestProgress =
            $repair->latestLog?->description ?? ($repair->initial_analysis ?? 'Belum ada perkembangan pekerjaan.');

        $latestProgressAt = $repair->latestLog?->created_at ?? $repair->updated_at;

        $technicianInitials = $repair->technician
            ? collect(preg_split('/\s+/', trim($repair->technician->name)))
                ->filter()
                ->take(2)
                ->map(fn(string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                ->implode('')
            : null;

        $repairPhotos = $repair->logs
            ->flatMap(
                fn($log) => $log->photos->map(
                    fn($photo) => [
                        'photo' => $photo,
                        'log' => $log,
                    ],
                ),
            )
            ->values();

        $freezerPhotoUrl = $repair->freezer->photo_path
            ? \Illuminate\Support\Facades\Storage::url($repair->freezer->photo_path)
            : null;
    @endphp

    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">

            {{-- Navigasi dan aksi --}}
            <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <a href="{{ route('admin.repairs.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-blue-700">
                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m15 18-6-6 6-6" />
                        </svg>

                        Kembali ke Tugas Reparasi
                    </a>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900">
                        Detail Tugas Reparasi
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi unit, penugasan, dan perkembangan pekerjaan.
                    </p>
                </div>
            </header>

            {{-- Ringkasan tugas --}}
            <section aria-label="Ringkasan tugas reparasi"
                class="mt-4 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(320px,0.72fr)]">
                    <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:p-5">
                        <div
                            class="flex size-16 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">
                            <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" />

                                <rect x="9" y="2" width="6" height="4" rx="1" stroke="currentColor"
                                    stroke-width="2" />

                                <path d="M9 12h6m-6 4h3" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-xl font-bold text-slate-950">
                                    {{ $repairCode }}
                                </p>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusBadgeClass }}">
                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94Z" />
                                    </svg>

                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="mt-1 space-y-0.5">
                                <p class="text-sm text-slate-500">
                                    Freezer {{ $repair->freezer->freezer_code }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    {{ $repair->freezer->brand }}
                                    {{ $repair->freezer->model }}
                                </p>
                            </div>

                            <p class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Diperbarui
                                {{ local_datetime($latestProgressAt) }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600">
                                    <svg class="size-4" aria-hidden="true" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">
                                        Perkembangan Terbaru
                                    </p>
                                    <p class="text-sm text-slate-700">
                                        {{ $latestProgress }}
                                    </p>
                                    <p class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                                        <svg class="size-3.5" aria-hidden="true" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ local_datetime($latestProgressAt) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Informasi utama --}}
            <div class="mt-4 grid items-stretch gap-4 lg:grid-cols-2">

                {{-- Informasi freezer --}}
                <section class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi Freezer
                        </h2>
                    </div>

                    <div class="flex-1 px-4 py-4">
                        <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-[140px_minmax(0,1fr)]">
                            <dt class="text-slate-500">
                                Kode Freezer
                            </dt>

                            <dd class="font-medium text-slate-800">
                                {{ $repair->freezer->freezer_code }}
                            </dd>

                            <dt class="text-slate-500">
                                Nomor Seri
                            </dt>

                            <dd class="font-medium text-slate-800">
                                {{ $repair->freezer->serial_number ?: 'Tidak tersedia' }}
                            </dd>

                            <dt class="text-slate-500">
                                Pemilik
                            </dt>

                            <dd class="font-medium text-slate-800">
                                {{ $repair->freezer->customer?->company_name ?: 'Tidak tersedia' }}
                            </dd>

                            <dt class="text-slate-500">
                                Kapasitas
                            </dt>

                            <dd class="font-medium text-slate-800">
                                @if ($repair->freezer->capacity_liter !== null)
                                    {{ number_format($repair->freezer->capacity_liter, 0, ',', '.') }}
                                    liter
                                @else
                                    Tidak tersedia
                                @endif
                            </dd>

                            <dt class="text-slate-500">
                                Status Verifikasi
                            </dt>

                            <dd>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />

                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />
                                    </svg>

                                    {{ $repair->serviceIntake?->status_verifikasi->label() ?? 'Tidak tersedia' }}
                                </span>
                            </dd>
                        </dl>
                    </div>

                    <div class="mt-auto border-t-[1.6px] border-slate-100 px-4 py-3">
                        <a href="{{ route('admin.freezers.show', $repair->freezer) }}"
                            class="inline-flex items-center justify-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">

                            Buka Data Freezer

                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path d="M15 4h5v5M20 4l-9 9M19 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h6"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" />
                            </svg>
                        </a>
                    </div>
                </section>

                {{-- Penugasan --}}
                <section class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Penugasan
                        </h2>
                    </div>

                    <div class="flex-1 px-4 py-4">
                        <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-[140px_minmax(0,1fr)]">
                            <dt class="text-slate-500">
                                Teknisi
                            </dt>

                            <dd>
                                @if ($repair->technician)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">

                                            {{ $technicianInitials }}
                                        </div>

                                        <span class="font-medium text-slate-800">
                                            {{ $repair->technician->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="font-medium text-rose-700">
                                        Belum ditugaskan
                                    </span>
                                @endif
                            </dd>

                            <dt class="text-slate-500">
                                Admin Pembuat
                            </dt>

                            <dd class="font-medium text-slate-800">
                                {{ $repair->admin?->name ?: 'Tidak tersedia' }}
                            </dd>

                            <dt class="text-slate-500">
                                Tugas Dibuat
                            </dt>

                            <dd class="font-medium text-slate-800">
                                {{ local_datetime($repair->created_at) }}
                            </dd>

                            <dt class="text-slate-500">
                                Status Tugas
                            </dt>

                            <dd>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusBadgeClass }}">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path
                                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    {{ $statusLabel }}
                                </span>
                            </dd>
                        </dl>
                    </div>

                    <div class="mt-auto border-t-[1.6px] border-slate-100 px-4 py-3">
                        <button type="button" data-modal-target="assignTechnicianModal"
                            data-modal-toggle="assignTechnicianModal" data-assignment-trigger
                            data-repair-id="{{ $repair->id }}" data-repair-code="{{ $repairCode }}"
                            data-freezer-code="{{ $repair->freezer->freezer_code }}"
                            data-freezer-name="{{ trim(($repair->freezer->brand ?? '') . ' ' . ($repair->freezer->model ?? '')) }}"
                            data-current-technician-id="{{ $repair->technician_id }}"
                            data-current-technician-name="{{ $repair->technician?->name }}"
                            data-update-url="{{ route('admin.repairs.technician.update', $repair) }}"
                            class="inline-flex items-center justify-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">

                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="2" />

                                <path d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>

                            {{ $repair->technician_id === null ? 'Tetapkan Teknisi' : 'Ganti Teknisi' }}
                        </button>
                    </div>
                </section>
            </div>

            {{-- Riwayat dan dokumentasi --}}
            <div class="mt-4 grid items-stretch gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">
                {{-- Riwayat --}}
                <section class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Riwayat Perkembangan
                        </h2>
                    </div>

                    <div class="flex-1 p-4">
                        <div class="rounded border border-blue-100 bg-blue-50/50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">
                                Keluhan Awal
                            </p>

                            <p class="mt-1.5 text-sm leading-5 text-slate-700">
                                {{ $repair->serviceIntake?->complaint_note ?: 'Keluhan awal tidak tersedia.' }}
                            </p>
                        </div>

                        @if ($repair->initial_analysis)
                            <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    Analisis Awal
                                </p>

                                <p class="mt-1.5 text-sm leading-5 text-slate-700">
                                    {{ $repair->initial_analysis }}
                                </p>
                            </div>
                        @endif

                        <div class="mt-4 border-t-[1.6px] border-slate-100">
                            @forelse ($repair->logs as $log)
                                @if ($loop->first)
                                    <ol
                                        class="relative ms-2 before:absolute before:inset-y-3 before:start-0 before:w-px before:bg-slate-200">
                                @endif

                                <li
                                    class="relative ms-5 py-3 {{ !$loop->last ? 'border-b-[1.6px] border-slate-100' : '' }}">

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

                                    <div class="grid gap-1 text-sm sm:grid-cols-[140px_minmax(0,1fr)] sm:gap-x-4">
                                        <time datetime="{{ $log->created_at->toIso8601String() }}"
                                            class="text-slate-500">

                                            {{ local_datetime($log->created_at) }}
                                        </time>

                                        <div>
                                            <p
                                                class="{{ $loop->first ? 'font-semibold text-slate-800' : 'font-medium text-slate-700' }}">
                                                {{ $log->description }}
                                            </p>

                                            <div
                                                class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                                                <span>
                                                    {{ $log->status->label() }}
                                                </span>

                                                @if ($log->updater)
                                                    <span aria-hidden="true">•</span>

                                                    <span>
                                                        Oleh {{ $log->updater->name }}
                                                    </span>
                                                @endif

                                                @if ($log->photos->isNotEmpty())
                                                    <span aria-hidden="true">•</span>

                                                    <span>
                                                        {{ $log->photos->count() }}
                                                        foto
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if ($loop->last)
                                    </ol>
                                @endif
                            @empty
                                <div class="py-8 text-center">
                                    <p class="font-medium text-slate-700">
                                        Belum ada riwayat perkembangan
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Perkembangan pekerjaan dari teknisi akan muncul di sini.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                {{-- Dokumentasi --}}
                <section class="flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-semibold text-slate-900">
                                Dokumentasi
                            </h2>

                            <span class="text-xs text-slate-500">
                                {{ ($freezerPhotoUrl ? 1 : 0) + $repairPhotos->count() }}
                                foto
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 p-4">
                        @if ($freezerPhotoUrl || $repairPhotos->isNotEmpty())
                            <div class="grid grid-cols-2 content-start gap-3">
                                @if ($freezerPhotoUrl)
                                    <a href="{{ $freezerPhotoUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="group overflow-hidden rounded border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                        <div class="aspect-[4/3] overflow-hidden">
                                            <img src="{{ $freezerPhotoUrl }}"
                                                alt="Foto freezer {{ $repair->freezer->freezer_code }}"
                                                class="size-full object-cover transition group-hover:scale-105">
                                        </div>

                                        <div class="border-t border-slate-200 bg-white px-2.5 py-2">
                                            <p class="truncate text-xs font-medium text-slate-700">
                                                Foto Freezer
                                            </p>
                                        </div>
                                    </a>
                                @endif

                                @foreach ($repairPhotos as $documentation)
                                    @php
                                        $photo = $documentation['photo'];
                                        $log = $documentation['log'];

                                        $photoUrl = \Illuminate\Support\Facades\Storage::url($photo->photo_path);
                                    @endphp

                                    <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="group overflow-hidden rounded border border-slate-200 bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                        <div class="aspect-[4/3] overflow-hidden">
                                            <img src="{{ $photoUrl }}" alt="Dokumentasi progres {{ $repairCode }}"
                                                loading="lazy"
                                                class="size-full object-cover transition group-hover:scale-105">
                                        </div>

                                        <div class="border-t border-slate-200 bg-white px-2.5 py-2">
                                            <p class="truncate text-xs font-medium text-slate-700">
                                                {{ $log->description }}
                                            </p>

                                            <p class="mt-0.5 text-[11px] text-slate-500">
                                                {{ local_datetime($photo->created_at) }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="flex min-h-48 flex-col items-center justify-center text-center">
                                <svg class="size-8 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">

                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor"
                                        stroke-width="2" />

                                    <circle cx="8.5" cy="10" r="1.5" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="m21 15-4.5-4.5L8 19" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                <p class="mt-3 font-medium text-slate-700">
                                    Belum ada dokumentasi
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Foto freezer atau progres reparasi akan muncul di sini.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- Komponen reparasi --}}
            <section class="mt-4 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Komponen Reparasi
                        </h2>

                        <span class="text-xs text-slate-500">
                            {{ $repair->components->count() }}
                            komponen
                        </span>
                    </div>
                </div>

                @if ($repair->components->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-max w-full whitespace-nowrap text-left text-sm text-slate-500">

                            <thead class="bg-slate-50 text-xs font-semibold text-slate-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3">
                                        Komponen
                                    </th>

                                    <th scope="col" class="px-4 py-3">
                                        Jumlah
                                    </th>

                                    <th scope="col" class="px-4 py-3">
                                        Status
                                    </th>

                                    <th scope="col" class="px-4 py-3">
                                        Ditambahkan Oleh
                                    </th>

                                    <th scope="col" class="px-4 py-3">
                                        Pemasangan
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($repair->components as $repairComponent)
                                    @php
                                        $componentStatusLabel = match ($repairComponent->status) {
                                            'requested' => 'Diminta',
                                            default => \Illuminate\Support\Str::headline($repairComponent->status),
                                        };

                                        $componentStatusClass = match ($repairComponent->status) {
                                            'requested' => 'border-amber-200 bg-amber-50 text-amber-700',

                                            default => 'border-slate-200 bg-slate-50 text-slate-700',
                                        };
                                    @endphp

                                    <tr class="align-top hover:bg-slate-50/70">
                                        <td class="px-4 py-3">
                                            <div class="max-w-sm whitespace-normal">
                                                <p class="font-semibold text-slate-900">
                                                    {{ $repairComponent->component?->name ?: 'Komponen tidak tersedia' }}
                                                </p>

                                                @if ($repairComponent->component?->part_number)
                                                    <p class="mt-0.5 text-xs text-slate-500">
                                                        Part number:
                                                        {{ $repairComponent->component->part_number }}
                                                    </p>
                                                @endif

                                                @if ($repairComponent->note)
                                                    <p class="mt-1.5 text-xs leading-5 text-slate-600">
                                                        {{ $repairComponent->note }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="font-medium text-slate-800">
                                                {{ number_format($repairComponent->quantity, 0, ',', '.') }}
                                            </span>

                                            <span class="text-slate-500">
                                                {{ $repairComponent->component?->unit ?: 'unit' }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $componentStatusClass }}">

                                                {{ $componentStatusLabel }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3">
                                            <p class="font-medium text-slate-800">
                                                {{ $repairComponent->addedBy?->name ?: 'Tidak tersedia' }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ local_datetime($repairComponent->created_at, fallback: 'Waktu tidak tersedia') }}
                                            </p>
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($repairComponent->installed_at)
                                                <p class="font-medium text-slate-800">
                                                    {{ local_datetime($repairComponent->installed_at) }}
                                                </p>

                                                <p class="mt-0.5 text-xs text-slate-500">
                                                    Oleh
                                                    {{ $repairComponent->installedBy?->name ?: 'Tidak tersedia' }}
                                                </p>
                                            @else
                                                <span class="text-slate-500">
                                                    Belum dipasang
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex min-h-44 flex-col items-center justify-center px-4 py-8 text-center">
                        <svg class="size-9 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                            <path d="M9.5 3.5 4 9l11 11 5.5-5.5-11-11Z" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />

                            <path d="m7 7 2 2m2-2 2 2" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                        </svg>

                        <p class="mt-3 font-medium text-slate-700">
                            Belum ada komponen reparasi
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Komponen yang diperlukan atau dipasang akan muncul di sini.
                        </p>
                    </div>
                @endif
            </section>
        </div>

        @include('admin.repairs.partials.assign-technician')
    </main>
@endsection
