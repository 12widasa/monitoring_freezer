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
                Periksa informasi unit sebelum memulai pemeriksaan.
            </p>
        </header>

        <div class="mt-4 grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(300px,0.36fr)]">

            {{-- Kolom utama --}}
            <div class="min-w-0 space-y-4">

                {{--
                    SLICING STATE ONLY

                    Simulasi frontend:
                    queued → inspecting → repairing → completed

                    Bukan business logic.
                    Tidak membaca atau menulis database.
                    Tidak mengirim form atau memanggil route.
                    Tidak memakai localStorage atau sessionStorage.
                    Refresh halaman selalu kembali ke state queued.
                              --}}

                {{-- State: queued --}}
                <div id="queuedState" data-slicing-state="queued" class="space-y-4">

                    {{-- Ringkasan tugas --}}
                    <section aria-label="Ringkasan tugas reparasi"
                        class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                        <div class="p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Menunggu diperiksa
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Data terverifikasi
                                </span>
                            </div>

                            <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                Freezer FZ-00131
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Modena MD-320
                                <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                MDN-320-1842
                            </p>

                            <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Keluhan awal
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-700">
                                    Freezer tidak mencapai suhu dingin dan kompresor terdengar lebih bising dari
                                    biasanya.
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Dicatat oleh admin saat unit didaftarkan.
                                </p>
                            </div>

                            <div
                                class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Ditugaskan hari ini, pukul 08.15
                                </p>

                                <button type="button" data-next-state="inspecting"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="m9 11 3 3L22 4" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />

                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    Mulai pemeriksaan
                                </button>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- State: inspecting --}}
                <div id="inspectionState" data-slicing-state="inspecting" class="hidden space-y-4">

                    {{-- Ringkasan tugas --}}
                    <section aria-label="Ringkasan tugas reparasi"
                        class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                        <div class="p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Pemeriksaan berlangsung
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Data terverifikasi
                                </span>
                            </div>

                            <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                Freezer FZ-00131
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Modena MD-320
                                <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                MDN-320-1842
                            </p>

                            <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Keluhan awal
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-700">
                                    Freezer tidak mencapai suhu dingin dan kompresor terdengar lebih bising dari
                                    biasanya.
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Dicatat oleh admin saat unit didaftarkan.
                                </p>
                            </div>

                            <div
                                class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Pemeriksaan dimulai hari ini, pukul 09.10
                                </p>

                                <span
                                    class="inline-flex w-full items-center justify-center gap-2 rounded border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Pemeriksaan berlangsung
                                </span>
                            </div>
                        </div>
                    </section>

                    {{-- Form hasil pemeriksaan --}}
                    <section id="inspectionForm" aria-labelledby="inspectionFormTitle"
                        class="overflow-hidden rounded border border-blue-200 bg-white shadow-sm">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 id="inspectionFormTitle" class="text-base font-semibold text-slate-950">
                                        Hasil pemeriksaan
                                    </h2>

                                    <span
                                        class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                        Pemeriksaan berlangsung
                                    </span>
                                </div>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Catat temuan, dokumentasi, dan kebutuhan komponen sebelum memulai perbaikan.
                                </p>
                            </div>
                        </div>

                        <form>
                            <div class="space-y-5 p-4 sm:p-5">

                                {{-- Temuan pemeriksaan --}}
                                <div>
                                    <label for="inspectionFinding"
                                        class="mb-2 block text-sm font-semibold text-slate-900">
                                        Temuan pemeriksaan
                                        <span class="text-red-600">*</span>
                                    </label>

                                    <textarea id="inspectionFinding" rows="5"
                                        placeholder="Contoh: Kompresor menyala, tetapi tekanan refrigeran rendah. Relay kompresor menunjukkan tanda panas berlebih."
                                        class="block w-full rounded border border-slate-300 bg-white p-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600"></textarea>

                                    <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                        Jelaskan kondisi unit, dugaan kerusakan, dan hasil pengecekan awal.
                                    </p>
                                </div>

                                {{-- Foto pemeriksaan --}}
                                <div>
                                    <div class="flex flex-wrap items-end justify-between gap-2">
                                        <div>
                                            <label for="inspectionPhotos"
                                                class="block text-sm font-semibold text-slate-900">
                                                Foto pemeriksaan
                                            </label>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Maksimal 3 foto, masing-masing maksimal 5 MB.
                                            </p>
                                        </div>

                                        <span class="text-xs font-medium text-slate-500">
                                            0 dari 3 foto
                                        </span>
                                    </div>

                                    <label for="inspectionPhotos"
                                        class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center hover:border-blue-400 hover:bg-blue-50">

                                        <svg class="size-7 text-slate-400" aria-hidden="true" fill="none"
                                            viewBox="0 0 24 24">
                                            <path d="M12 16V4m0 0L8 8m4-4 4 4" stroke="currentColor"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />

                                            <path d="M5 13v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5" stroke="currentColor"
                                                stroke-linecap="round" stroke-width="2" />
                                        </svg>

                                        <span class="mt-2 text-sm font-medium text-slate-700">
                                            Pilih foto pemeriksaan
                                        </span>

                                        <span class="mt-1 text-xs text-slate-500">
                                            JPG, JPEG, atau PNG
                                        </span>

                                        <input id="inspectionPhotos" type="file" accept=".jpg,.jpeg,.png" multiple
                                            class="hidden">
                                    </label>
                                </div>

                                {{-- Kebutuhan komponen --}}
                                <div>
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                Kebutuhan komponen sementara
                                            </h3>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Komponen masih dapat ditambah atau diubah selama proses perbaikan.
                                            </p>
                                        </div>

                                        <button type="button"
                                            class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-blue-200 bg-white px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 sm:w-auto">

                                            <svg class="size-4" aria-hidden="true" fill="none"
                                                viewBox="0 0 24 24">
                                                <path d="M12 5v14M5 12h14" stroke="currentColor"
                                                    stroke-linecap="round" stroke-width="2" />
                                            </svg>

                                            Tambah komponen
                                        </button>
                                    </div>

                                    <div class="mt-3 space-y-3">

                                        {{-- Komponen dummy 1 --}}
                                        <div class="rounded border border-slate-200 bg-slate-50/50 p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <p class="text-xs font-semibold text-slate-700">
                                                    Komponen 1
                                                </p>

                                                <button type="button" aria-label="Hapus komponen kompresor"
                                                    class="inline-flex size-8 shrink-0 items-center justify-center rounded border border-red-200 bg-white text-red-600 hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100">

                                                    <svg class="size-4" aria-hidden="true" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <path d="M4 7h16M10 11v6M14 11v6M9 7l1-3h4l1 3m-8 0 1 13h8l1-13"
                                                            stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label for="componentName1"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Nama komponen
                                                        <span class="text-red-600">*</span>
                                                    </label>

                                                    <input id="componentName1" type="text"
                                                        value="Kompresor Secop SC18G"
                                                        placeholder="Contoh: Kompresor Secop SC18G"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentPartNumber1"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Nomor part
                                                        <span class="font-normal text-slate-400">(opsional)</span>
                                                    </label>

                                                    <input id="componentPartNumber1" type="text" value="104G8820"
                                                        placeholder="Contoh: 104G8820"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>
                                            </div>

                                            <div class="mt-3 grid gap-3 sm:grid-cols-[120px_160px_minmax(0,1fr)]">

                                                <div>
                                                    <label for="componentQuantity1"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Jumlah
                                                    </label>

                                                    <input id="componentQuantity1" type="number" min="1"
                                                        value="1"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentUnit1"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Satuan
                                                    </label>

                                                    <input id="componentUnit1" type="text" value="unit"
                                                        placeholder="Contoh: unit"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentNote1"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Catatan
                                                        <span class="font-normal text-slate-400">(opsional)</span>
                                                    </label>

                                                    <input id="componentNote1" type="text"
                                                        value="Suara kompresor kasar dan suhu meningkat."
                                                        placeholder="Alasan komponen dibutuhkan"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Komponen dummy 2 --}}
                                        <div class="rounded border border-slate-200 bg-slate-50/50 p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <p class="text-xs font-semibold text-slate-700">
                                                    Komponen 2
                                                </p>

                                                <button type="button" aria-label="Hapus komponen relay kompresor"
                                                    class="inline-flex size-8 shrink-0 items-center justify-center rounded border border-red-200 bg-white text-red-600 hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100">

                                                    <svg class="size-4" aria-hidden="true" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <path d="M4 7h16M10 11v6M14 11v6M9 7l1-3h4l1 3m-8 0 1 13h8l1-13"
                                                            stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label for="componentName2"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Nama komponen
                                                        <span class="text-red-600">*</span>
                                                    </label>

                                                    <input id="componentName2" type="text" value="Relay kompresor"
                                                        placeholder="Contoh: Relay kompresor"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentPartNumber2"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Nomor part
                                                        <span class="font-normal text-slate-400">(opsional)</span>
                                                    </label>

                                                    <input id="componentPartNumber2" type="text"
                                                        placeholder="Belum diketahui"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>
                                            </div>

                                            <div class="mt-3 grid gap-3 sm:grid-cols-[120px_160px_minmax(0,1fr)]">

                                                <div>
                                                    <label for="componentQuantity2"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Jumlah
                                                    </label>

                                                    <input id="componentQuantity2" type="number" min="1"
                                                        value="1"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentUnit2"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Satuan
                                                    </label>

                                                    <input id="componentUnit2" type="text" value="unit"
                                                        placeholder="Contoh: unit"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>

                                                <div>
                                                    <label for="componentNote2"
                                                        class="mb-1.5 block text-xs font-medium text-slate-700">
                                                        Catatan
                                                        <span class="font-normal text-slate-400">(opsional)</span>
                                                    </label>

                                                    <input id="componentNote2" type="text"
                                                        value="Housing relay terlihat menghitam akibat panas."
                                                        placeholder="Alasan komponen dibutuhkan"
                                                        class="block w-full rounded border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer form --}}
                            <div
                                class="flex flex-col gap-3 border-t-[1.6px] border-slate-100 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">

                                <div class="inline-flex items-center gap-2 text-xs text-slate-500">
                                    <span class="relative flex size-2">
                                        <span
                                            class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75">
                                        </span>

                                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500">
                                        </span>
                                    </span>

                                    Draft tersimpan otomatis
                                </div>

                                <button type="button" data-next-state="repairing"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Mulai perbaikan
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                {{-- State: repairing --}}
                <div id="repairingState" data-slicing-state="repairing" class="hidden space-y-4">

                    {{-- Ringkasan tugas --}}
                    <section aria-label="Ringkasan tugas reparasi"
                        class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                        <div class="p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path
                                            d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    Proses perbaikan
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Data terverifikasi
                                </span>
                            </div>

                            <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                Freezer FZ-00131
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Modena MD-320
                                <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                MDN-320-1842
                            </p>

                            <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Keluhan awal
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-700">
                                    Freezer tidak mencapai suhu dingin dan kompresor terdengar lebih bising dari
                                    biasanya.
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Dicatat oleh admin saat unit didaftarkan.
                                </p>
                            </div>

                            <div
                                class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Ditugaskan hari ini, pukul 08.15
                                </p>

                                <span
                                    class="inline-flex w-full items-center justify-center gap-2 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path
                                            d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    Perbaikan berlangsung
                                </span>
                            </div>
                        </div>
                    </section>

                    {{-- Form hasil pemeriksaan --}}
                    <section id="inspectionForm" aria-labelledby="inspectionFormTitle"
                        class="overflow-hidden rounded border border-blue-200 bg-white shadow-sm">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 id="inspectionFormTitle" class="text-base font-semibold text-slate-950">
                                        Hasil pemeriksaan
                                    </h2>

                                    <span
                                        class="inline-flex items-center rounded border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
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
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Temuan pemeriksaan
                                </h3>

                                <div class="mt-2 rounded border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-sm leading-6 text-slate-700">
                                        Kompresor menyala, tetapi menghasilkan suara kasar dan mengalami peningkatan
                                        suhu.
                                        Relay kompresor menunjukkan tanda panas berlebih pada bagian housing. Tekanan
                                        refrigeran juga terindikasi berada di bawah kondisi normal.
                                    </p>
                                </div>
                            </div>

                            {{-- Foto pemeriksaan --}}
                            <div>
                                <div class="flex flex-wrap items-end justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Foto pemeriksaan
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Dokumentasi yang dicatat saat pemeriksaan awal.
                                        </p>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500">
                                        3 foto
                                    </span>
                                </div>

                                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                    @foreach (['Kondisi kompresor', 'Relay kompresor', 'Pengukuran tekanan'] as $inspectionPhoto)
                                        <figure>
                                            <button type="button"
                                                aria-label="Buka {{ strtolower($inspectionPhoto) }}"
                                                class="group flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                                <div class="flex flex-col items-center gap-2">
                                                    <svg class="size-7" aria-hidden="true" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <rect x="3" y="5" width="18" height="14"
                                                            rx="2" stroke="currentColor" stroke-width="2" />

                                                        <circle cx="8.5" cy="10" r="1.5"
                                                            stroke="currentColor" stroke-width="2" />

                                                        <path d="m21 15-4.5-4.5L8 19" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" />
                                                    </svg>

                                                    <span class="text-xs font-medium">
                                                        Foto pemeriksaan
                                                    </span>
                                                </div>
                                            </button>

                                            <figcaption class="mt-2 text-center text-xs text-slate-500">
                                                {{ $inspectionPhoto }}
                                            </figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Kebutuhan komponen hasil pemeriksaan --}}
                            <div>
                                <div class="flex flex-wrap items-end justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Kebutuhan komponen
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Komponen yang dicatat saat pemeriksaan awal.
                                        </p>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500">
                                        2 komponen
                                    </span>
                                </div>

                                <div class="mt-3 divide-y-[1.6px] divide-slate-100 rounded border border-slate-200">
                                    <div class="flex flex-col gap-3 p-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    Kompresor Secop SC18G
                                                </p>

                                                <span
                                                    class="inline-flex rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">
                                                    Dibutuhkan
                                                </span>
                                            </div>

                                            <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                                <div class="inline-flex gap-1">
                                                    <dt>Nomor part:</dt>
                                                    <dd class="font-medium text-slate-700">104G8820</dd>
                                                </div>

                                                <div class="inline-flex gap-1">
                                                    <dt>Jumlah:</dt>
                                                    <dd class="font-medium text-slate-700">1 unit</dd>
                                                </div>
                                            </dl>

                                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                                Suara kompresor kasar dan suhu meningkat.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3 p-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    Relay kompresor
                                                </p>

                                                <span
                                                    class="inline-flex rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">
                                                    Dibutuhkan
                                                </span>
                                            </div>

                                            <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                                <div class="inline-flex gap-1">
                                                    <dt>Nomor part:</dt>
                                                    <dd class="font-medium text-slate-700">Belum diketahui</dd>
                                                </div>

                                                <div class="inline-flex gap-1">
                                                    <dt>Jumlah:</dt>
                                                    <dd class="font-medium text-slate-700">1 unit</dd>
                                                </div>
                                            </dl>

                                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                                Housing relay terlihat menghitam akibat panas.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 border-t-[1.6px] border-slate-100 pt-4 text-xs text-slate-500">

                                <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Pemeriksaan diselesaikan hari ini, pukul 10.20.
                            </div>
                        </div>
                    </section>

                    {{-- Form progres perbaikan --}}
                    <section aria-labelledby="repairProgressFormTitle"
                        class="overflow-hidden rounded border border-amber-200 bg-white shadow-sm">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 id="repairProgressFormTitle" class="text-base font-semibold text-slate-950">
                                    Progres perbaikan
                                </h2>

                                <span
                                    class="inline-flex items-center rounded border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                                    Perbaikan berlangsung
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Catat tindakan, dokumentasi, dan perubahan kebutuhan komponen selama perbaikan.
                            </p>
                        </div>

                        <form>
                            <div class="space-y-5 p-4 sm:p-5">

                                {{-- Catatan progres --}}
                                <div>
                                    <label for="repairProgressNote"
                                        class="mb-2 block text-sm font-semibold text-slate-900">
                                        Catatan progres
                                        <span class="text-red-600">*</span>
                                    </label>

                                    <textarea id="repairProgressNote" rows="5"
                                        placeholder="Contoh: Kompresor lama telah dilepas. Jalur refrigeran sedang dibersihkan sebelum kompresor pengganti dipasang."
                                        class="block w-full rounded border border-slate-300 bg-white p-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">Kompresor lama telah dilepas. Jalur refrigeran sedang dibersihkan sebelum pemasangan kompresor pengganti.</textarea>

                                    <p class="mt-1.5 text-xs leading-5 text-slate-500">
                                        Catat tindakan terbaru dan kondisi unit setelah tindakan dilakukan.
                                    </p>
                                </div>

                                {{-- Foto progres --}}
                                <div>
                                    <div class="flex flex-wrap items-end justify-between gap-2">
                                        <div>
                                            <label for="repairProgressPhotos"
                                                class="block text-sm font-semibold text-slate-900">
                                                Foto progres
                                            </label>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Maksimal 3 foto untuk setiap pembaruan progres.
                                            </p>
                                        </div>

                                        <span class="text-xs font-medium text-slate-500">
                                            1 dari 3 foto
                                        </span>
                                    </div>

                                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                        <figure>
                                            <button type="button" aria-label="Buka foto pelepasan kompresor"
                                                class="group flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                                <div class="flex flex-col items-center gap-2">
                                                    <svg class="size-7" aria-hidden="true" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <rect x="3" y="5" width="18" height="14"
                                                            rx="2" stroke="currentColor" stroke-width="2" />

                                                        <circle cx="8.5" cy="10" r="1.5"
                                                            stroke="currentColor" stroke-width="2" />

                                                        <path d="m21 15-4.5-4.5L8 19" stroke="currentColor"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" />
                                                    </svg>

                                                    <span class="text-xs font-medium">
                                                        Foto progres
                                                    </span>
                                                </div>
                                            </button>

                                            <figcaption class="mt-2 text-center text-xs text-slate-500">
                                                Pelepasan kompresor lama
                                            </figcaption>
                                        </figure>

                                        <label for="repairProgressPhotos"
                                            class="flex aspect-[16/9] cursor-pointer flex-col items-center justify-center rounded border border-dashed border-slate-300 bg-slate-50 px-4 text-center hover:border-blue-400 hover:bg-blue-50">

                                            <svg class="size-6 text-slate-400" aria-hidden="true" fill="none"
                                                viewBox="0 0 24 24">
                                                <path d="M12 16V4m0 0L8 8m4-4 4 4" stroke="currentColor"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" />

                                                <path d="M5 13v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5"
                                                    stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                            </svg>

                                            <span class="mt-2 text-xs font-medium text-slate-700">
                                                Tambah foto
                                            </span>

                                            <input id="repairProgressPhotos" type="file" accept=".jpg,.jpeg,.png"
                                                multiple class="hidden">
                                        </label>
                                    </div>
                                </div>

                                {{-- Komponen tambahan --}}
                                <div>
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">
                                                Komponen tambahan
                                            </h3>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Tambahkan komponen baru yang ditemukan selama proses perbaikan.
                                            </p>
                                        </div>

                                        <button type="button"
                                            class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-blue-200 bg-white px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 sm:w-auto">

                                            <svg class="size-4" aria-hidden="true" fill="none"
                                                viewBox="0 0 24 24">
                                                <path d="M12 5v14M5 12h14" stroke="currentColor"
                                                    stroke-linecap="round" stroke-width="2" />
                                            </svg>

                                            Tambah komponen
                                        </button>
                                    </div>

                                    <div
                                        class="mt-3 rounded border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada komponen tambahan
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Komponen hasil pemeriksaan tetap tercatat pada card di atas.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer progres --}}
                            <div
                                class="flex flex-col gap-3 border-t-[1.6px] border-slate-100 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">

                                <div class="inline-flex items-center gap-2 text-xs text-slate-500">
                                    <span class="relative flex size-2">
                                        <span
                                            class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75">
                                        </span>

                                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500">
                                        </span>
                                    </span>

                                    Draft progres tersimpan otomatis
                                </div>

                                <button type="button" data-next-state="completed"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-300 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Selesaikan perbaikan
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                {{-- State: completed --}}
                <div id="completedState" data-slicing-state="completed" class="hidden space-y-4">

                    {{-- Ringkasan tugas selesai --}}
                    <section aria-label="Ringkasan tugas reparasi selesai"
                        class="overflow-hidden rounded border border-emerald-200 bg-white shadow-sm">

                        <div class="p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Perbaikan selesai
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Data terverifikasi
                                </span>
                            </div>

                            <h2 class="mt-3 text-xl font-bold text-slate-950 sm:text-2xl">
                                Freezer FZ-00131
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Modena MD-320
                                <span class="mx-1 text-slate-300" aria-hidden="true">•</span>
                                MDN-320-1842
                            </p>

                            <div class="mt-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Keluhan awal
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-700">
                                    Freezer tidak mencapai suhu dingin dan kompresor terdengar lebih bising dari
                                    biasanya.
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Dicatat oleh admin saat unit didaftarkan.
                                </p>
                            </div>

                            <div
                                class="mt-4 flex flex-col gap-3 border-t-[1.6px] border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                    <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    Diselesaikan hari ini, pukul 14.35
                                </p>

                                <span
                                    class="inline-flex w-full items-center justify-center gap-2 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 sm:w-auto">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
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
                                <h2 id="completedRepairTitle" class="text-base font-semibold text-slate-950">
                                    Hasil perbaikan
                                </h2>

                                <span
                                    class="inline-flex items-center rounded border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                                    Selesai
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Ringkasan tindakan dan komponen setelah pekerjaan diselesaikan.
                            </p>
                        </div>

                        <div class="space-y-5 p-4 sm:p-5">

                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">
                                    Tindakan perbaikan
                                </h3>

                                <div class="mt-2 rounded border border-slate-200 bg-slate-50 p-3">
                                    <p class="text-sm leading-6 text-slate-700">
                                        Kompresor lama dilepas dan diganti dengan Kompresor Secop SC18G.
                                        Relay kompresor diganti, jalur refrigeran dibersihkan, kemudian sistem
                                        divakum dan diisi ulang. Unit telah diuji dan kembali mencapai suhu kerja
                                        normal.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <div class="flex flex-wrap items-end justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Komponen yang digunakan
                                        </h3>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Komponen yang tercatat pada pekerjaan ini.
                                        </p>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500">
                                        2 komponen
                                    </span>
                                </div>

                                <div class="mt-3 divide-y-[1.6px] divide-slate-100 rounded border border-slate-200">

                                    <div class="p-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-900">
                                                Kompresor Secop SC18G
                                            </p>

                                            <span
                                                class="inline-flex rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                                Terpasang
                                            </span>
                                        </div>

                                        <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                            <div class="inline-flex gap-1">
                                                <dt>Nomor part:</dt>
                                                <dd class="font-medium text-slate-700">
                                                    104G8820
                                                </dd>
                                            </div>

                                            <div class="inline-flex gap-1">
                                                <dt>Jumlah:</dt>
                                                <dd class="font-medium text-slate-700">
                                                    1 unit
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <div class="p-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-slate-900">
                                                Relay kompresor
                                            </p>

                                            <span
                                                class="inline-flex rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                                Terpasang
                                            </span>
                                        </div>

                                        <dl class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                                            <div class="inline-flex gap-1">
                                                <dt>Nomor part:</dt>
                                                <dd class="font-medium text-slate-700">
                                                    Belum diketahui
                                                </dd>
                                            </div>

                                            <div class="inline-flex gap-1">
                                                <dt>Jumlah:</dt>
                                                <dd class="font-medium text-slate-700">
                                                    1 unit
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 border-t-[1.6px] border-slate-100 pt-4 text-xs text-slate-500">

                                <svg class="size-4 shrink-0 text-emerald-600" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Diselesaikan oleh Teknisi Utama pada 16 Juli 2026, pukul 14.35.
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Foto kondisi awal --}}
                <section aria-labelledby="initialConditionPhotosTitle"
                    class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                        <h2 id="initialConditionPhotosTitle" class="text-sm font-semibold text-slate-900">
                            Foto kondisi awal
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Dokumentasi unit sebelum pemeriksaan dilakukan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">

                        {{-- Foto tampak depan --}}
                        <figure>
                            <button type="button" aria-label="Buka foto tampak depan freezer"
                                class="group flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                <div class="flex flex-col items-center gap-2">
                                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <rect x="3" y="5" width="18" height="14" rx="2"
                                            stroke="currentColor" stroke-width="2" />

                                        <circle cx="8.5" cy="10" r="1.5" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m21 15-4.5-4.5L8 19" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    <span class="text-xs font-medium">
                                        Foto belum tersedia
                                    </span>
                                </div>
                            </button>

                            <figcaption class="mt-2 text-center text-xs text-slate-500">
                                Tampak depan freezer
                            </figcaption>
                        </figure>

                        {{-- Foto nomor seri --}}
                        <figure>
                            <button type="button" aria-label="Buka foto nomor seri freezer"
                                class="group flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                <div class="flex flex-col items-center gap-2">
                                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <rect x="3" y="5" width="18" height="14" rx="2"
                                            stroke="currentColor" stroke-width="2" />

                                        <circle cx="8.5" cy="10" r="1.5" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m21 15-4.5-4.5L8 19" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    <span class="text-xs font-medium">
                                        Foto belum tersedia
                                    </span>
                                </div>
                            </button>

                            <figcaption class="mt-2 text-center text-xs text-slate-500">
                                Nomor seri freezer
                            </figcaption>
                        </figure>

                        {{-- Foto bagian bermasalah --}}
                        <figure class="sm:col-span-2 lg:col-span-1">
                            <button type="button" aria-label="Buka foto bagian freezer yang dilaporkan bermasalah"
                                class="group flex aspect-[16/9] w-full items-center justify-center overflow-hidden rounded border border-slate-200 bg-slate-100 text-slate-400 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                                <div class="flex flex-col items-center gap-2">
                                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <rect x="3" y="5" width="18" height="14" rx="2"
                                            stroke="currentColor" stroke-width="2" />

                                        <circle cx="8.5" cy="10" r="1.5" stroke="currentColor"
                                            stroke-width="2" />

                                        <path d="m21 15-4.5-4.5L8 19" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" />
                                    </svg>

                                    <span class="text-xs font-medium">
                                        Foto belum tersedia
                                    </span>
                                </div>
                            </button>

                            <figcaption class="mt-2 text-center text-xs text-slate-500">
                                Bagian yang dilaporkan bermasalah
                            </figcaption>
                        </figure>
                    </div>
                </section>

                {{-- Riwayat tugas --}}
                <section aria-labelledby="taskHistoryTitle"
                    class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3 sm:px-5">
                        <h2 id="taskHistoryTitle" class="text-sm font-semibold text-slate-900">
                            Riwayat tugas
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Pembaruan terbaru ditampilkan paling atas.
                        </p>
                    </div>

                    <div class="p-4 sm:p-5">
                        <ol
                            class="relative ms-2 before:absolute before:inset-y-3 before:start-0 before:w-px before:bg-slate-200">

                            {{-- SLICING STATE ONLY: riwayat completed --}}
                            <li data-history-state="completed"
                                class="hidden relative ms-5 border-b-[1.6px] border-slate-100 py-3">

                                <span
                                    class="absolute -start-[27px] top-[13px] z-10 flex size-4 items-center justify-center rounded-full border-2 border-emerald-600 bg-white">

                                    <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                </span>

                                <div class="grid gap-1 text-sm sm:grid-cols-[145px_minmax(0,1fr)] sm:gap-x-5">
                                    <time class="text-slate-500">
                                        16 Juli 2026,<br class="hidden sm:block">
                                        pukul 14.35
                                    </time>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            Perbaikan selesai
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Perbaikan freezer telah diselesaikan oleh Teknisi Utama.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            {{-- Riwayat terbaru --}}
                            <li class="relative ms-5 border-b-[1.6px] border-slate-100 py-3">
                                <span
                                    class="absolute -start-[27px] top-[13px] z-10 flex size-4 items-center justify-center rounded-full border-2 border-blue-600 bg-white">

                                    <span class="size-1.5 rounded-full bg-blue-600"></span>
                                </span>

                                <div class="grid gap-1 text-sm sm:grid-cols-[145px_minmax(0,1fr)] sm:gap-x-5">
                                    <time class="text-slate-500">
                                        16 Juli 2026,<br class="hidden sm:block">
                                        pukul 08.15
                                    </time>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            Tugas diberikan
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Tugas pemeriksaan diberikan kepada Budi Santoso.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            {{-- Data diverifikasi --}}
                            <li class="relative ms-5 border-b-[1.6px] border-slate-100 py-3">
                                <span
                                    class="absolute -start-[25px] top-[15px] z-10 size-3 rounded-full bg-blue-600 ring-2 ring-white">
                                </span>

                                <div class="grid gap-1 text-sm sm:grid-cols-[145px_minmax(0,1fr)] sm:gap-x-5">
                                    <time class="text-slate-500">
                                        16 Juli 2026,<br class="hidden sm:block">
                                        pukul 07.50
                                    </time>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            Data sudah sesuai
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Nomor seri fisik telah diverifikasi oleh admin.
                                        </p>
                                    </div>
                                </div>
                            </li>

                            {{-- Freezer didaftarkan --}}
                            <li class="relative ms-5 pt-3">
                                <span
                                    class="absolute -start-[25px] top-[15px] z-10 size-3 rounded-full bg-blue-600 ring-2 ring-white">
                                </span>

                                <div class="grid gap-1 text-sm sm:grid-cols-[145px_minmax(0,1fr)] sm:gap-x-5">
                                    <time class="text-slate-500">
                                        15 Juli 2026,<br class="hidden sm:block">
                                        pukul 16.20
                                    </time>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            Freezer didaftarkan
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Data freezer dan keluhan awal telah dicatat oleh admin.
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                </section>
            </div>

            {{-- Informasi pendukung --}}
            <aside class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-1">

                {{-- Informasi freezer --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi freezer
                        </h2>
                    </div>

                    <dl class="space-y-4 p-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-500">
                                Pemilik
                            </dt>

                            <dd class="mt-1 font-medium leading-5 text-slate-800">
                                PT. Magnum Ice Cream Indonesia
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Merek
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                Modena
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Model
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                MD-320
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Nomor seri
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                MDN-320-1842
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Kapasitas
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                320 liter
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Estimasi umur
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                1–3 tahun
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Informasi penugasan --}}
                <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-slate-900">
                            Informasi penugasan
                        </h2>
                    </div>

                    <dl class="space-y-4 p-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-500">
                                Ditugaskan oleh
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                Admin Karyatama
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Teknisi
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                {{ auth()->user()->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Tanggal penugasan
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                16 Juli 2026
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Waktu penugasan
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                08.15
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs text-slate-500">
                                Jenis tugas
                            </dt>

                            <dd class="mt-1 font-medium text-slate-800">
                                Pemeriksaan awal
                            </dd>
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
                <span class="block sm:inline">
                    Sistem Monitoring Reparasi Freezer
                </span>
            </p>
        </div>
    </footer>

    <script>
        /*
         * SLICING STATE ONLY
         *
         * Simulasi perpindahan UI tanpa business logic.
         * Tidak mengirim data ke backend.
         * Tidak memakai localStorage atau sessionStorage.
         * Refresh halaman selalu kembali ke state queued.
         */
        document.addEventListener('DOMContentLoaded', () => {
            const slicingStates = document.querySelectorAll('[data-slicing-state]');
            const transitionButtons = document.querySelectorAll('[data-next-state]');
            const stateHistoryItems = document.querySelectorAll('[data-history-state]');

            let currentSlicingState = 'queued';

            const showSlicingState = (stateName) => {
                const targetState = document.querySelector(
                    `[data-slicing-state="${stateName}"]`
                );

                if (!targetState) {
                    console.warn(`Slicing state "${stateName}" tidak ditemukan.`);
                    return;
                }

                slicingStates.forEach((stateElement) => {
                    const isActive = stateElement.dataset.slicingState === stateName;

                    stateElement.classList.toggle('hidden', !isActive);
                });

                stateHistoryItems.forEach((historyItem) => {
                    const shouldShow = historyItem.dataset.historyState === stateName;

                    historyItem.classList.toggle('hidden', !shouldShow);
                });

                currentSlicingState = stateName;

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                });
            };

            transitionButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const nextState = button.dataset.nextState;

                    if (!nextState || nextState === currentSlicingState) {
                        return;
                    }

                    showSlicingState(nextState);
                });
            });

            showSlicingState('queued');
        });
    </script>
</body>

</html>
