<header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Manajemen Freezer
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola data freezer dan pantau status reparasinya.
        </p>
    </div>

    <button type="button" data-modal-target="createFreezerModal" data-modal-toggle="createFreezerModal"
        class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">

        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
        </svg>

        Tambah freezer
    </button>
</header>

<section aria-label="Ringkasan data freezer" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

    {{-- Total freezer --}}
    <article class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-blue-600 shadow-sm">

                <svg class="size-7" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="2" x2="22" y1="12" y2="12" />
                    <line x1="12" x2="12" y1="2" y2="22" />
                    <path d="m20 16-4-4 4-4" />
                    <path d="m4 8 4 4-4 4" />
                    <path d="m16 4-4 4-4-4" />
                    <path d="m8 20 4-4 4 4" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-700">
                    Total freezer
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ number_format($summary['total']) }}
                </p>

                <p class="mt-2 text-[11px] leading-4 text-slate-500">
                    Seluruh unit terdaftar
                </p>
            </div>
        </div>

        <div class="mt-3 border-t border-slate-100 pt-3">
            <p class="flex items-center gap-2 text-[11px] font-medium leading-4 text-slate-500">
                <svg class="size-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M7 17 17 7" />
                    <path d="M7 7h10v10" />
                </svg>

                +{{ number_format($summary['added_this_month']) }} bulan ini
            </p>
        </div>
    </article>

    {{-- Menunggu diperiksa --}}
    <article class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-amber-100 bg-amber-50 text-amber-500 shadow-sm">

                <svg class="size-7" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-700">
                    Menunggu diperiksa
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ number_format($summary['queued']) }}
                </p>

                <p class="mt-2 text-[11px] leading-4 text-slate-500">
                    Tugas belum diperiksa
                </p>
            </div>
        </div>

        <div class="mt-3 border-t border-slate-100 pt-3">
            <p class="flex items-center gap-2 text-[11px] font-medium leading-4 text-slate-500">
                <span class="size-1.5 shrink-0 rounded-full bg-amber-500" aria-hidden="true"></span>

                {{ number_format($summary['queued_today']) }} tugas baru hari ini
            </p>
        </div>
    </article>

    {{-- Sedang diperbaiki --}}
    <article class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-blue-600 shadow-sm">

                <svg class="size-7" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path
                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-700">
                    Sedang diperbaiki
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ number_format($summary['repairing']) }}
                </p>

                <p class="mt-2 text-[11px] leading-4 text-slate-500">
                    Perbaikan masih berlangsung
                </p>
            </div>
        </div>

        <div class="mt-3 border-t border-slate-100 pt-3">
            <p class="flex items-center gap-2 text-[11px] font-medium leading-4 text-slate-500">
                <span class="size-1.5 shrink-0 rounded-full bg-blue-600" aria-hidden="true"></span>

                {{ number_format($summary['repairing_updated_today']) }}
                diperbarui hari ini
            </p>
        </div>
    </article>

    {{-- Perbaikan selesai --}}
    <article class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
        <div class="flex items-start gap-3">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-green-100 bg-green-50 text-green-600 shadow-sm">

                <svg class="size-7" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" />
                    <path d="m8 12 2.7 2.7L16.5 9" />
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-medium leading-4 text-slate-700">
                    Perbaikan selesai
                </p>

                <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                    {{ number_format($summary['completed_this_month']) }}
                </p>

                <p class="mt-2 text-[11px] leading-4 text-slate-500">
                    Selesai bulan ini
                </p>
            </div>
        </div>

        <div class="mt-3 border-t border-slate-100 pt-3">
            <p class="flex items-center gap-2 text-[11px] font-medium leading-4 text-slate-500">
                <span class="size-1.5 shrink-0 rounded-full bg-green-600" aria-hidden="true"></span>

                {{ number_format($summary['completed_this_week']) }}
                selesai minggu ini
            </p>
        </div>
    </article>
</section>
