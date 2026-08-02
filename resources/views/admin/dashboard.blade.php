@extends('admin.layout')

@section('title', 'Beranda Admin - Karyatama')

@section('content')
    <div class="space-y-4">
        <header
            class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:p-5">
            <div>
                <h1 class="text-lg font-bold leading-tight text-slate-900 sm:text-xl">
                    Beranda Admin
                </h1>

                <p class="mt-0.5 text-xs font-normal text-slate-500 sm:text-sm">
                    Pantau aktivitas operasional dan kelola data utama Karyatama.
                </p>
            </div>

            <div
                class="flex items-center justify-between gap-3 border-t border-slate-100 pt-2 sm:justify-end sm:border-0 sm:pt-0">

                <span class="text-xs font-medium text-slate-600 sm:text-sm">
                    {{ local_datetime(now(), 'l, j F Y', translated: true) }}
                </span>

                <div class="hidden h-4 w-px bg-slate-200 sm:block"></div>

                <div class="inline-flex items-center gap-2" aria-label="Waktu Indonesia Barat">

                    <div
                        class="flex size-9 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700">

                        <svg class="size-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                    </div>

                    <time id="admin-current-time"
                        class="min-w-[4.75rem] text-xs font-semibold tabular-nums text-slate-700 sm:text-sm"
                        aria-label="Jam saat ini dalam Waktu Indonesia Barat">

                        --.-- WIB
                    </time>
                </div>
            </div>
        </header>

        <!-- Section 1: Welcome & Metric Stat Cards -->
        <div class="p-5 sm:p-6 bg-white border border-slate-200/60 rounded-2xl shadow-xs space-y-5">

            <!-- Welcome Header & Badge Tugas Aktif -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1">
                <div>
                    <h2 class="text-base font-bold text-slate-900 leading-tight">
                        Selamat datang, {{ Auth::user()->name }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">
                        Berikut ringkasan aktivitas reparasi freezer hari ini.
                    </p>
                </div>

                <!-- Badge Tugas Aktif -->
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50/60 border border-blue-200/80 rounded-lg text-blue-600 font-semibold text-xs self-start sm:self-auto">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path
                            d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2" />
                    </svg>
                    <span>
                        {{ number_format($summary['active_repairs']) }}
                        tugas aktif
                    </span>
                </div>
            </div>

            <!-- Garis Pemisah Horizontal Soft -->
            <div class="border-t border-slate-100"></div>

            <!-- Grid Stat Cards (2 Kolom di Laptop 1024, 4 Kolom di XL) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- Stat 1: Total Freezer -->
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="size-12 rounded-xl bg-blue-50/80 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <line x1="2" x2="22" y1="12" y2="12" />
                                <line x1="12" x2="12" y1="2" y2="22" />
                                <path d="m20 16-4-4 4-4" />
                                <path d="m4 8 4 4-4 4" />
                                <path d="m16 4-4 4-4-4" />
                                <path d="m8 20 4-4 4 4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Total freezer</p>
                            <h3 class="text-2xl font-bold text-slate-900 leading-tight mt-0.5">
                                {{ number_format($summary['total_freezers']) }}
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate">Unit yang telah terdaftar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-emerald-600 pt-0.5">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="m5 12 7-7 7 7" />
                            <path d="M12 19V5" />
                        </svg>
                        <span>
                            +{{ number_format($summary['freezers_created_this_month']) }}
                            bulan ini
                        </span>
                    </div>
                </div>

                <!-- Stat 2: Menunggu Diperiksa -->
                <div class="space-y-2.5 pt-4 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="size-12 rounded-xl bg-amber-50/80 border border-amber-200/60 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Menunggu diperiksa</p>
                            <h3 class="text-2xl font-bold text-slate-900 leading-tight mt-0.5">
                                {{ number_format($summary['queued_repairs']) }}
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate">Tugas belum mulai diperiksa</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600 pt-0.5">
                        <span class="size-2 rounded-full bg-amber-400 shrink-0"></span>
                        <span>
                            {{ number_format($summary['queued_created_today']) }}
                            tugas baru hari ini
                        </span>
                    </div>
                </div>

                <!-- Stat 3: Sedang Diperbaiki -->
                <div class="space-y-2.5 pt-4 xl:pt-0 border-t xl:border-t-0 border-slate-100">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="size-12 rounded-xl bg-indigo-50/80 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path
                                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Sedang diperbaiki</p>
                            <h3 class="text-2xl font-bold text-slate-900 leading-tight mt-0.5">
                                {{ number_format($summary['repairing_repairs']) }}
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate">Perbaikan masih berlangsung</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600 pt-0.5">
                        <span class="size-2 rounded-full bg-blue-500 shrink-0"></span>
                        <span>
                            {{ number_format($summary['repairing_updated_today']) }}
                            diperbarui hari ini
                        </span>
                    </div>
                </div>

                <!-- Stat 4: Perbaikan Selesai -->
                <div class="space-y-2.5 pt-4 xl:pt-0 border-t xl:border-t-0 border-slate-100">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="size-12 rounded-xl bg-emerald-50/80 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Perbaikan selesai</p>
                            <h3 class="text-2xl font-bold text-slate-900 leading-tight mt-0.5">
                                {{ number_format($summary['completed_this_month']) }}
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate">Selesai bulan ini</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-600 pt-0.5">
                        <span class="size-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span>
                            {{ number_format($summary['completed_this_week']) }}
                            selesai minggu ini
                        </span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Section 2: Aksi Cepat (1 Kolom di 1024, 3 Kolom di XL) -->
        <div class="space-y-2.5">
            <div>
                <h2 class="text-base font-bold text-slate-900 leading-tight">Aksi cepat</h2>
                <p class="text-xs text-slate-500 mt-0.5">Buka fitur yang paling sering digunakan.</p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-3.5">

                <!-- Action 1: Tambah Freezer -->
                <div
                    class="p-4 bg-white border border-slate-200/60 rounded-2xl shadow-xs flex items-center justify-between gap-3.5">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div
                            class="size-12 sm:size-14 rounded-xl bg-blue-50/80 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="size-6 sm:size-7" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="9" cy="9" r="7" />
                                <line x1="9" x2="9" y1="6" y2="12" />
                                <line x1="6" x2="12" y1="9" y2="9" />
                                <path d="m19 19-3-3" />
                                <path d="M12 21a9 9 0 0 0 9-9" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 leading-snug truncate">Tambah freezer</h3>
                            <p class="text-xs text-slate-500 font-normal leading-relaxed mt-0.5 truncate">
                                Daftarkan unit freezer baru ke dalam sistem.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.freezers.index') }}"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg transition-colors shrink-0 whitespace-nowrap shadow-xs">
                        Tambah freezer
                    </a>
                </div>

                <!-- Action 2: Buat Tugas Reparasi -->
                <div
                    class="p-4 bg-white border border-slate-200/60 rounded-2xl shadow-xs flex items-center justify-between gap-3.5">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div
                            class="size-12 sm:size-14 rounded-xl bg-amber-50/80 border border-amber-200/60 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="size-6 sm:size-7" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                                <path d="M12 11v6" />
                                <path d="M9 14h6" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 leading-snug truncate">Buat tugas reparasi</h3>
                            <p class="text-xs text-slate-500 font-normal leading-relaxed mt-0.5 truncate">
                                Buat tugas dan tetapkan teknisi.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.repairs.index') }}"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg transition-colors shrink-0 whitespace-nowrap shadow-xs">
                        Buat tugas
                    </a>
                </div>

                <!-- Action 3: Tambah Pengguna -->
                <div
                    class="p-4 bg-white border border-slate-200/60 rounded-2xl shadow-xs flex items-center justify-between gap-3.5">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div
                            class="size-12 sm:size-14 rounded-xl bg-blue-50/80 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="size-6 sm:size-7" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" x2="19" y1="8" y2="14" />
                                <line x1="16" x2="22" y1="11" y2="11" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 leading-snug truncate">Tambah pengguna</h3>
                            <p class="text-xs text-slate-500 font-normal leading-relaxed mt-0.5 truncate">
                                Tambahkan akun teknisi atau pelanggan.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg transition-colors shrink-0 whitespace-nowrap shadow-xs">
                        Tambah pengguna
                    </a>
                </div>

            </div>
        </div>

        <!-- Section 3: Layout Grid 2 Kolom (Perlu Perhatian & Aktivitas Terbaru) -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

            <!-- Section Kiri: Perlu Perhatian -->
            <div class="xl:col-span-7 bg-white border border-slate-200/60 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">

                <!-- Header Section -->
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 leading-tight">

                            Perlu perhatian
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            Tugas yang belum bergerak atau memerlukan tindakan admin.
                        </p>
                    </div>

                    <a href="{{ route('admin.repairs.index') }}"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">

                        <span>Lihat semua tugas</span>

                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if ($attentionRepairs->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach ($attentionRepairs as $attention)
                            @php
                                $attentionRepair = $attention['repair'];
                                $attentionFreezer = $attentionRepair->freezer;
                                $attentionType = $attention['type'];
                                $attentionReferenceAt = $attention['reference_at'];

                                $attentionDisplay = match ($attentionType) {
                                    'unassigned' => [
                                        'label' => 'Belum ditugaskan',
                                        'message' => 'Belum memiliki teknisi.',
                                        'badge_class' => 'bg-red-50 text-red-600 border-red-100',
                                        'icon' => 'alert',
                                    ],
                                    'waiting_inspection' => [
                                        'label' => 'Menunggu diperiksa',
                                        'message' => 'Belum diperiksa sejak ditugaskan.',
                                        'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                                        'icon' => 'clock',
                                    ],
                                    'stale_repair' => [
                                        'label' => 'Sedang diperbaiki',
                                        'message' => sprintf(
                                            'Tidak ada pembaruan selama %s.',
                                            $attentionReferenceAt->locale('id')->diffForHumans(null, true),
                                        ),
                                        'badge_class' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'icon' => 'repair',
                                    ],
                                };

                                $attentionTimeLabel = match ($attentionType) {
                                    'unassigned' => sprintf(
                                        'Dibuat %s',
                                        $attentionReferenceAt->locale('id')->diffForHumans(),
                                    ),
                                    'waiting_inspection' => sprintf(
                                        'Ditugaskan %s',
                                        $attentionReferenceAt->locale('id')->diffForHumans(),
                                    ),
                                    'stale_repair' => sprintf(
                                        'Terakhir diperbarui %s',
                                        $attentionReferenceAt->locale('id')->diffForHumans(),
                                    ),
                                };
                            @endphp

                            <article class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                                <div class="flex flex-wrap items-center gap-3 flex-1 min-w-0">

                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 border rounded-lg text-[11px] font-semibold whitespace-nowrap shrink-0 {{ $attentionDisplay['badge_class'] }}">

                                        @if ($attentionDisplay['icon'] === 'alert')
                                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                viewBox="0 0 24 24">

                                                <circle cx="12" cy="12" r="10" />

                                                <line x1="12" x2="12" y1="8" y2="12" />

                                                <line x1="12" x2="12.01" y1="16" y2="16" />
                                            </svg>
                                        @elseif ($attentionDisplay['icon'] === 'clock')
                                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                viewBox="0 0 24 24">

                                                <circle cx="12" cy="12" r="10" />

                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                        @else
                                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                viewBox="0 0 24 24">

                                                <path
                                                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                            </svg>
                                        @endif

                                        {{ $attentionDisplay['label'] }}
                                    </span>

                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 min-w-0">

                                        <span class="text-xs font-bold text-slate-800 shrink-0">

                                            {{ $attentionFreezer?->freezer_code ?: 'Freezer tidak tersedia' }}
                                        </span>

                                        <span class="text-xs font-medium text-slate-700 break-words">

                                            {{ collect([$attentionFreezer?->brand, $attentionFreezer?->model])->filter()->implode(' ') ?:
                                                'Identitas tidak tersedia' }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-50">

                                    <div class="text-left sm:text-right">
                                        <p class="text-xs text-slate-600 font-medium">

                                            {{ $attentionDisplay['message'] }}
                                        </p>

                                        <p class="text-[11px] text-slate-400">
                                            {{ $attentionTimeLabel }}
                                        </p>
                                    </div>

                                    <a href="{{ route('admin.repairs.show', $attentionRepair) }}"
                                        class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-lg transition-colors">

                                        Buka
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 px-5 py-8 text-center">

                        <div
                            class="mx-auto flex size-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400">

                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="10" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>

                        <h3 class="mt-3 text-sm font-semibold text-slate-800">
                            Tidak ada tugas yang memerlukan perhatian
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Semua tugas aktif masih berada dalam batas waktu operasional.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Section Kanan: Aktivitas Terbaru -->
            <div class="xl:col-span-5 bg-white border border-slate-200/60 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">

                <!-- Header Section -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 leading-tight">
                        Aktivitas terbaru
                    </h2>

                    <p class="text-xs text-slate-500 mt-0.5">
                        Pembaruan reparasi terbaru.
                    </p>
                </div>

                @if ($latestActivities->isNotEmpty())
                    <!-- Timeline Vertical -->
                    <div
                        class="relative space-y-5 pt-1 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">

                        @foreach ($latestActivities as $activity)
                            @php
                                [$activityIconClass, $activityIcon] = match ($activity->status) {
                                    \App\Enums\RepairStatus::QUEUED => [
                                        'bg-slate-50 border-slate-200 text-slate-600',
                                        'clock',
                                    ],
                                    \App\Enums\RepairStatus::INSPECTING => [
                                        'bg-blue-50/80 border-blue-100 text-blue-600',
                                        'search',
                                    ],
                                    \App\Enums\RepairStatus::REPAIRING => [
                                        'bg-amber-50/80 border-amber-200/60 text-amber-600',
                                        'repair',
                                    ],
                                    \App\Enums\RepairStatus::COMPLETED => [
                                        'bg-emerald-50/80 border-emerald-100 text-emerald-600',
                                        'completed',
                                    ],
                                };

                                $activityRepair = $activity->repair;
                                $activityFreezer = $activityRepair?->freezer;
                            @endphp

                            <article class="relative flex items-start gap-3.5">
                                <div
                                    class="size-10 rounded-xl border flex items-center justify-center shrink-0 z-10 {{ $activityIconClass }}">

                                    @if ($activityIcon === 'completed')
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                            <circle cx="12" cy="12" r="10" />
                                            <path d="m9 12 2 2 4-4" />
                                        </svg>
                                    @elseif ($activityIcon === 'repair')
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                            <path
                                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                        </svg>
                                    @elseif ($activityIcon === 'search')
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                            <circle cx="11" cy="11" r="7" />
                                            <path d="m20 20-3.5-3.5" />
                                        </svg>
                                    @else
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 flex items-start justify-between gap-3 pt-0.5 min-w-0">

                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-slate-900 leading-snug">

                                            {{ $activity->status->label() }}
                                        </h3>

                                        <p class="text-xs text-slate-500 font-normal leading-normal mt-0.5 break-words">

                                            {{ $activity->description }}
                                        </p>

                                        <p class="mt-1 text-[11px] text-slate-400">

                                            @if ($activityFreezer)
                                                <span class="font-medium text-slate-600">
                                                    {{ $activityFreezer->freezer_code }}
                                                </span>
                                            @else
                                                Unit freezer tidak tersedia
                                            @endif

                                            @if ($activity->updater)
                                                <span aria-hidden="true">·</span>
                                                {{ $activity->updater->name }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 flex-col items-end gap-1">

                                        <span class="text-[11px] text-slate-400 font-medium whitespace-nowrap">

                                            {{ $activity->created_at->locale('id')->diffForHumans() }}
                                        </span>

                                        @if ($activityRepair)
                                            <a href="{{ route('admin.repairs.show', $activityRepair) }}"
                                                class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">

                                                Buka
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 px-5 py-8 text-center">

                        <div
                            class="mx-auto flex size-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400">

                            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>

                        <h3 class="mt-3 text-sm font-semibold text-slate-800">
                            Belum ada aktivitas
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Pembaruan reparasi akan muncul di bagian ini.
                        </p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Section 4: Freezer Terbaru (2 Kolom di Laptop 1024, 4 Kolom di XL 1280+) -->
        <div class="p-5 sm:p-6 bg-white border border-slate-200/60 rounded-2xl shadow-xs space-y-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 leading-tight">
                        Freezer terbaru
                    </h2>

                    <p class="text-xs text-slate-500 mt-0.5">
                        Unit yang baru ditambahkan ke sistem.
                    </p>
                </div>

                <a href="{{ route('admin.freezers.index') }}"
                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">

                    <span>Lihat semua freezer</span>

                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                        <path d="M5 12h14" />
                        <path d="m12 5 7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if ($latestFreezers->isNotEmpty())
                <!-- Grid Cards Freezer -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
                    @foreach ($latestFreezers as $freezer)
                        <article
                            class="p-4 bg-white border border-slate-200/60 rounded-2xl shadow-xs space-y-3 flex flex-col justify-between">

                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div
                                        class="size-12 rounded-xl bg-blue-50/80 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">

                                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                                            <line x1="2" x2="22" y1="12" y2="12" />

                                            <line x1="12" x2="12" y1="2" y2="22" />

                                            <path d="m20 16-4-4 4-4" />
                                            <path d="m4 8 4 4-4 4" />
                                            <path d="m16 4-4 4-4-4" />
                                            <path d="m8 20 4-4 4 4" />
                                        </svg>
                                    </div>

                                    <div class="space-y-0.5 min-w-0">
                                        <h3 class="text-sm font-bold text-slate-900 leading-snug">

                                            {{ $freezer->freezer_code }}
                                        </h3>

                                        <p class="text-xs text-slate-600 font-medium break-words">

                                            {{ collect([$freezer->brand, $freezer->model])->filter()->implode(' ') ?:
                                                'Identitas tidak tersedia' }}
                                        </p>

                                        <p class="text-[11px] text-slate-400 font-mono break-all">

                                            No. seri:
                                            {{ $freezer->serial_number ?: '—' }}
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('admin.freezers.show', $freezer) }}"
                                    class="px-2.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-lg transition-colors shrink-0">

                                    Lihat
                                </a>
                            </div>

                            <div class="space-y-0.5 pt-2 border-t border-slate-100">
                                <p class="text-xs text-slate-600 font-medium break-words">

                                    {{ $freezer->customer?->company_name ?: 'Pelanggan tidak tersedia' }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    Ditambahkan
                                    {{ $freezer->created_at->locale('id')->diffForHumans() }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 px-5 py-8 text-center">

                    <div
                        class="mx-auto flex size-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400">

                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">

                            <line x1="2" x2="22" y1="12" y2="12" />

                            <line x1="12" x2="12" y1="2" y2="22" />

                            <path d="m20 16-4-4 4-4" />
                            <path d="m4 8 4 4-4 4" />
                            <path d="m16 4-4 4-4-4" />
                            <path d="m8 20 4-4 4 4" />
                        </svg>
                    </div>

                    <h3 class="mt-3 text-sm font-semibold text-slate-800">
                        Belum ada freezer
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Freezer yang ditambahkan akan muncul di bagian ini.
                    </p>
                </div>
            @endif
        </div>

    </div>

    <script>
        (() => {
            const clockElement = document.getElementById(
                'admin-current-time',
            );

            if (!clockElement) {
                return;
            }

            const formatter = new Intl.DateTimeFormat(
                'id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                    hourCycle: 'h23',
                },
            );

            const updateClock = () => {
                const currentDate = new Date();
                const formattedTime = formatter
                    .format(currentDate)
                    .replace(':', '.');

                clockElement.textContent =
                    `${formattedTime} WIB`;

                clockElement.setAttribute(
                    'datetime',
                    currentDate.toISOString(),
                );
            };

            updateClock();

            window.setInterval(
                updateClock,
                60_000,
            );
        })();
    </script>
@endsection
