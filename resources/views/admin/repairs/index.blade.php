@extends('admin.layout')

@section('content')
    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                        Manajemen Tugas Reparasi
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Buat tugas, tetapkan teknisi, dan pantau perkembangan reparasi.
                    </p>
                </div>

                <button type="button" data-modal-target="createRepairModal" data-modal-toggle="createRepairModal"
                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">

                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v14M5 12h14" />
                    </svg>

                    Buat tugas reparasi
                </button>
            </header>
        </div>

        <section aria-label="Ringkasan tugas reparasi"
            class="mx-auto mt-4 grid max-w-7xl grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

            {{-- Total tugas aktif --}}
            <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">
                        <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" />
                            <rect width="6" height="4" x="9" y="2" rx="1" stroke="currentColor"
                                stroke-width="2" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-4 text-slate-600">
                            Total tugas aktif
                        </p>

                        <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                            {{ $repairSummary['active'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] leading-4 text-slate-500">
                    Belum selesai dikerjakan
                </p>
            </article>

            {{-- Belum ditugaskan --}}
            <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded border border-amber-100 bg-amber-50 text-amber-600">
                        <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-4 text-slate-600">
                            Belum ditugaskan
                        </p>

                        <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                            {{ $repairSummary['unassigned'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] leading-4 text-slate-500">
                    Belum memiliki teknisi
                </p>
            </article>

            {{-- Menunggu diperiksa --}}
            <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded border border-emerald-100 bg-emerald-50 text-emerald-600">
                        <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 7v5l3 2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-4 text-slate-600">
                            Menunggu diperiksa
                        </p>

                        <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                            {{ $repairSummary['queued'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] leading-4 text-slate-500">
                    Belum mulai diperiksa
                </p>
            </article>

            {{-- Sedang diperiksa --}}
            <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded border border-violet-100 bg-violet-50 text-violet-600">
                        <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m20 20-4-4" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m8 11 2 2 4-4" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-4 text-slate-600">
                            Sedang diperiksa
                        </p>

                        <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                            {{ $repairSummary['inspecting'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] leading-4 text-slate-500">
                    Analisis awal sedang dilakukan
                </p>
            </article>

            {{-- Sedang diperbaiki --}}
            <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded border border-rose-100 bg-rose-50 text-rose-600">
                        <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium leading-4 text-slate-600">
                            Sedang diperbaiki
                        </p>

                        <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                            {{ $repairSummary['repairing'] }}
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] leading-4 text-slate-500">
                    Perbaikan sedang berlangsung
                </p>
            </article>
        </section>

        <section class="mx-auto mt-4 max-w-7xl overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
            <form action="{{ route('admin.repairs.index') }}" method="GET"
                class="grid gap-3 border-b-[1.6px] border-slate-100 p-4 md:grid-cols-2 xl:grid-cols-[minmax(280px,1fr)_170px_180px_190px_auto]">

                <input type="hidden" name="per_page" value="{{ $perPage }}">

                {{-- Pencarian --}}
                <div class="relative md:col-span-2 xl:col-span-1">
                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                        <svg class="size-4 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />

                            <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </div>

                    <input type="search" id="repair-search" name="search" value="{{ $filters['search'] }}"
                        autocomplete="off"
                        placeholder="Cari ID tugas, ID freezer, nomor seri, merek, model, atau pelanggan"
                        class="block w-full rounded border border-slate-300 bg-white p-2.5 ps-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Status reparasi --}}
                <div>
                    <label for="repair-status-filter" class="sr-only">
                        Status reparasi
                    </label>

                    <select id="repair-status-filter" name="status" onchange="this.form.submit()"
                        class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Semua status
                        </option>

                        @foreach (\App\Enums\RepairStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>

                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Teknisi --}}
                <div>
                    <label for="repair-technician-filter" class="sr-only">
                        Teknisi
                    </label>

                    <select id="repair-technician-filter" name="technician_id" onchange="this.form.submit()"
                        class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Semua teknisi
                        </option>

                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}" @selected($filters['technician_id'] === $technician->id)>

                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pelanggan --}}
                <div>
                    <label for="repair-customer-filter" class="sr-only">
                        Pelanggan
                    </label>

                    <select id="repair-customer-filter" name="customer_id" onchange="this.form.submit()"
                        class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Semua pelanggan
                        </option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected($filters['customer_id'] === $customer->id)>

                                {{ $customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hapus filter --}}
                <a href="{{ route('admin.repairs.index', ['per_page' => $perPage]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded px-3 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 xl:justify-start">

                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="M4 5h16l-6.5 7.5V18l-3 1.5v-7L4 5Z" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />

                        <path d="m17 17 4 4m0-4-4 4" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                    </svg>

                    Hapus filter
                </a>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-max w-full whitespace-nowrap text-left text-sm text-slate-500">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Tugas Reparasi</th>
                            <th scope="col" class="px-4 py-3">Unit &amp; Pelanggan</th>
                            <th scope="col" class="px-4 py-3">Teknisi</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Progres Terbaru</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($repairs as $repair)
                            @php
                                $repairCode = sprintf(
                                    'TR-%s-%04d',
                                    $repair->created_at?->format('Y') ?? now()->format('Y'),
                                    $repair->id,
                                );

                                $taskIconVariants = [
                                    'border-rose-200 bg-rose-50 text-rose-600',
                                    'border-amber-200 bg-amber-50 text-amber-600',
                                    'border-violet-200 bg-violet-50 text-violet-600',
                                    'border-blue-200 bg-blue-50 text-blue-600',
                                    'border-emerald-200 bg-emerald-50 text-emerald-600',
                                ];

                                $taskIconClass = $taskIconVariants[abs(crc32($repairCode)) % count($taskIconVariants)];

                                $isUnassigned = blank($repair->technician_id);

                                $statusBadgeClass = $isUnassigned
                                    ? 'border-rose-200 bg-rose-50 text-rose-700'
                                    : match ($repair->status) {
                                        \App\Enums\RepairStatus::QUEUED
                                            => 'border-amber-200 bg-amber-50 text-amber-700',

                                        \App\Enums\RepairStatus::INSPECTING
                                            => 'border-violet-200 bg-violet-50 text-violet-700',

                                        \App\Enums\RepairStatus::REPAIRING
                                            => 'border-blue-200 bg-blue-50 text-blue-700',

                                        \App\Enums\RepairStatus::COMPLETED
                                            => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                    };

                                $statusLabel = $isUnassigned ? 'Belum ditugaskan' : $repair->status->label();

                                $statusKey = $isUnassigned ? 'unassigned' : $repair->status->value;
                            @endphp

                            <tr class="border-b border-slate-200 bg-white hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded border {{ $taskIconClass }}">

                                            <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" />

                                                <rect x="9" y="2" width="6" height="4" rx="1"
                                                    stroke="currentColor" stroke-width="2" />

                                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h4" />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $repairCode }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Dibuat
                                                {{ $repair->created_at?->diffForHumans() ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-800">
                                        {{ $repair->freezer->freezer_code }}

                                        <span class="font-normal text-slate-400">
                                            ·
                                        </span>

                                        {{ $repair->freezer->brand }}
                                        {{ $repair->freezer->model }}
                                    </p>

                                    <p class="mt-1 max-w-56 whitespace-normal text-xs leading-4 text-slate-500">

                                        {{ $repair->freezer->customer->company_name }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($repair->technician)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">

                                                <svg class="size-4" aria-hidden="true" fill="none"
                                                    viewBox="0 0 24 24">

                                                    <circle cx="12" cy="8" r="4" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor"
                                                        stroke-linecap="round" stroke-width="2" />
                                                </svg>
                                            </div>

                                            <span class="font-medium text-slate-700">
                                                {{ $repair->technician->name }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <div
                                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-slate-100">

                                                <svg class="size-4" aria-hidden="true" fill="none"
                                                    viewBox="0 0 24 24">

                                                    <circle cx="10" cy="8" r="4" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                </svg>
                                            </div>

                                            Belum ditugaskan
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusBadgeClass }}">

                                        @if ($statusKey === 'unassigned')
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="10" cy="8" r="4" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6" stroke="currentColor"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        @elseif ($statusKey === \App\Enums\RepairStatus::QUEUED->value)
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        @elseif ($statusKey === \App\Enums\RepairStatus::INSPECTING->value)
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="11" cy="11" r="7" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="m20 20-4-4" stroke="currentColor" stroke-linecap="round"
                                                    stroke-width="2" />
                                            </svg>
                                        @elseif ($statusKey === \App\Enums\RepairStatus::REPAIRING->value)
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path
                                                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94Z"
                                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" />
                                            </svg>
                                        @elseif ($statusKey === \App\Enums\RepairStatus::COMPLETED->value)
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        @endif

                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="max-w-56 whitespace-normal leading-5 text-slate-700">

                                        {{ $repair->latestLog?->description ?? ($repair->initial_analysis ?? 'Belum ada pembaruan progres.') }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        @if ($repair->latestLog)
                                            {{ $repair->latestLog->created_at?->diffForHumans() }}
                                        @else
                                            Belum ada log progres
                                        @endif
                                    </p>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button id="repairActionsButton-{{ $repair->id }}"
                                        data-dropdown-toggle="repairActionsDropdown-{{ $repair->id }}"
                                        data-dropdown-placement="bottom-end" type="button"
                                        aria-label="Buka aksi tugas {{ $repairCode }}"
                                        class="inline-flex size-9 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">

                                        <svg class="size-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="1.8" />
                                            <circle cx="12" cy="12" r="1.8" />
                                            <circle cx="12" cy="19" r="1.8" />
                                        </svg>
                                    </button>

                                    <div id="repairActionsDropdown-{{ $repair->id }}"
                                        class="z-30 hidden w-52 rounded border border-slate-200 bg-white p-2 text-left shadow-lg">

                                        <ul class="space-y-1 text-sm text-slate-700"
                                            aria-labelledby="repairActionsButton-{{ $repair->id }}">

                                            <li>
                                                <a href="{{ route('admin.repairs.show', $repair) }}"
                                                    class="flex w-full items-center gap-2 rounded px-3 py-2 text-left hover:bg-slate-100">

                                                    <svg class="size-4 text-slate-500" aria-hidden="true" fill="none"
                                                        viewBox="0 0 24 24">

                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z" />

                                                        <circle cx="12" cy="12" r="3" stroke="currentColor"
                                                            stroke-width="2" />
                                                    </svg>

                                                    Lihat detail
                                                </a>
                                            </li>

                                            @if (!$repair->technician)
                                                <li>
                                                    <button type="button" data-modal-target="assignTechnicianModal"
                                                        data-modal-toggle="assignTechnicianModal" data-assignment-trigger
                                                        data-repair-id="{{ $repair->id }}"
                                                        data-repair-code="{{ $repairCode }}"
                                                        data-freezer-code="{{ $repair->freezer->freezer_code }}"
                                                        data-freezer-name="{{ trim(($repair->freezer->brand ?? '') . ' ' . ($repair->freezer->model ?? '')) }}"
                                                        data-current-technician-id="" data-current-technician-name=""
                                                        data-update-url="{{ route('admin.repairs.technician.update', $repair) }}"
                                                        onclick="document.getElementById('repairActionsButton-{{ $repair->id }}')?.click()"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-blue-700 hover:bg-blue-50">

                                                        <svg class="size-4" aria-hidden="true" fill="none"
                                                            viewBox="0 0 24 24">

                                                            <circle cx="10" cy="8" r="4"
                                                                stroke="currentColor" stroke-width="2" />

                                                            <path d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6"
                                                                stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" />
                                                        </svg>

                                                        Tetapkan teknisi
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="font-medium text-slate-700">
                                        Belum ada tugas reparasi
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Tugas reparasi yang dibuat akan muncul di sini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-table-pagination :paginator="$repairs" :action="route('admin.repairs.index')" :per-page="$perPage" :query="[
                'search' => $filters['search'],
                'status' => $filters['status'],
                'technician_id' => $filters['technician_id'],
                'customer_id' => $filters['customer_id'],
            ]"
                item-label="tugas reparasi" aria-label="Pagination tugas reparasi" />
        </section>

        @include('admin.repairs.partials.create-repairs')
        @include('admin.repairs.partials.assign-technician')
    </main>
@endsection
