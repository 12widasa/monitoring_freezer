<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beranda Teknisi</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
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
        $statusStyle = [
            'gray' => ['bg-slate-50', 'border-slate-200', 'text-slate-700'],
            'blue' => ['bg-blue-50', 'border-blue-200', 'text-blue-700'],
            'yellow' => ['bg-amber-50', 'border-amber-200', 'text-amber-700'],
            'green' => ['bg-emerald-50', 'border-emerald-200', 'text-emerald-700'],
        ];

        $buttonLabel = [
            'queued' => 'Mulai pemeriksaan',
            'inspecting' => 'Lanjutkan pemeriksaan',
            'repairing' => 'Lanjutkan perbaikan',
            'completed' => 'Lihat detail',
        ];
    @endphp

    <main class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-6">
        <div class="space-y-5">

            {{-- Sapaan teknisi --}}
            <section class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                        Selamat datang, {{ str(auth()->user()->name)->before(' ') }}
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Lihat dan lanjutkan pekerjaan reparasi yang ditugaskan kepada Anda.
                    </p>
                </div>

                <p class="inline-flex shrink-0 items-center gap-1.5 text-xs text-slate-500 sm:pt-1 sm:text-sm">
                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                        <path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                    </svg>

                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </section>

            {{-- Ringkasan tugas --}}
            <section aria-label="Ringkasan tugas teknisi" class="grid grid-cols-1 gap-3 md:grid-cols-3">

                {{-- Total tugas aktif --}}
                <article class="flex items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">
                        <svg class="size-6" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <rect x="9" y="2" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2" />
                            <path d="M9 12h6m-6 4h4" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Total tugas aktif</p>
                        <p class="mt-0.5 text-xl font-bold text-slate-950">{{ $totalActiveCount }}</p>
                        <p class="mt-1 text-xs text-slate-500">Tugas yang belum selesai</p>
                    </div>
                </article>

                {{-- Menunggu diperiksa --}}
                <article class="flex items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded border border-amber-100 bg-amber-50 text-amber-500">
                        <svg class="size-6" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Menunggu diperiksa</p>
                        <p class="mt-0.5 text-xl font-bold text-slate-950">{{ $waitingCount }}</p>
                        <p class="mt-1 text-xs text-slate-500">Belum mulai diperiksa</p>
                    </div>
                </article>

                {{-- Sedang dikerjakan --}}
                <article class="flex items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">
                        <svg class="size-6" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Sedang dikerjakan</p>
                        <p class="mt-0.5 text-xl font-bold text-slate-950">{{ $inProgressCount }}</p>
                        <p class="mt-1 text-xs text-slate-500">Pemeriksaan atau perbaikan aktif</p>
                    </div>
                </article>
            </section>

            {{-- Header daftar tugas --}}
            <section class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Tugas saya</h2>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Pilih tugas untuk melihat informasi lengkap dan memperbarui progres.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center gap-1.5 rounded border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        <rect x="9" y="2" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ $totalActiveCount }} tugas aktif
                </span>
            </section>

            {{-- Daftar tugas --}}
            <section class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                {{-- Filter --}}
                <form method="GET" action="{{ route('technician.dashboard') }}" class="border-b-[1.6px] border-slate-100 p-4">
                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(280px,1.25fr)_minmax(220px,0.9fr)_minmax(220px,0.85fr)_auto] lg:items-end">

                        {{-- Pencarian --}}
                        <div>
                            <label for="technicianTaskSearch" class="mb-2 block text-xs font-medium text-slate-500">
                                Pencarian
                            </label>

                            <div class="relative">
                                <svg class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                    <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                </svg>

                                <input id="technicianTaskSearch" type="search" name="search" value="{{ $search }}"
                                    placeholder="Cari nomor seri, merek, atau model"
                                    class="block w-full rounded border border-slate-300 bg-white p-2.5 ps-9 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Status pekerjaan --}}
                        <div>
                            <label for="technicianTaskStatus" class="mb-2 block text-xs font-medium text-slate-500">
                                Status pekerjaan
                            </label>

                            <select id="technicianTaskStatus" name="status" onchange="this.form.submit()"
                                class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="" {{ $selectedStatus === '' ? 'selected' : '' }}>Semua tugas aktif</option>
                                @foreach (\App\Enums\RepairStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $selectedStatus === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Urutan --}}
                        <div>
                            <label for="technicianTaskOrder" class="mb-2 block text-xs font-medium text-slate-500">
                                Urutan
                            </label>

                            <select id="technicianTaskOrder" name="sort" onchange="this.form.submit()"
                                class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                                <option value="updated" {{ $selectedSort === 'updated' ? 'selected' : '' }}>Pembaruan terakhir</option>
                                <option value="newest" {{ $selectedSort === 'newest' ? 'selected' : '' }}>Tugas terbaru</option>
                                <option value="oldest" {{ $selectedSort === 'oldest' ? 'selected' : '' }}>Tugas terlama</option>
                            </select>
                        </div>

                        {{-- Hapus filter --}}
                        <a href="{{ route('technician.dashboard') }}"
                            class="inline-flex h-[42px] items-center justify-center gap-1.5 rounded px-3 text-sm font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path d="M3 6h18" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                <path d="M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                <path d="M10 11v5M14 11v5" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                            </svg>
                            Hapus filter
                        </a>
                    </div>

                    <button type="submit" class="sr-only">Terapkan filter</button>
                </form>

                {{-- Card tugas --}}
                @if ($repairs->isEmpty())
                    <div class="p-6 text-center text-sm text-slate-500">
                        Tidak ada tugas yang cocok dengan filter ini.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-3 p-4 lg:grid-cols-2">
                        @foreach ($repairs as $repair)
                            @php
                                $style = $statusStyle[$repair->status->color()];
                                $assignedAt = $repair->activeAssignment?->assigned_at ?? $repair->created_at;
                            @endphp

                            <article class="flex h-full flex-col rounded border border-slate-200 bg-white p-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded border {{ $style[1] }} {{ $style[0] }} px-2.5 py-1 text-xs font-medium {{ $style[2] }}">
                                        {{ $repair->status->label() }}
                                    </span>
                                </div>

                                <h3 class="mt-3 text-base font-bold text-slate-900">
                                    {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                </h3>

                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="2" />
                                            <path d="M9 7h6M9 11h6M9 15h3" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                        </svg>
                                        {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                                    </span>

                                    <span aria-hidden="true">•</span>

                                    <span>No. seri: {{ $repair->freezer->serial_number }}</span>
                                </div>

                                <p class="mt-3 flex items-start gap-2 text-sm text-slate-600">
                                    <svg class="mt-0.5 size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Keluhan awal: {{ $repair->initial_analysis ?? 'Belum ada catatan.' }}
                                </p>

                                <div class="mt-auto flex flex-col gap-3 pt-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Ditugaskan {{ $assignedAt->diffForHumans() }}
                                    </p>

                                    <a href="{{ route('technician.repairs.show', $repair->id) }}"
                                        class="inline-flex w-full items-center justify-center rounded bg-blue-700 px-3 py-2 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
                                        {{ $buttonLabel[$repair->status->value] }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                {{-- Pagination --}}
                @if ($repairs->hasPages() || $repairs->total() > 0)
                    <nav class="flex flex-col items-center gap-3 border-t border-slate-200 bg-white px-4 py-3 sm:px-5 xl:flex-row xl:justify-between"
                        aria-label="Pagination tugas teknisi">

                        <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:text-left">
                            <form method="GET" action="{{ route('technician.dashboard') }}">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <input type="hidden" name="status" value="{{ $selectedStatus }}">
                                <input type="hidden" name="sort" value="{{ $selectedSort }}">

                                <label>
                                    <span class="sr-only">Jumlah tugas per halaman</span>
                                    <select name="per_page" onchange="this.form.submit()"
                                        class="rounded border border-slate-300 bg-white p-2.5 pe-9 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="8" {{ $perPage === 8 ? 'selected' : '' }}>8 per halaman</option>
                                        <option value="12" {{ $perPage === 12 ? 'selected' : '' }}>12 per halaman</option>
                                        <option value="24" {{ $perPage === 24 ? 'selected' : '' }}>24 per halaman</option>
                                    </select>
                                </label>
                            </form>

                            <p class="text-sm text-slate-500">
                                Menampilkan
                                <span class="font-semibold text-slate-900">{{ $repairs->firstItem() ?? 0 }}–{{ $repairs->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-semibold text-slate-900">{{ $repairs->total() }}</span>
                            </p>
                        </div>

                        <ul class="flex flex-wrap items-center justify-center gap-1.5 text-sm">
                            <li>
                                @if ($repairs->onFirstPage())
                                    <button type="button" disabled
                                        class="inline-flex cursor-not-allowed items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-400">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Sebelumnya
                                    </button>
                                @else
                                    <a href="{{ $repairs->previousPageUrl() }}"
                                        class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-100">
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Sebelumnya
                                    </a>
                                @endif
                            </li>

                            @for ($page = max(1, $repairs->currentPage() - 2); $page <= min($repairs->lastPage(), $repairs->currentPage() + 2); $page++)
                                <li>
                                    @if ($page === $repairs->currentPage())
                                        <span aria-current="page"
                                            class="flex size-9 items-center justify-center rounded bg-blue-700 font-medium text-white">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $repairs->url($page) }}"
                                            class="flex size-9 items-center justify-center rounded border border-slate-200 bg-white font-medium text-slate-600 hover:bg-slate-100">
                                            {{ $page }}
                                        </a>
                                    @endif
                                </li>
                            @endfor

                            <li>
                                @if ($repairs->hasMorePages())
                                    <a href="{{ $repairs->nextPageUrl() }}"
                                        class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-100">
                                        Berikutnya
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </a>
                                @else
                                    <button type="button" disabled
                                        class="inline-flex cursor-not-allowed items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-400">
                                        Berikutnya
                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </button>
                                @endif
                            </li>
                        </ul>
                    </nav>
                @endif
            </section>
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
</body>

</html>