<header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Manajemen Pengguna
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola akun Teknisi dan Pelanggan yang menggunakan sistem.
        </p>
    </div>

    <button type="button" data-modal-target="createUserModal" data-modal-toggle="createUserModal"
        class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />

            <path d="M2 21a7 7 0 0 1 14 0M19 8v6m3-3h-6" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" />
        </svg>

        Tambah pengguna
    </button>
</header>

<section aria-label="Ringkasan pengguna" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Total pengguna --}}
    <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">

                <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" />

                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />

                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-600">
                    Total pengguna
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ $totalUserCount }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-[11px] leading-4 text-slate-500">
            Seluruh akun terdaftar
        </p>
    </article>

    {{-- Teknisi --}}
    <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded border border-emerald-100 bg-emerald-50 text-emerald-600">

                <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path
                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77z"
                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-600">
                    Teknisi
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ $technicianCount }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-[11px] leading-4 text-slate-500">
            Akun teknisi terdaftar
        </p>
    </article>

    {{-- Pelanggan --}}
    <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded border border-amber-100 bg-amber-50 text-amber-600">

                <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="M3 21h18M6 21V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v17M16 9h3a1 1 0 0 1 1 1v11"
                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />

                    <path d="M9 7h1M9 11h1M9 15h1M13 7h1M13 11h1M13 15h1" stroke="currentColor" stroke-linecap="round"
                        stroke-width="2" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-600">
                    Pelanggan
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ $customerCount }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-[11px] leading-4 text-slate-500">
            Akun pelanggan terdaftar
        </p>
    </article>

    {{-- Akun nonaktif --}}
    <article class="rounded border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3 border-b-[1.6px] border-slate-100 pb-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded border border-rose-100 bg-rose-50 text-rose-600">

                <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />

                    <path d="M2 21a7 7 0 0 1 11.5-5.4M16 16l5 5M21 16l-5 5" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-600">
                    Akun nonaktif
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ $inactiveUserCount }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-[11px] leading-4 text-slate-500">
            Akses sedang dinonaktifkan
        </p>
    </article>
</section>
