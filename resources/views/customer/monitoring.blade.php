<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitoring Pelanggan</title>

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

    <main class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-6">

        {{--
            SLICING ONLY

            Angka ringkasan masih berupa data statis.
            Belum membaca freezer atau reparasi milik pelanggan dari database.
        --}}

        {{-- Sapaan pelanggan --}}
        <section aria-labelledby="customerWelcomeTitle">

            <h1 id="customerWelcomeTitle" class="text-xl font-bold tracking-tight text-slate-950 sm:text-2xl">
                Selamat datang, {{ auth()->user()->name }}
            </h1>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Lihat perkembangan terbaru reparasi freezer Anda.
            </p>
        </section>

        {{-- Ringkasan monitoring --}}
        <section aria-label="Ringkasan monitoring freezer" class="mt-5 grid gap-4 md:grid-cols-3">

            {{-- Total freezer --}}
            <article
                class="flex min-w-0 items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div
                    class="flex size-14 shrink-0 items-center justify-center rounded border border-blue-200 bg-blue-50 text-blue-700">

                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor"
                            stroke-width="2" />

                        <path d="M5 11h14M15 7h.01M15 15h.01" stroke="currentColor" stroke-linecap="round"
                            stroke-width="2" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Total freezer
                    </h2>

                    <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                        {{ $totalFreezerCount }}
                    </p>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Freezer milik perusahaan Anda
                    </p>
                </div>
            </article>

            {{-- Sedang diproses --}}
            <article
                class="flex min-w-0 items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div
                    class="flex size-14 shrink-0 items-center justify-center rounded border border-amber-200 bg-amber-50 text-amber-600">

                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Sedang diproses
                    </h2>

                    <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                       {{ $activeRepairCount }}
                    </p>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Masih dalam proses reparasi
                    </p>
                </div>
            </article>

            {{-- Perbaikan selesai --}}
            <article
                class="flex min-w-0 items-center gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div
                    class="flex size-14 shrink-0 items-center justify-center rounded border border-emerald-200 bg-emerald-50 text-emerald-600">

                    <svg class="size-7" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Perbaikan selesai
                    </h2>

                    <p class="mt-1 text-2xl font-bold leading-none text-slate-950">
                        {{ $completedRepairCount }}
                    </p>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Sudah selesai diperbaiki
                    </p>
                </div>
            </article>
        </section>

        {{-- Daftar freezer pelanggan --}}
        <section aria-labelledby="customerFreezersTitle" class="mt-7">

            <div>
                <h2 id="customerFreezersTitle" class="text-xl font-bold tracking-tight text-slate-950">
                    Freezer Anda
                </h2>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Pilih freezer untuk melihat riwayat dan perkembangan reparasi.
                </p>
            </div>

            @if ($freezers->isEmpty())
            <div class="mt-4 rounded border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500">
                Belum ada freezer yang terdaftar untuk perusahaan Anda.
            </div>
            @else
            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                @php
                    $statusStyle = [
                        'gray' => ['bg-slate-50', 'border-slate-200', 'text-slate-700', 'bg-slate-400'],
                        'blue' => ['bg-blue-50', 'border-blue-200', 'text-blue-700', 'bg-blue-600'],
                        'yellow' => ['bg-amber-50', 'border-amber-200', 'text-amber-700', 'bg-amber-500'],
                        'green' => ['bg-emerald-50', 'border-emerald-200', 'text-emerald-700', 'bg-emerald-600'],
                    ];
                @endphp

                @foreach ($freezers as $freezer)
                    @php
                        $repair = $freezer->latestRepair;
                        $style = $statusStyle[$repair?->status?->color() ?? 'gray'];
                    @endphp

                    <article class="flex min-w-0 flex-col rounded border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded border {{ $style[1] }} {{ $style[0] }} px-2.5 py-1 text-xs font-medium {{ $style[2] }}">
                                <span class="size-2 rounded-full {{ $style[3] }}"></span>
                                {{ $repair?->status?->label() ?? 'Belum ada reparasi' }}
                            </span>

                            <h3 class="mt-3 text-lg font-bold text-slate-950">
                                {{ $freezer->brand }} {{ $freezer->model }}
                            </h3>

                            <p class="mt-0.5 text-sm font-medium text-slate-500">
                                {{ $freezer->brand }} {{ $freezer->model }}
                            </p>

                            <p class="mt-2 text-xs text-slate-500">
                                Nomor seri:
                                <span class="font-medium text-slate-600">
                                    {{ $freezer->serial_number }}
                                </span>
                            </p>
                        </div>

                        <div class="mt-4 border-t-[1.6px] border-slate-100 pt-4">
                            <p class="text-sm leading-6 text-slate-700">
                                {{ $repair?->latestLog?->description ?? 'Belum ada catatan perkembangan.' }}
                            </p>

                            @if ($repair?->latestLog)
                                <p class="mt-3 inline-flex items-center gap-1.5 text-xs text-slate-500">
                                    <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Diperbarui {{ $repair->latestLog->created_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        @if ($repair)
                            <a href="{{ route('customer.repairs.show', $repair->id) }}"
                                class="mt-4 inline-flex w-full items-center justify-center rounded border border-blue-600 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                Lihat perkembangan
                            </a>
                        @else
                            <button type="button" disabled
                                class="mt-4 inline-flex w-full cursor-not-allowed items-center justify-center rounded border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-400">
                                Belum ada reparasi
                            </button>
                        @endif
                    </article>
                @endforeach
            </div>
            @endif
        </section>

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
