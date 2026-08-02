<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Reparasi Pelanggan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">

            {{-- Brand --}}
            <a href="{{ route('customer.monitoring') }}" class="flex min-w-0 items-center gap-3">

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

            {{-- Profil pelanggan --}}
            <div class="relative">
                <button id="customerProfileButton" type="button" data-dropdown-toggle="customerProfileDropdown"
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

                <div id="customerProfileDropdown"
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

        {{--
            SLICING ONLY

            Halaman detail reparasi pelanggan masih menggunakan data statis.
            Belum membaca parameter repair, database, atau ownership pelanggan.
            Seluruh informasi bersifat read-only.
        --}}

        {{-- Navigasi dan judul --}}
        <header>
            <a href="{{ route('customer.monitoring') }}"
                class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">

                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>

                Kembali ke monitoring
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Detail reparasi
            </h1>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Pantau status, perkembangan, dan hasil reparasi freezer Anda.
            </p>
        </header>

        <div class="mt-4 grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(300px,0.36fr)]">

            {{-- Kolom utama --}}
            <div class="min-w-0 space-y-4">

                {{-- Ringkasan reparasi --}}
                <section aria-labelledby="repairSummaryTitle"
                    class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                    {{-- Header ringkasan --}}
                    <div
                        class="flex flex-col gap-3 border-b-[1.6px] border-slate-100 px-4 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-5">

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">

                                <span
                                    class="inline-flex items-center gap-1.5 rounded border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">

                                    <span class="size-2 rounded-full bg-blue-600"></span>

                                    Sedang diperbaiki
                                </span>

                                <span class="text-xs text-slate-400">
                                    Reparasi aktif
                                </span>
                            </div>

                            <h2 id="repairSummaryTitle" class="mt-3 text-xl font-bold tracking-tight text-slate-950">
                                Freezer FZ-00124
                            </h2>

                            <p class="mt-1 text-sm font-medium text-slate-500">
                                Polytron SCN-200
                            </p>
                        </div>

                        <div class="shrink-0 sm:text-right">
                            <p class="text-sm font-medium text-slate-400">
                                Terakhir diperbarui
                            </p>

                            <p class="mt-1 inline-flex items-center gap-1.5 text-sm font-medium text-slate-700">
                                <svg class="size-4 shrink-0 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>

                                Hari ini, pukul 10.30
                            </p>
                        </div>
                    </div>

                    {{-- Informasi ringkas --}}
                    <div class="grid sm:grid-cols-2">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:border-r-[1.6px] sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Nomor seri
                            </p>

                            <p class="mt-1.5 text-sm font-semibold text-slate-800">
                                PLT-SCN-200-0924
                            </p>
                        </div>

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Status unit
                            </p>

                            <p class="mt-1.5 text-sm font-semibold text-slate-800">
                                Dalam proses reparasi
                            </p>
                        </div>

                        <div class="px-4 py-4 sm:col-span-2 sm:px-5">
                            <div class="flex items-start gap-3">

                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-500">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-linecap="round"
                                            stroke-width="2" />

                                        <path
                                            d="M5 4h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-8l-4 3v-3H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-400">
                                        Keluhan awal
                                    </p>

                                    <p class="mt-1.5 text-sm leading-6 text-slate-700">
                                        Freezer tidak dingin meskipun mesin tetap menyala.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Perkembangan reparasi --}}
                <section aria-labelledby="repairProgressTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                        <h2 id="repairProgressTitle" class="text-base font-bold text-slate-950">
                            Perkembangan reparasi
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Tahapan penanganan freezer Anda dari awal hingga selesai.
                        </p>
                    </div>

                    <div class="px-4 py-5 sm:px-5">
                        <ol class="relative ms-4 border-s-[1.6px] border-slate-200">

                            {{-- Unit diterima --}}
                            <li class="relative ms-6 pb-7">
                                <span
                                    class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full bg-emerald-100 ring-4 ring-white">

                                    <svg class="size-3.5 text-emerald-700" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2.5" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Unit diterima
                                        </h3>

                                        <span
                                            class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                            Selesai
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Freezer telah diterima dan masuk ke antrean pemeriksaan.
                                    </p>

                                    <p class="mt-1.5 text-xs text-slate-400">
                                        25 Juli 2026, pukul 08.15
                                    </p>
                                </div>
                            </li>

                            {{-- Pemeriksaan selesai --}}
                            <li class="relative ms-6 pb-7">
                                <span
                                    class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full bg-emerald-100 ring-4 ring-white">

                                    <svg class="size-3.5 text-emerald-700" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2.5" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Pemeriksaan selesai
                                        </h3>

                                        <span
                                            class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                            Selesai
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Kerusakan ditemukan pada kompresor dan perlu dilakukan penggantian.
                                    </p>

                                    <p class="mt-1.5 text-xs text-slate-400">
                                        25 Juli 2026, pukul 11.40
                                    </p>
                                </div>
                            </li>

                            {{-- Sedang diperbaiki --}}
                            <li class="relative ms-6 pb-7">
                                <span
                                    class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full bg-blue-100 ring-4 ring-white">

                                    <svg class="size-3.5 text-blue-700" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Sedang diperbaiki
                                        </h3>

                                        <span class="rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">
                                            Sedang berlangsung
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Teknisi sedang melakukan penggantian kompresor dan pengujian sistem pendingin.
                                    </p>

                                    <p class="mt-1.5 text-xs text-slate-400">
                                        Diperbarui hari ini, pukul 10.30
                                    </p>
                                </div>
                            </li>

                            {{-- Perbaikan selesai --}}
                            <li class="relative ms-6">
                                <span
                                    class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full border-2 border-slate-300 bg-white ring-4 ring-white">

                                    <span class="size-2 rounded-full bg-slate-300"></span>
                                </span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-slate-500">
                                            Perbaikan selesai
                                        </h3>

                                        <span
                                            class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                                            Belum selesai
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-400">
                                        Unit akan melalui pemeriksaan akhir sebelum dinyatakan selesai.
                                    </p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </section>

                {{-- Hasil pemeriksaan teknisi --}}
                <section aria-labelledby="inspectionResultTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                        <div class="flex items-start gap-3">

                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded bg-amber-50 text-amber-600">

                                <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path
                                        d="M9 3h6l1 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l1-2Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />

                                    <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="2" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h2 id="inspectionResultTitle" class="text-base font-bold text-slate-950">
                                    Hasil pemeriksaan teknisi
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Temuan dan kebutuhan perbaikan berdasarkan pemeriksaan unit.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y-[1.6px] divide-slate-100">

                        {{-- Ringkasan temuan --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Ringkasan temuan
                            </p>

                            <p class="mt-2 text-sm leading-6 text-slate-700">
                                Kompresor mengalami penurunan tekanan sehingga proses pendinginan tidak bekerja secara
                                optimal. Kondisi kelistrikan dan thermostat masih berfungsi normal.
                            </p>
                        </div>

                        {{-- Tindakan yang diperlukan --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Tindakan yang diperlukan
                            </p>

                            <div class="mt-2 flex items-start gap-3">

                                <div
                                    class="flex size-8 shrink-0 items-center justify-center rounded bg-blue-50 text-blue-700">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path
                                            d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>
                                </div>

                                <p class="min-w-0 text-sm leading-6 text-slate-700">
                                    Mengganti kompresor, melakukan vakum sistem, mengisi ulang refrigeran, dan menguji
                                    kestabilan suhu freezer.
                                </p>
                            </div>
                        </div>

                        {{-- Komponen yang diperlukan --}}
                        <div class="px-4 py-4 sm:px-5">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-400">
                                        Komponen yang diperlukan
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Komponen yang digunakan dalam proses perbaikan.
                                    </p>
                                </div>

                                <span
                                    class="mt-2 inline-flex w-fit items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 sm:mt-0">

                                    <span class="size-2 rounded-full bg-amber-500"></span>

                                    Dibutuhkan
                                </span>
                            </div>

                            <div
                                class="mt-3 flex flex-col gap-3 rounded border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center sm:justify-between">

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">
                                        Kompresor 1/4 PK
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Jumlah: 1 unit
                                    </p>
                                </div>

                                <p class="shrink-0 text-xs font-medium text-slate-500">
                                    Pengganti kompresor lama
                                </p>
                            </div>
                        </div>

                        {{-- Foto pemeriksaan --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Foto pemeriksaan
                            </p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">

                                <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">

                                    <div class="flex aspect-video items-center justify-center text-slate-400">

                                        <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <rect x="3" y="5" width="18" height="14" rx="2"
                                                stroke="currentColor" stroke-width="2" />

                                            <circle cx="9" cy="10" r="2" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="m21 15-5-5L5 19" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <figcaption
                                        class="border-t-[1.6px] border-slate-200 bg-white px-3 py-2 text-xs text-slate-500">
                                        Kondisi kompresor sebelum penggantian
                                    </figcaption>
                                </figure>

                                <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">

                                    <div class="flex aspect-video items-center justify-center text-slate-400">

                                        <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <rect x="3" y="5" width="18" height="14" rx="2"
                                                stroke="currentColor" stroke-width="2" />

                                            <circle cx="9" cy="10" r="2" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="m21 15-5-5L5 19" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <figcaption
                                        class="border-t-[1.6px] border-slate-200 bg-white px-3 py-2 text-xs text-slate-500">
                                        Pemeriksaan jalur sistem pendingin
                                    </figcaption>
                                </figure>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-slate-400">
                                Foto masih berupa placeholder untuk kebutuhan slicing.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Progres perbaikan --}}
                <section aria-labelledby="repairWorkProgressTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                        <div class="flex items-start gap-3">

                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded bg-blue-50 text-blue-700">

                                <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path
                                        d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 id="repairWorkProgressTitle" class="text-base font-bold text-slate-950">
                                        Progres perbaikan
                                    </h2>

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">

                                        <span class="size-1.5 rounded-full bg-blue-600"></span>

                                        Sedang berlangsung
                                    </span>
                                </div>

                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Informasi pekerjaan yang sedang dilakukan oleh teknisi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y-[1.6px] divide-slate-100">

                        {{-- Pekerjaan yang sedang dilakukan --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Pekerjaan yang sedang dilakukan
                            </p>

                            <div class="mt-3 rounded border border-blue-100 bg-blue-50/60 p-3">
                                <div class="flex items-start gap-3">

                                    <div
                                        class="flex size-8 shrink-0 items-center justify-center rounded bg-white text-blue-700 shadow-sm">

                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900">
                                            Penggantian kompresor
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-slate-600">
                                            Kompresor lama telah dilepas dan teknisi sedang memasang kompresor
                                            pengganti.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 rounded border border-slate-200 p-3">
                                <div class="flex items-start gap-3">

                                    <div
                                        class="flex size-8 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-600">

                                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <path d="M4 14a8 8 0 1 1 16 0" stroke="currentColor"
                                                stroke-linecap="round" stroke-width="2" />

                                            <path d="m12 14 4-4M6 18h12" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900">
                                            Pengujian sistem pendingin
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-slate-600">
                                            Setelah pemasangan selesai, tekanan refrigeran dan kestabilan suhu akan
                                            diuji.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Komponen terpasang --}}
                        <div class="px-4 py-4 sm:px-5">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-400">
                                        Komponen terpasang
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Komponen pengganti yang digunakan pada unit.
                                    </p>
                                </div>

                                <span
                                    class="mt-2 inline-flex w-fit items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 sm:mt-0">

                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2.5" />
                                    </svg>

                                    Terpasang
                                </span>
                            </div>

                            <div
                                class="mt-3 flex flex-col gap-3 rounded border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center sm:justify-between">

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">
                                        Kompresor 1/4 PK
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Jumlah: 1 unit
                                    </p>
                                </div>

                                <p class="shrink-0 text-xs font-medium text-slate-500">
                                    Dipasang hari ini
                                </p>
                            </div>
                        </div>

                        {{-- Foto progres --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Foto progres
                            </p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">

                                <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">
                                    <div class="flex aspect-video items-center justify-center text-slate-400">

                                        <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <rect x="3" y="5" width="18" height="14" rx="2"
                                                stroke="currentColor" stroke-width="2" />

                                            <circle cx="9" cy="10" r="2" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="m21 15-5-5L5 19" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <figcaption
                                        class="border-t-[1.6px] border-slate-200 bg-white px-3 py-2 text-xs text-slate-500">
                                        Proses pemasangan kompresor baru
                                    </figcaption>
                                </figure>

                                <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">
                                    <div class="flex aspect-video items-center justify-center text-slate-400">

                                        <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                            <rect x="3" y="5" width="18" height="14" rx="2"
                                                stroke="currentColor" stroke-width="2" />

                                            <circle cx="9" cy="10" r="2" stroke="currentColor"
                                                stroke-width="2" />

                                            <path d="m21 15-5-5L5 19" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <figcaption
                                        class="border-t-[1.6px] border-slate-200 bg-white px-3 py-2 text-xs text-slate-500">
                                        Persiapan pengujian sistem pendingin
                                    </figcaption>
                                </figure>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-slate-400">
                                Foto masih berupa placeholder untuk kebutuhan slicing.
                            </p>
                        </div>

                        {{-- Catatan teknisi --}}
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Catatan teknisi
                            </p>

                            <div class="mt-3 flex items-start gap-3 rounded border border-slate-200 bg-slate-50 p-3">

                                <div
                                    class="flex size-8 shrink-0 items-center justify-center rounded bg-white text-slate-500 shadow-sm">

                                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-linecap="round"
                                            stroke-width="2" />

                                        <path
                                            d="M5 4h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-8l-4 3v-3H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm leading-6 text-slate-700">
                                        Suhu unit akan dipantau setelah pemasangan selesai untuk memastikan sistem
                                        pendingin bekerja stabil sebelum pemeriksaan akhir.
                                    </p>

                                    <p class="mt-2 text-xs text-slate-400">
                                        Diperbarui hari ini, pukul 10.30
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            {{-- Informasi pendukung --}}
            <aside class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-1">

                {{-- Informasi freezer --}}
                <section aria-labelledby="freezerInformationTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4">
                        <h2 id="freezerInformationTitle" class="text-base font-bold text-slate-950">
                            Informasi freezer
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Identitas unit yang sedang ditangani.
                        </p>
                    </div>

                    <dl class="divide-y-[1.6px] divide-slate-100">

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">
                                Kode freezer
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                FZ-00124
                            </dd>
                        </div>

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">
                                Merek dan model
                            </dt>

                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                Polytron SCN-200
                            </dd>
                        </div>

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">
                                Nomor seri
                            </dt>

                            <dd class="mt-1.5 break-words text-sm font-semibold text-slate-900">
                                PLT-SCN-200-0924
                            </dd>
                        </div>

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">
                                Lokasi unit
                            </dt>

                            <dd class="mt-1.5 text-sm leading-6 text-slate-700">
                                Gudang utama, PT Magnum Indonesia
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Informasi penugasan --}}
                <section aria-labelledby="assignmentInformationTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4">
                        <h2 id="assignmentInformationTitle" class="text-base font-bold text-slate-950">
                            Informasi penugasan
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Informasi teknisi dan jadwal penanganan.
                        </p>
                    </div>

                    <div class="divide-y-[1.6px] divide-slate-100">

                        <div class="px-4 py-4">
                            <p class="text-sm font-medium text-slate-400">
                                Teknisi
                            </p>

                            <div class="mt-2 flex items-center gap-3">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">
                                    BS
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        Budi Santoso
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Teknisi freezer
                                    </p>
                                </div>
                            </div>
                        </div>

                        <dl class="divide-y-[1.6px] divide-slate-100">

                            <div class="px-4 py-3.5">
                                <dt class="text-sm font-medium text-slate-400">
                                    Tanggal diterima
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    25 Juli 2026, pukul 08.15
                                </dd>
                            </div>

                            <div class="px-4 py-3.5">
                                <dt class="text-sm font-medium text-slate-400">
                                    Target penyelesaian
                                </dt>

                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    28 Juli 2026
                                </dd>
                            </div>

                            <div class="px-4 py-3.5">
                                <dt class="text-sm font-medium text-slate-400">
                                    Status penugasan
                                </dt>

                                <dd class="mt-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">

                                        <span class="size-2 rounded-full bg-blue-600"></span>

                                        Sedang ditangani
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </section>

                {{-- Bantuan --}}
                <section aria-labelledby="customerHelpTitle"
                    class="rounded border border-slate-200 bg-white p-4 shadow-sm md:col-span-2 xl:col-span-1">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-600">

                            <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                    stroke-width="2" />

                                <path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-.9.8-1.7 1.2-1.7 2.7" stroke="currentColor"
                                    stroke-linecap="round" stroke-width="2" />

                                <path d="M12 17h.01" stroke="currentColor" stroke-linecap="round"
                                    stroke-width="2.5" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <h2 id="customerHelpTitle" class="text-sm font-bold text-slate-950">
                                Butuh bantuan?
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Hubungi admin Karyatama bila ada pertanyaan terkait proses reparasi atau informasi unit.
                            </p>
                        </div>
                    </div>
                </section>

            </aside>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="mt-8 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-center px-4 py-4 text-center sm:px-6">
            <p class="inline-flex items-center gap-1 text-xs text-slate-500">
                <span aria-hidden="true">©</span>
                CV. Karyatama Agung Abadi
            </p>
        </div>
    </footer>

</body>

</html>
