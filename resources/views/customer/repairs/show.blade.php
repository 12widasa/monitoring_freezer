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

    @php
        $statusStyle = [
            'gray' => ['bg-slate-50', 'border-slate-200', 'text-slate-700', 'bg-slate-400'],
            'blue' => ['bg-blue-50', 'border-blue-200', 'text-blue-700', 'bg-blue-600'],
            'yellow' => ['bg-amber-50', 'border-amber-200', 'text-amber-700', 'bg-amber-500'],
            'green' => ['bg-emerald-50', 'border-emerald-200', 'text-emerald-700', 'bg-emerald-600'],
        ];

        $style = $statusStyle[$repair->status->color()];

        $stages = \App\Enums\RepairStatus::cases();
        $currentIndex = array_search($repair->status, $stages, true);

        $logsByStatus = $repair->logs->groupBy(fn($log) => $log->status->value);

        $inspectingLog = $logsByStatus->get(\App\Enums\RepairStatus::INSPECTING->value)?->first();
        $repairingLog = $logsByStatus->get(\App\Enums\RepairStatus::REPAIRING->value)?->first();
        $lastLog = $repair->logs->last();

        $requestedComponents = $repair->components->where('status', '!=', 'installed');
        $installedComponents = $repair->components->where('status', 'installed');
    @endphp

    <main class="mx-auto max-w-7xl px-4 py-4 sm:px-6">

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

                    <div
                        class="flex flex-col gap-3 border-b-[1.6px] border-slate-100 px-4 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-5">

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded border {{ $style[1] }} {{ $style[0] }} px-2.5 py-1 text-xs font-semibold {{ $style[2] }}">
                                    <span class="size-2 rounded-full {{ $style[3] }}"></span>
                                    {{ $repair->status->label() }}
                                </span>

                                <span class="text-xs text-slate-400">
                                    {{ $repair->status === \App\Enums\RepairStatus::COMPLETED ? 'Reparasi selesai' : 'Reparasi aktif' }}
                                </span>
                            </div>

                            <h2 id="repairSummaryTitle" class="mt-3 text-xl font-bold tracking-tight text-slate-950">
                                {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                            </h2>

                            <p class="mt-1 text-sm font-medium text-slate-500">
                                {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                            </p>
                        </div>

                        <div class="shrink-0 sm:text-right">
                            <p class="text-sm font-medium text-slate-400">
                                Terakhir diperbarui
                            </p>

                            <p class="mt-1 inline-flex items-center gap-1.5 text-sm font-medium text-slate-700">
                                <svg class="size-4 shrink-0 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                {{ $lastLog?->created_at?->diffForHumans() ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2">
                        <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:border-r-[1.6px] sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Nomor seri
                            </p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-800">
                                {{ $repair->freezer->serial_number }}
                            </p>
                        </div>

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">
                                Status unit
                            </p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-800">
                                {{ $repair->status->label() }}
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
                                        {{ $repair->initial_analysis ?? 'Belum ada catatan keluhan awal.' }}
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
                            @foreach ($stages as $index => $stage)
                                @php
                                    $stageLog = $logsByStatus->get($stage->value)?->first();
                                    $isDone = $index < $currentIndex || $repair->status === \App\Enums\RepairStatus::COMPLETED && $index <= $currentIndex;
                                    $isCurrent = $index === $currentIndex && $repair->status !== \App\Enums\RepairStatus::COMPLETED;
                                @endphp

                                <li class="relative ms-6 {{ !$loop->last ? 'pb-7' : '' }}">
                                    @if ($isDone)
                                        <span class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full bg-emerald-100 ring-4 ring-white">
                                            <svg class="size-3.5 text-emerald-700" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                            </svg>
                                        </span>
                                    @elseif ($isCurrent)
                                        <span class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full bg-blue-100 ring-4 ring-white">
                                            <svg class="size-3.5 text-blue-700" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="absolute -start-[2.15rem] flex size-6 items-center justify-center rounded-full border-2 border-slate-300 bg-white ring-4 ring-white">
                                            <span class="size-2 rounded-full bg-slate-300"></span>
                                        </span>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-semibold {{ $isDone || $isCurrent ? 'text-slate-900' : 'text-slate-500' }}">
                                                {{ $stage->label() }}
                                            </h3>

                                            @if ($isDone)
                                                <span class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Selesai</span>
                                            @elseif ($isCurrent)
                                                <span class="rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Sedang berlangsung</span>
                                            @else
                                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Belum selesai</span>
                                            @endif
                                        </div>

                                        <p class="mt-1 text-sm leading-6 {{ $isDone || $isCurrent ? 'text-slate-600' : 'text-slate-400' }}">
                                            {{ $stageLog?->description ?? 'Belum ada catatan untuk tahap ini.' }}
                                        </p>

                                        @if ($stageLog)
                                            <p class="mt-1.5 text-xs text-slate-400">
                                                {{ $stageLog->created_at->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                            </p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </section>

                {{-- Hasil pemeriksaan teknisi --}}
                <section aria-labelledby="inspectionResultTitle"
                    class="rounded border border-slate-200 bg-white shadow-sm">

                    <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                        <div class="flex items-start gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded bg-amber-50 text-amber-600">
                                <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <path d="M9 3h6l1 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l1-2Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
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
                        <div class="px-4 py-4 sm:px-5">
                            <p class="text-sm font-medium text-slate-400">Ringkasan temuan</p>
                            <p class="mt-2 text-sm leading-6 text-slate-700">
                                {{ $inspectingLog?->description ?? 'Belum ada hasil pemeriksaan.' }}
                            </p>
                        </div>

                        @if ($requestedComponents->isNotEmpty())
                            <div class="px-4 py-4 sm:px-5">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-slate-400">Komponen yang diperlukan</p>
                                        <p class="mt-1 text-sm text-slate-500">Komponen yang digunakan dalam proses perbaikan.</p>
                                    </div>
                                </div>

                                <div class="mt-3 space-y-3">
                                    @foreach ($requestedComponents as $repairComponent)
                                        <div class="flex flex-col gap-3 rounded border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ $repairComponent->component->name }}
                                                </p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    Jumlah: {{ $repairComponent->quantity }} {{ $repairComponent->component->unit }}
                                                </p>
                                            </div>
                                            <span class="inline-flex w-fit items-center gap-1.5 rounded border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                <span class="size-2 rounded-full bg-amber-500"></span>
                                                {{ ucfirst($repairComponent->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($inspectingLog && $inspectingLog->photos->isNotEmpty())
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-sm font-medium text-slate-400">Foto pemeriksaan</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    @foreach ($inspectingLog->photos as $photo)
                                        <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto pemeriksaan" class="aspect-video w-full object-cover">
                                        </figure>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Progres perbaikan --}}
                @if ($repairingLog || $installedComponents->isNotEmpty())
                    <section aria-labelledby="repairWorkProgressTitle"
                        class="rounded border border-slate-200 bg-white shadow-sm">

                        <div class="border-b-[1.6px] border-slate-100 px-4 py-4 sm:px-5">
                            <div class="flex items-start gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded bg-blue-50 text-blue-700">
                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                        <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-2.3 2.3-3-3L14.7 6.3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 id="repairWorkProgressTitle" class="text-base font-bold text-slate-950">
                                            Progres perbaikan
                                        </h2>
                                    </div>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Informasi pekerjaan yang sedang dilakukan oleh teknisi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y-[1.6px] divide-slate-100">
                            @if ($repairingLog)
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-sm font-medium text-slate-400">Pekerjaan yang sedang dilakukan</p>
                                    <div class="mt-3 rounded border border-blue-100 bg-blue-50/60 p-3">
                                        <p class="text-sm leading-6 text-slate-700">
                                            {{ $repairingLog->description }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if ($installedComponents->isNotEmpty())
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-sm font-medium text-slate-400">Komponen terpasang</p>
                                    <div class="mt-3 space-y-3">
                                        @foreach ($installedComponents as $repairComponent)
                                            <div class="flex flex-col gap-3 rounded border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900">
                                                        {{ $repairComponent->component->name }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-500">
                                                        Jumlah: {{ $repairComponent->quantity }} {{ $repairComponent->component->unit }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex w-fit items-center gap-1.5 rounded border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                    <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                        <path d="m7 12 3 3 7-7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                                    </svg>
                                                    Terpasang
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($repairingLog && $repairingLog->photos->isNotEmpty())
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-sm font-medium text-slate-400">Foto progres</p>
                                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                        @foreach ($repairingLog->photos as $photo)
                                            <figure class="overflow-hidden rounded border border-slate-200 bg-slate-100">
                                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto progres" class="aspect-video w-full object-cover">
                                            </figure>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($lastLog)
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-sm font-medium text-slate-400">Catatan teknisi</p>
                                    <div class="mt-3 flex items-start gap-3 rounded border border-slate-200 bg-slate-50 p-3">
                                        <div class="min-w-0">
                                            <p class="text-sm leading-6 text-slate-700">
                                                {{ $lastLog->description }}
                                            </p>
                                            <p class="mt-2 text-xs text-slate-400">
                                                Diperbarui {{ $lastLog->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

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
                            <dt class="text-sm font-medium text-slate-400">Merek dan model</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                {{ $repair->freezer->brand }} {{ $repair->freezer->model }}
                            </dd>
                        </div>

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">Nomor seri</dt>
                            <dd class="mt-1.5 break-words text-sm font-semibold text-slate-900">
                                {{ $repair->freezer->serial_number }}
                            </dd>
                        </div>

                        <div class="px-4 py-3.5">
                            <dt class="text-sm font-medium text-slate-400">Kapasitas</dt>
                            <dd class="mt-1.5 text-sm leading-6 text-slate-700">
                                {{ $repair->freezer->capacity_liter ? $repair->freezer->capacity_liter . ' liter' : '-' }}
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
                            <p class="text-sm font-medium text-slate-400">Teknisi</p>

                            @if ($repair->technician)
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">
                                        {{ collect(explode(' ', $repair->technician->name))->filter()->take(2)->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">
                                            {{ $repair->technician->name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-500">Teknisi freezer</p>
                                    </div>
                                </div>
                            @else
                                <p class="mt-2 text-sm text-slate-500">Belum ada teknisi yang ditugaskan.</p>
                            @endif
                        </div>

                        <dl class="divide-y-[1.6px] divide-slate-100">
                            <div class="px-4 py-3.5">
                                <dt class="text-sm font-medium text-slate-400">Tanggal diterima</dt>
                                <dd class="mt-1.5 text-sm font-semibold text-slate-900">
                                    {{ $repair->created_at->translatedFormat('d F Y, \p\u\k\u\l H.i') }}
                                </dd>
                            </div>

                            <div class="px-4 py-3.5">
                                <dt class="text-sm font-medium text-slate-400">Status penugasan</dt>
                                <dd class="mt-2">
                                    <span class="inline-flex items-center gap-1.5 rounded border {{ $style[1] }} {{ $style[0] }} px-2.5 py-1 text-xs font-semibold {{ $style[2] }}">
                                        <span class="size-2 rounded-full {{ $style[3] }}"></span>
                                        {{ $repair->status->label() }}
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
                        <div class="flex size-9 shrink-0 items-center justify-center rounded bg-slate-100 text-slate-600">
                            <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                <path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-.9.8-1.7 1.2-1.7 2.7" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                                <path d="M12 17h.01" stroke="currentColor" stroke-linecap="round" stroke-width="2.5" />
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