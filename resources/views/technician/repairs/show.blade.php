<!DOCTYPE html>
<html lang="id">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <title>Detail Tugas Reparasi - Karyatama</title>
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
 
<body class="min-h-screen bg-slate-50 text-slate-900">
 
    {{-- Header teknisi --}}
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
 
            {{-- Brand --}}
            <a href="{{ route('technician.dashboard') }}" class="flex min-w-0 items-center gap-3">
 
                <img src="{{ asset('assets/Logo.png') }}" alt="Logo Karyatama" class="size-9 shrink-0 object-contain">
 
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold leading-tight text-slate-900 sm:text-base">
                        Karyatama
                    </p>
 
                    <p class="hidden truncate text-xs text-slate-500 sm:block">
                        Sistem Monitoring Freezer
                    </p>
                </div>
            </a>
 
            {{-- Profil teknisi --}}
            <div class="relative">
                <button id="technicianProfileButton" type="button" data-dropdown-toggle="technicianProfileDropdown"
                    data-dropdown-placement="bottom-end" aria-expanded="false"
                    class="flex items-center gap-3 rounded px-2 py-1.5 text-left hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">
 
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">
                        {{ collect(explode(' ', auth()->user()->name))->filter()->take(2)->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}
                    </div>
 
                    <div class="hidden min-w-0 sm:block">
                        <p class="max-w-52 truncate text-sm font-semibold leading-tight text-slate-900">
                            {{ auth()->user()->name }}
                        </p>
 
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ auth()->user()->role->label() }}
                        </p>
                    </div>
 
                    <svg class="size-4 shrink-0 text-slate-500" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="m9 10 3 3 3-3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" />
                    </svg>
                </button>
 
                <div id="technicianProfileDropdown"
                    class="z-50 hidden w-56 rounded border border-slate-200 bg-white p-1.5 shadow-lg">
 
                    <div class="border-b border-slate-100 px-3 py-2">
                        <p class="truncate text-sm font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>
 
                        <p class="mt-0.5 truncate text-xs text-slate-500">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
 
                    <form action="{{ route('logout') }}" method="POST" class="mt-1">
                        @csrf
 
                        <button type="submit"
                            class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">
 
                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path d="M10 17l5-5-5-5M15 12H3" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" />
 
                                <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
 
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
 
    @php
        $intake = $repair->serviceIntake;
        $isVerified = $intake && $intake->status_verifikasi === \App\Enums\VerificationStatus::VERIFIED;
        $complaint = $intake->complaint_note ?? $repair->initial_analysis;
        $assignedAt = $repair->activeAssignment?->assigned_at ?? $repair->created_at;
 
        $inspectingLog = $repair->logs->firstWhere(fn($log) => $log->status === \App\Enums\RepairStatus::INSPECTING);
        $completedLog = $repair->logs->firstWhere(fn($log) => $log->status === \App\Enums\RepairStatus::COMPLETED);
        $requestedComponents = $repair->components->where('status', 'requested');
        $installedComponents = $repair->components->where('status', 'installed');
    @endphp
 
    <main class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
 
        {{-- Navigasi dan judul --}}
        <header>
            <a href="{{ route('technician.dashboard') }}"
                class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">
 
                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
 
                Kembali ke daftar tugas
            </a>
 
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Detail tugas reparasi
            </h1>
 
            <p class="mt-1 text-sm text-slate-500">
                Periksa informasi unit dan perbarui progres reparasi.
            </p>
        </header>
 
        @if (session('success'))
            <div class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
 
        @if ($errors->any())
            <div class="mt-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">Periksa kembali isian Anda:</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
 
        <div class="mt-4 grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(300px,0.36fr)]">
 
            {{-- Kolom utama --}}
            <div class="min-w-0 space-y-4">
 
                {{-- State: queued --}}
                @if ($repair->status === \App\Enums\RepairStatus::QUEUED)
                    <div class="space-y-4">
 
                        {{-- Ringkasan tugas --}}
                        <section aria-label="Ringkasan tugas reparasi"
                            class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Menunggu diperiksa
                                    </span>
 
                                    @if ($isVerified)
                                        <span class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            Data terverifikasi
                                        </span>
                                    @endif
                                </div>
 
                                <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                    Freezer {{ $repair->freezer->freezer_code }}
                                </h2>
 
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                    <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                    {{ $repair->freezer->serial_number }}
                                </p>
 
                                <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                    <h3 class="text-sm font-semibold text-slate-900">Keluhan awal</h3>
                                    <p class="mt-1 text-sm leading-5 text-slate-700">
                                        {{ $complaint ?? 'Belum ada catatan keluhan awal.' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Dicatat oleh admin saat unit didaftarkan.</p>
                                </div>
 
                                <div class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                        <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Ditugaskan {{ $assignedAt->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                    </p>
 
                                    <form method="POST" action="{{ route('technician.repairs.start-inspection', $repair->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
                                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path d="m9 11 3 3L22 4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            Mulai pemeriksaan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                @endif
 
                {{-- State: inspecting --}}
                @if ($repair->status === \App\Enums\RepairStatus::INSPECTING)
                    <div class="space-y-4">
 
                        {{-- Ringkasan tugas --}}
                        <section aria-label="Ringkasan tugas reparasi"
                            class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Pemeriksaan berlangsung
                                    </span>
 
                                    @if ($isVerified)
                                        <span class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            Data terverifikasi
                                        </span>
                                    @endif
                                </div>
 
                                <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                    Freezer {{ $repair->freezer->freezer_code }}
                                </h2>
 
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                    <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                    {{ $repair->freezer->serial_number }}
                                </p>
 
                                <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                    <h3 class="text-sm font-semibold text-slate-900">Keluhan awal</h3>
                                    <p class="mt-1 text-sm leading-5 text-slate-700">
                                        {{ $complaint ?? 'Belum ada catatan keluhan awal.' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Dicatat oleh admin saat unit didaftarkan.</p>
                                </div>
 
                                <div class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                        <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Pemeriksaan dimulai {{ $inspectingLog?->created_at?->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                    </p>
 
                                    <span class="inline-flex w-full items-center justify-center gap-2 rounded border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 sm:w-auto">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Pemeriksaan berlangsung
                                    </span>
                                </div>
                            </div>
                        </section>
 
                        {{-- Form hasil pemeriksaan --}}
                        <section aria-labelledby="inspectionFormTitle"
                            class="overflow-hidden rounded border border-blue-200 bg-white shadow-sm">
 
                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 id="inspectionFormTitle" class="text-base font-semibold text-slate-950">
                                            Hasil pemeriksaan
                                        </h2>
                                        <span class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                            Pemeriksaan berlangsung
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Catat temuan, dokumentasi, dan kebutuhan komponen sebelum memulai perbaikan.
                                    </p>
                                </div>
                            </div>
 
                            <form method="POST" action="{{ route('technician.repairs.inspection.store', $repair->id) }}" enctype="multipart/form-data">
                                @csrf
 
                                <div class="space-y-5 p-4 sm:p-5">
 
                                    {{-- Temuan pemeriksaan --}}
                                    <div>
                                        <label for="inspectionFinding" class="mb-2 block text-sm font-semibold text-slate-900">
                                            Temuan pemeriksaan <span class="text-red-600">*</span>
                                        </label>
                                        <textarea id="inspectionFinding" name="description" rows="5" required
                                            placeholder="Contoh: Kompresor menyala, tetapi tekanan refrigeran rendah. Relay kompresor menunjukkan tanda panas berlebih."
                                            class="block w-full rounded border border-slate-300 bg-white p-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">{{ old('description') }}</textarea>
                                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                            Jelaskan kondisi unit, dugaan kerusakan, dan hasil pengecekan awal.
                                        </p>
                                    </div>
 
                                    {{-- Foto pemeriksaan --}}
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <label for="inspectionPhotos" class="block text-sm font-semibold text-slate-900">
                                                    Foto pemeriksaan
                                                </label>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    Maksimal 3 foto, masing-masing maksimal 5 MB.
                                                </p>
                                            </div>
                                        </div>
 
                                        <label for="inspectionPhotos"
                                            class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center hover:border-blue-400 hover:bg-blue-50">
                                            <svg class="size-7 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path d="M12 16V4m0 0L8 8m4-4 4 4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                <path d="M5 13v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                            </svg>
                                            <span class="mt-2 text-sm font-medium text-slate-700">Pilih foto pemeriksaan</span>
                                            <span class="mt-1 text-xs text-slate-500">JPG, JPEG, atau PNG</span>
                                            <input id="inspectionPhotos" type="file" name="photos[]" accept=".jpg,.jpeg,.png" multiple class="hidden">
                                        </label>
                                    </div>
 
                                    {{-- Kebutuhan komponen --}}
                                    <div>
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">Kebutuhan komponen sementara</h3>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                                    Komponen masih dapat ditambah atau diubah selama proses perbaikan.
                                                </p>
                                            </div>
 
                                            <button type="button" id="addInspectionComponent"
                                                class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-blue-200 bg-white px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 sm:w-auto">
                                                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                                </svg>
                                                Tambah komponen
                                            </button>
                                        </div>
 
                                        <div id="inspectionComponentRows" class="mt-3 space-y-3"></div>
 
                                        <template id="inspectionComponentTemplate">
                                            <div class="rounded border border-slate-200 bg-slate-50/50 p-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <p class="text-xs font-semibold text-slate-700">Komponen</p>
                                                    <button type="button" class="removeComponentRow inline-flex size-8 shrink-0 items-center justify-center rounded border border-red-200 bg-white text-red-600 hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100">
                                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                            <path d="M4 7h16M10 11v6M14 11v6M9 7l1-3h4l1 3m-8 0 1 13h8l1-13" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                        </svg>
                                                    </button>
                                                </div>
 
                                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-medium text-slate-700">
                                                            Nama komponen <span class="text-red-600">*</span>
                                                        </label>
                                                        <select name="components[__INDEX__][component_id]" required
                                                            class="componentSelect block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                                            <option value="">Pilih komponen</option>
                                                            @foreach ($components as $component)
                                                                <option value="{{ $component->id }}" data-part="{{ $component->part_number }}" data-unit="{{ $component->unit }}">{{ $component->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
 
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-medium text-slate-700">
                                                            Nomor part
                                                        </label>
                                                        <input type="text" readonly placeholder="Otomatis dari komponen"
                                                            class="componentPartDisplay block w-full rounded border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500">
                                                    </div>
                                                </div>
 
                                                <div class="mt-3 grid gap-3 sm:grid-cols-[120px_160px_minmax(0,1fr)]">
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-medium text-slate-700">Jumlah</label>
                                                        <input type="number" name="components[__INDEX__][quantity]" min="1" value="1" required
                                                            class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                                    </div>
 
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-medium text-slate-700">Satuan</label>
                                                        <input type="text" readonly placeholder="Otomatis"
                                                            class="componentUnitDisplay block w-full rounded border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500">
                                                    </div>
 
                                                    <div>
                                                        <label class="mb-1.5 block text-xs font-medium text-slate-700">
                                                            Catatan <span class="font-normal text-slate-400">(opsional)</span>
                                                        </label>
                                                        <input type="text" name="components[__INDEX__][note]" placeholder="Alasan komponen dibutuhkan"
                                                            class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
 
                                {{-- Footer form --}}
                                <div class="flex flex-col gap-3 border-t-[1.6px] border-slate-100 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                                    <div class="inline-flex items-center gap-2 text-xs text-slate-500">
                                        Isi hasil pemeriksaan sebelum melanjutkan ke tahap perbaikan.
                                    </div>
 
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Mulai perbaikan
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                @endif
 
                {{-- State: repairing --}}
                @if ($repair->status === \App\Enums\RepairStatus::REPAIRING)
                    <div class="space-y-4">
 
                        {{-- Ringkasan tugas --}}
                        <section aria-label="Ringkasan tugas reparasi"
                            class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Proses perbaikan
                                    </span>
 
                                    @if ($isVerified)
                                        <span class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            Data terverifikasi
                                        </span>
                                    @endif
                                </div>
 
                                <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                    Freezer {{ $repair->freezer->freezer_code }}
                                </h2>
 
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                    <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                    {{ $repair->freezer->serial_number }}
                                </p>
 
                                <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                    <h3 class="text-sm font-semibold text-slate-900">Keluhan awal</h3>
                                    <p class="mt-1 text-sm leading-5 text-slate-700">
                                        {{ $complaint ?? 'Belum ada catatan keluhan awal.' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Dicatat oleh admin saat unit didaftarkan.</p>
                                </div>
 
                                <div class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                        <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Ditugaskan {{ $assignedAt->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                    </p>
 
                                    <span class="inline-flex w-full items-center justify-center gap-2 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 sm:w-auto">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Perbaikan berlangsung
                                    </span>
                                </div>
                            </div>
                        </section>
 
                        {{-- Form hasil pemeriksaan (read-only) --}}
                        <section aria-labelledby="inspectionFormTitle"
                            class="overflow-hidden rounded border border-blue-200 bg-white shadow-sm">
 
                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 id="inspectionFormTitle" class="text-base font-semibold text-slate-950">Hasil pemeriksaan</h2>
                                        <span class="inline-flex items-center rounded border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                                            Pemeriksaan selesai
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Ringkasan hasil pemeriksaan sebelum proses perbaikan dimulai.
                                    </p>
                                </div>
                            </div>
 
                            <div class="space-y-5 p-4 sm:p-5">
 
                                {{-- Temuan pemeriksaan --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-900">Temuan pemeriksaan</h3>
                                    <div class="mt-2 rounded border border-slate-200 bg-slate-50 p-3">
                                        <p class="text-sm leading-6 text-slate-700">
                                            {{ $inspectingLog?->description ?? 'Belum ada catatan hasil pemeriksaan.' }}
                                        </p>
                                    </div>
                                </div>
 
                                {{-- Foto pemeriksaan --}}
                                @if ($inspectingLog && $inspectingLog->photos->isNotEmpty())
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">Foto pemeriksaan</h3>
                                                <p class="mt-1 text-xs text-slate-500">Dokumentasi yang dicatat saat pemeriksaan awal.</p>
                                            </div>
                                            <span class="text-xs font-medium text-slate-500">{{ $inspectingLog->photos->count() }} foto</span>
                                        </div>
 
                                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                            @foreach ($inspectingLog->photos as $photo)
                                                <figure>
                                                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank"
                                                        class="flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100">
                                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto pemeriksaan" class="size-full object-cover">
                                                    </a>
                                                </figure>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
 
                                {{-- Kebutuhan komponen hasil pemeriksaan --}}
                                @if ($requestedComponents->isNotEmpty())
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">Kebutuhan komponen</h3>
                                                <p class="mt-1 text-xs text-slate-500">Komponen yang dicatat saat pemeriksaan awal.</p>
                                            </div>
                                            <span class="text-xs font-medium text-slate-500">{{ $requestedComponents->count() }} komponen</span>
                                        </div>
 
                                        <div class="mt-3 divide-y-[1.6px] divide-slate-100 rounded border border-slate-200">
                                            @foreach ($requestedComponents as $repairComponent)
                                                <div class="flex flex-col gap-3 p-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <p class="text-sm font-semibold text-slate-900">{{ $repairComponent->component->name }}</p>
                                                            <span class="inline-flex rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Dibutuhkan</span>
                                                        </div>
 
                                                        <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                                            <div class="inline-flex gap-1">
                                                                <dt>Nomor part:</dt>
                                                                <dd class="font-medium text-slate-700">{{ $repairComponent->component->part_number ?? 'Belum diketahui' }}</dd>
                                                            </div>
                                                            <div class="inline-flex gap-1">
                                                                <dt>Jumlah:</dt>
                                                                <dd class="font-medium text-slate-700">{{ $repairComponent->quantity }} {{ $repairComponent->component->unit }}</dd>
                                                            </div>
                                                        </dl>
 
                                                        @if ($repairComponent->note)
                                                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $repairComponent->note }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
 
                                <div class="flex items-center gap-2 border-t-[1.6px] border-slate-100 pt-4 text-xs text-slate-500">
                                    <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Pemeriksaan diselesaikan {{ $inspectingLog?->created_at?->translatedFormat('d F Y, \p\u\k\u\l H.i') }}.
                                </div>
                            </div>
                        </section>
 
                        {{-- Form progres perbaikan --}}
                        <section aria-labelledby="repairProgressFormTitle"
                            class="overflow-hidden rounded border border-amber-200 bg-white shadow-sm">
 
                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 id="repairProgressFormTitle" class="text-base font-semibold text-slate-950">Progres perbaikan</h2>
                                    <span class="inline-flex items-center rounded border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                                        Perbaikan berlangsung
                                    </span>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Catat tindakan, dokumentasi, dan komponen yang terpasang sebelum menyelesaikan reparasi.
                                </p>
                            </div>
 
                            <form method="POST" action="{{ route('technician.repairs.progress.store', $repair->id) }}" enctype="multipart/form-data">
                                @csrf
 
                                <div class="space-y-5 p-4 sm:p-5">
 
                                    {{-- Catatan progres --}}
                                    <div>
                                        <label for="repairProgressNote" class="mb-2 block text-sm font-semibold text-slate-900">
                                            Catatan progres <span class="text-red-600">*</span>
                                        </label>
                                        <textarea id="repairProgressNote" name="description" rows="5" required
                                            placeholder="Contoh: Kompresor lama telah dilepas. Jalur refrigeran sedang dibersihkan sebelum kompresor pengganti dipasang."
                                            class="block w-full rounded border border-slate-300 bg-white p-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">{{ old('description') }}</textarea>
                                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                            Catat tindakan terbaru dan kondisi unit setelah tindakan dilakukan.
                                        </p>
                                    </div>
 
                                    {{-- Foto progres --}}
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <label for="repairProgressPhotos" class="block text-sm font-semibold text-slate-900">Foto progres</label>
                                                <p class="mt-1 text-xs text-slate-500">Maksimal 3 foto untuk pembaruan progres ini.</p>
                                            </div>
                                        </div>
 
                                        <label for="repairProgressPhotos"
                                            class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center hover:border-blue-400 hover:bg-blue-50">
                                            <svg class="size-7 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path d="M12 16V4m0 0L8 8m4-4 4 4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                <path d="M5 13v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                            </svg>
                                            <span class="mt-2 text-sm font-medium text-slate-700">Tambah foto progres</span>
                                            <span class="mt-1 text-xs text-slate-500">JPG, JPEG, atau PNG</span>
                                            <input id="repairProgressPhotos" type="file" name="photos[]" accept=".jpg,.jpeg,.png" multiple class="hidden">
                                        </label>
                                    </div>
 
                                    {{-- Komponen terpasang --}}
                                    @if ($requestedComponents->isNotEmpty())
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">Tandai komponen yang sudah terpasang</h3>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Komponen hasil pemeriksaan tetap tercatat pada card di atas.
                                            </p>
 
                                            <div class="mt-3 space-y-2">
                                                @foreach ($requestedComponents as $repairComponent)
                                                    <label class="flex items-center gap-2.5 rounded border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                                                        <input type="checkbox" name="installed_component_ids[]" value="{{ $repairComponent->id }}"
                                                            class="size-4 rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                                                        <span class="font-medium">{{ $repairComponent->component->name }}</span>
                                                        <span class="text-xs text-slate-400">({{ $repairComponent->quantity }} {{ $repairComponent->component->unit }})</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
 
                                {{-- Footer progres --}}
                                <div class="flex flex-col gap-3 border-t-[1.6px] border-slate-100 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                                    <div class="inline-flex items-center gap-2 text-xs text-slate-500">
                                        Pastikan seluruh pekerjaan perbaikan sudah selesai sebelum submit.
                                    </div>
 
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-300 sm:w-auto">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Selesaikan perbaikan
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                @endif
 
                {{-- State: completed --}}
                @if ($repair->status === \App\Enums\RepairStatus::COMPLETED)
                    <div class="space-y-4">
 
                        {{-- Ringkasan tugas selesai --}}
                        <section aria-label="Ringkasan tugas reparasi selesai"
                            class="overflow-hidden rounded border border-emerald-200 bg-white shadow-sm">
 
                            <div class="p-4 sm:p-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Perbaikan selesai
                                    </span>
 
                                    @if ($isVerified)
                                        <span class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            Data terverifikasi
                                        </span>
                                    @endif
                                </div>
 
                                <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                    Freezer {{ $repair->freezer->freezer_code }}
                                </h2>
 
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                    <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                    {{ $repair->freezer->serial_number }}
                                </p>
 
                                <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                    <h3 class="text-sm font-semibold text-slate-900">Keluhan awal</h3>
                                    <p class="mt-1 text-sm leading-5 text-slate-700">
                                        {{ $complaint ?? 'Belum ada catatan keluhan awal.' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Dicatat oleh admin saat unit didaftarkan.</p>
                                </div>
 
                                <div class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                        <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Diselesaikan {{ $completedLog?->created_at?->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                    </p>
 
                                    <span class="inline-flex w-full items-center justify-center gap-2 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 sm:w-auto">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Pekerjaan selesai
                                    </span>
                                </div>
                            </div>
                        </section>
 
                        {{-- Ringkasan hasil perbaikan --}}
                        <section aria-labelledby="completedRepairTitle"
                            class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                            <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 id="completedRepairTitle" class="text-base font-semibold text-slate-950">Hasil perbaikan</h2>
                                    <span class="inline-flex items-center rounded border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Selesai</span>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Ringkasan tindakan dan komponen setelah pekerjaan diselesaikan.
                                </p>
                            </div>
 
                            <div class="space-y-5 p-4 sm:p-5">
 
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-900">Tindakan perbaikan</h3>
                                    <div class="mt-2 rounded border border-slate-200 bg-slate-50 p-3">
                                        <p class="text-sm leading-6 text-slate-700">
                                            {{ $completedLog?->description ?? 'Belum ada catatan penyelesaian.' }}
                                        </p>
                                    </div>
                                </div>
 
                                @if ($completedLog && $completedLog->photos->isNotEmpty())
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <h3 class="text-sm font-semibold text-slate-900">Foto progres akhir</h3>
                                            <span class="text-xs font-medium text-slate-500">{{ $completedLog->photos->count() }} foto</span>
                                        </div>
                                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                            @foreach ($completedLog->photos as $photo)
                                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank"
                                                    class="flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100">
                                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto progres" class="size-full object-cover">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
 
                                @if ($installedComponents->isNotEmpty())
                                    <div>
                                        <div class="flex flex-wrap items-end justify-between gap-2">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900">Komponen yang digunakan</h3>
                                                <p class="mt-1 text-xs text-slate-500">Komponen yang tercatat pada pekerjaan ini.</p>
                                            </div>
                                            <span class="text-xs font-medium text-slate-500">{{ $installedComponents->count() }} komponen</span>
                                        </div>
 
                                        <div class="mt-3 divide-y-[1.6px] divide-slate-100 rounded border border-slate-200">
                                            @foreach ($installedComponents as $repairComponent)
                                                <div class="p-3">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="text-sm font-semibold text-slate-900">{{ $repairComponent->component->name }}</p>
                                                        <span class="inline-flex rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Terpasang</span>
                                                    </div>
 
                                                    <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                                        <div class="inline-flex gap-1">
                                                            <dt>Nomor part:</dt>
                                                            <dd class="font-medium text-slate-700">{{ $repairComponent->component->part_number ?? 'Belum diketahui' }}</dd>
                                                        </div>
                                                        <div class="inline-flex gap-1">
                                                            <dt>Jumlah:</dt>
                                                            <dd class="font-medium text-slate-700">{{ $repairComponent->quantity }} {{ $repairComponent->component->unit }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
 
                                <div class="flex items-center gap-2 border-t-[1.6px] border-slate-100 pt-4 text-xs text-slate-500">
                                    <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Diselesaikan oleh {{ auth()->user()->name }} pada {{ $completedLog?->created_at?->translatedFormat('d F Y, \p\u\k\u\l H.i') }}.
                                </div>
                            </div>
                        </section>
                    </div>
                @endif
 
                {{-- Foto kondisi awal --}}
                <section aria-labelledby="initialConditionPhotosTitle"
                    class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                        <h2 id="initialConditionPhotosTitle" class="text-sm font-semibold text-slate-900">Foto kondisi awal</h2>
                        <p class="mt-1 text-xs text-slate-500">Dokumentasi unit sebelum pemeriksaan dilakukan.</p>
                    </div>
 
                    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
 
                        @php
                            $conditionPhotos = [
                                'Tampak depan freezer' => $repair->freezer->photo_path,
                                'Nomor seri freezer' => null,
                                'Bagian yang dilaporkan bermasalah' => null,
                            ];
                        @endphp
 
                        @foreach ($conditionPhotos as $caption => $photoPath)
                            <figure class="{{ $loop->last ? 'sm:col-span-2 lg:col-span-1' : '' }}">
                                @if ($photoPath)
                                    <a href="{{ asset('storage/' . $photoPath) }}" target="_blank"
                                        class="flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100">
                                        <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $caption }}" class="size-full object-cover">
                                    </a>
                                @else
                                    <div class="flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2" />
                                                <circle cx="8.5" cy="10" r="1.5" stroke="currentColor" stroke-width="2" />
                                                <path d="m21 15-4.5-4.5L8 19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                            <span class="text-xs font-medium">Foto belum tersedia</span>
                                        </div>
                                    </div>
                                @endif
 
                                <figcaption class="mt-2 text-center text-xs text-slate-500">{{ $caption }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
 
                {{-- Riwayat tugas --}}
                <section aria-labelledby="taskHistoryTitle"
                    class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
 
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                        <h2 id="taskHistoryTitle" class="text-sm font-semibold text-slate-900">Riwayat tugas</h2>
                        <p class="mt-1 text-xs text-slate-500">Pembaruan terbaru ditampilkan paling atas.</p>
                    </div>
 
                    <div class="p-4 sm:p-5">
                        @php
                            $historyItems = collect();
 
                            foreach ($repair->logs as $log) {
                                $historyItems->push([
                                    'time' => $log->created_at,
                                    'title' => $log->status->label(),
                                    'description' => $log->description,
                                    'dot' => $log->status === \App\Enums\RepairStatus::COMPLETED ? 'emerald' : 'blue',
                                ]);
                            }
 
                            if ($intake?->verified_at) {
                                $historyItems->push([
                                    'time' => $intake->verified_at,
                                    'title' => 'Data sudah sesuai',
                                    'description' => 'Nomor seri fisik telah diverifikasi oleh admin.',
                                    'dot' => 'blue',
                                ]);
                            }
 
                            if ($intake?->received_at) {
                                $historyItems->push([
                                    'time' => $intake->received_at,
                                    'title' => 'Freezer didaftarkan',
                                    'description' => 'Data freezer dan keluhan awal telah dicatat oleh admin.',
                                    'dot' => 'blue',
                                ]);
                            }
 
                            $historyItems = $historyItems->sortByDesc('time')->values();
                        @endphp
 
                        @if ($historyItems->isEmpty())
                            <p class="text-sm text-slate-500">Belum ada riwayat untuk tugas ini.</p>
                        @else
                            <ol class="relative ms-2 before:absolute before:inset-y-3 before:start-0 before:w-px before:bg-slate-200">
                                @foreach ($historyItems as $item)
                                    <li class="relative ms-5 {{ !$loop->last ? 'border-b-[1.6px] border-slate-100' : '' }} py-3">
                                        <span class="absolute -start-[25px] top-[15px] z-10 size-3 rounded-full bg-{{ $item['dot'] }}-600 ring-2 ring-white"></span>
 
                                        <div class="grid gap-1 text-sm sm:grid-cols-[145px_minmax(0,1fr)] sm:gap-x-5">
                                            <time class="text-slate-500">
                                                {{ $item['time']->translatedFormat('d F Y') }},<br class="hidden sm:block">
                                                pukul {{ $item['time']->translatedFormat('H.i') }}
                                            </time>
 
                                            <div>
                                                <p class="font-semibold text-slate-800">{{ $item['title'] }}</p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $item['description'] }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </section>
            </div>
 
            {{-- Informasi pendukung --}}
            <aside class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-1">
 
                {{-- Informasi freezer --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">Informasi freezer</h2>
                    </div>
 
                    <dl class="space-y-4 p-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-500">Pemilik</dt>
                            <dd class="mt-1 font-medium leading-5 text-slate-800">
                                {{ $repair->freezer->customer->company_name ?? '-' }}
                            </dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Merek</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $repair->freezer->brand }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Model</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $repair->freezer->model }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Nomor seri</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $repair->freezer->serial_number }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Kapasitas</dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                {{ $repair->freezer->capacity_liter ? $repair->freezer->capacity_liter . ' liter' : '-' }}
                            </dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Estimasi umur</dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                {{ $repair->freezer->estimated_age ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </section>
 
                {{-- Informasi penugasan --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">Informasi penugasan</h2>
                    </div>
 
                    <dl class="space-y-4 p-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-500">Ditugaskan oleh</dt>
                            <dd class="mt-1 font-medium text-slate-800">
                                {{ $repair->activeAssignment?->assignedBy?->name ?? $repair->admin?->name ?? '-' }}
                            </dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Teknisi</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ auth()->user()->name }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Tanggal penugasan</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $assignedAt->translatedFormat('d F Y') }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Waktu penugasan</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $assignedAt->translatedFormat('H.i') }}</dd>
                        </div>
 
                        <div>
                            <dt class="text-xs text-slate-500">Status saat ini</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $repair->status->label() }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </main>
 
    <footer class="mt-4 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-center px-4 py-4 text-center sm:px-6">
            <p class="text-xs text-slate-500">
                © {{ now()->year }} CV. Karyatama Agung Abadi
                <span class="hidden sm:inline">—</span>
                <span class="block sm:inline">Sistem Monitoring Reparasi Freezer</span>
            </p>
        </div>
    </footer>
 
    <script>
        (function () {
            const rowsContainer = document.getElementById('inspectionComponentRows');
            const template = document.getElementById('inspectionComponentTemplate');
            const addButton = document.getElementById('addInspectionComponent');
 
            if (!rowsContainer || !template || !addButton) {
                return;
            }
 
            let rowIndex = 0;
 
            function addRow() {
                const html = template.innerHTML.replaceAll('__INDEX__', String(rowIndex));
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                const row = wrapper.firstElementChild;
 
                const select = row.querySelector('.componentSelect');
                const partDisplay = row.querySelector('.componentPartDisplay');
                const unitDisplay = row.querySelector('.componentUnitDisplay');
 
                select.addEventListener('change', function () {
                    const option = select.options[select.selectedIndex];
                    partDisplay.value = option.dataset.part || '';
                    unitDisplay.value = option.dataset.unit || '';
                });
 
                row.querySelector('.removeComponentRow').addEventListener('click', function () {
                    row.remove();
                });
 
                rowsContainer.appendChild(row);
                rowIndex += 1;
            }
 
            addButton.addEventListener('click', addRow);
        })();
    </script>
</body>
 
</html>
 
