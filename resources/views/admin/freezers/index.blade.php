@extends('admin.layout')

@section('content')
    <main class="min-h-screen bg-slate-50 px-2 py-3">
        <div class="mx-auto max-w-7xl">
            @include('admin.freezers.partials.freezer-summary')

            {{-- Daftar freezer --}}
            <section class="mt-4 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">

                {{-- Toolbar tabel --}}
                @include('admin.freezers.partials.freezer-toolbar')

                {{-- Tabel freezer --}}
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full whitespace-nowrap text-left text-sm text-slate-500">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-700">
                            <tr>
                                <th scope="col" class="px-5 py-3">Freezer</th>
                                <th scope="col" class="px-5 py-3">Pemilik</th>
                                <th scope="col" class="px-5 py-3">Nomor seri</th>
                                <th scope="col" class="px-5 py-3">Status reparasi</th>
                                <th scope="col" class="px-5 py-3">Verifikasi</th>
                                <th scope="col" class="px-5 py-3">Diperbarui</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($freezers as $freezer)
                                @php
                                    $latestIntake = $freezer->latestIntake;
                                    $currentRepair = $latestIntake?->repair;
                                    $repairStatus = $currentRepair?->status;
                                    $verificationStatus = $latestIntake?->status_verifikasi;

                                    $repairBadgeClass = match ($repairStatus) {
                                        \App\Enums\RepairStatus::QUEUED
                                            => 'border-amber-200 bg-amber-50 text-amber-700',

                                        \App\Enums\RepairStatus::INSPECTING
                                            => 'border-blue-200 bg-blue-50 text-blue-700',

                                        \App\Enums\RepairStatus::REPAIRING
                                            => 'border-amber-200 bg-amber-50 text-amber-700',

                                        \App\Enums\RepairStatus::COMPLETED
                                            => 'border-emerald-200 bg-emerald-50 text-emerald-700',

                                        default => 'border-slate-200 bg-slate-100 text-slate-700',
                                    };

                                    $repairLabel = match ($repairStatus) {
                                        \App\Enums\RepairStatus::QUEUED => 'Menunggu diperiksa',

                                        \App\Enums\RepairStatus::INSPECTING => 'Sedang diperiksa',

                                        \App\Enums\RepairStatus::REPAIRING => 'Sedang diperbaiki',

                                        \App\Enums\RepairStatus::COMPLETED => 'Perbaikan selesai',

                                        default => 'Tidak ada reparasi aktif',
                                    };

                                    $verificationBadgeClass = match ($verificationStatus) {
                                        \App\Enums\VerificationStatus::VERIFIED
                                            => 'border-emerald-200 bg-emerald-50 text-emerald-700',

                                        \App\Enums\VerificationStatus::REJECTED
                                            => 'border-rose-200 bg-rose-50 text-rose-700',

                                        \App\Enums\VerificationStatus::PENDING_ARRIVAL
                                            => 'border-amber-200 bg-amber-50 text-amber-700',

                                        default => 'border-slate-200 bg-slate-100 text-slate-600',
                                    };

                                    $verificationLabel = match ($verificationStatus) {
                                        \App\Enums\VerificationStatus::VERIFIED => 'Sudah diverifikasi',

                                        \App\Enums\VerificationStatus::REJECTED => 'Ditolak',

                                        \App\Enums\VerificationStatus::PENDING_ARRIVAL => 'Belum diverifikasi',

                                        default => 'Penerimaan tidak tersedia',
                                    };

                                    $lastUpdatedAt = $freezer->updated_at;

                                    foreach (
                                        [$latestIntake?->updated_at, $currentRepair?->updated_at]
                                        as $candidateUpdatedAt
                                    ) {
                                        if (
                                            $candidateUpdatedAt !== null &&
                                            $candidateUpdatedAt->greaterThan($lastUpdatedAt)
                                        ) {
                                            $lastUpdatedAt = $candidateUpdatedAt;
                                        }
                                    }
                                @endphp

                                <tr class="border-b border-slate-200 bg-white hover:bg-slate-50">
                                    {{-- Freezer --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex size-10 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">

                                                <svg class="size-5" aria-hidden="true" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    viewBox="0 0 24 24">
                                                    <line x1="2" x2="22" y1="12" y2="12" />
                                                    <line x1="12" x2="12" y1="2" y2="22" />
                                                    <path d="m20 16-4-4 4-4" />
                                                    <path d="m4 8 4 4-4 4" />
                                                    <path d="m16 4-4 4-4-4" />
                                                    <path d="m8 20 4-4 4 4" />
                                                </svg>
                                            </div>

                                            <div class="min-w-0">
                                                <p class="font-semibold text-slate-900">
                                                    {{ $freezer->freezer_code }}
                                                </p>

                                                <p class="text-slate-500">
                                                    {{ $freezer->brand }} {{ $freezer->model }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Pemilik --}}
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-700">
                                            {{ $freezer->customer->company_name }}
                                        </p>
                                    </td>

                                    {{-- Nomor seri --}}
                                    <td class="px-5 py-4">
                                        {{ $freezer->serial_number ?: 'Tidak tersedia' }}
                                    </td>

                                    {{-- Status reparasi --}}
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $repairBadgeClass }}">

                                            @if ($repairStatus === \App\Enums\RepairStatus::REPAIRING)
                                                <svg class="size-3.5" aria-hidden="true" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z" />
                                                </svg>
                                            @elseif ($repairStatus === \App\Enums\RepairStatus::COMPLETED)
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                </svg>
                                            @elseif ($repairStatus !== null)
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2" />
                                                </svg>
                                            @else
                                                <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="M12 8v5" stroke="currentColor" stroke-linecap="round"
                                                        stroke-width="2" />
                                                </svg>
                                            @endif

                                            {{ $repairLabel }}
                                        </span>
                                    </td>

                                    {{-- Verifikasi --}}
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium {{ $verificationBadgeClass }}">

                                            <svg class="size-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" />

                                                @if ($verificationStatus === \App\Enums\VerificationStatus::VERIFIED)
                                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                @elseif ($verificationStatus === \App\Enums\VerificationStatus::REJECTED)
                                                    <path d="m8.5 8.5 7 7m0-7-7 7" stroke="currentColor"
                                                        stroke-linecap="round" stroke-width="2" />
                                                @else
                                                    <path d="M12 8v5m0 3h.01" stroke="currentColor" stroke-linecap="round"
                                                        stroke-width="2" />
                                                @endif
                                            </svg>

                                            {{ $verificationLabel }}
                                        </span>
                                    </td>

                                    {{-- Diperbarui --}}
                                    <td class="px-5 py-4">
                                        <span
                                            title="{{ local_datetime($lastUpdatedAt, 'd F Y, H.i', translated: true) }}">
                                            {{ $lastUpdatedAt->locale('id')->diffForHumans() }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 text-right">
                                        <button id="freezerActionsButton-{{ $freezer->id }}"
                                            data-dropdown-toggle="freezerActionsDropdown-{{ $freezer->id }}"
                                            data-dropdown-placement="bottom-end" type="button"
                                            aria-label="Buka aksi freezer {{ $freezer->freezer_code }}"
                                            class="inline-flex size-9 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">

                                            <svg class="size-5" aria-hidden="true" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.8" />
                                                <circle cx="12" cy="12" r="1.8" />
                                                <circle cx="12" cy="19" r="1.8" />
                                            </svg>
                                        </button>

                                        <div id="freezerActionsDropdown-{{ $freezer->id }}"
                                            class="z-50 hidden w-52 rounded border border-slate-200 bg-white p-2 text-left shadow-lg">

                                            <ul class="space-y-1 text-sm text-slate-700"
                                                aria-labelledby="freezerActionsButton-{{ $freezer->id }}">

                                                <li>
                                                    <a href="{{ route('admin.freezers.show', [
                                                        'freezer' => $freezer,
                                                        ...request()->only(['search', 'repair_status', 'verification_status', 'sort', 'per_page', 'page']),
                                                    ]) }}"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left hover:bg-slate-100">

                                                        <svg class="size-4 text-slate-500" aria-hidden="true"
                                                            fill="none" viewBox="0 0 24 24">
                                                            <path
                                                                d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z"
                                                                stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" />

                                                            <circle cx="12" cy="12" r="3"
                                                                stroke="currentColor" stroke-width="2" />
                                                        </svg>

                                                        Lihat detail
                                                    </a>
                                                </li>

                                                <li>
                                                    <button type="button" data-modal-target="editFreezerModal"
                                                        data-modal-toggle="editFreezerModal" data-edit-freezer
                                                        data-update-url="{{ route('admin.freezers.update', [
                                                            'freezer' => $freezer,
                                                            ...request()->only(['search', 'repair_status', 'verification_status', 'sort', 'per_page', 'page']),
                                                        ]) }}"
                                                        data-freezer-id="{{ $freezer->id }}"
                                                        data-freezer-code="{{ $freezer->freezer_code }}"
                                                        data-customer-id="{{ $freezer->customer_id }}"
                                                        data-customer-name="{{ $freezer->customer->company_name }}"
                                                        data-brand="{{ $freezer->brand }}"
                                                        data-model="{{ $freezer->model }}"
                                                        data-serial-number="{{ $freezer->serial_number }}"
                                                        data-capacity-liter="{{ $freezer->capacity_liter }}"
                                                        data-estimated-age="{{ $freezer->estimated_age }}"
                                                        data-photo-url="{{ $freezer->photo_path ? \Illuminate\Support\Facades\Storage::url($freezer->photo_path) : '' }}"
                                                        data-photo-name="{{ $freezer->photo_path ? basename($freezer->photo_path) : '' }}"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left hover:bg-slate-100">

                                                        <svg class="size-4 text-slate-500" aria-hidden="true"
                                                            fill="none" viewBox="0 0 24 24">
                                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"
                                                                stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" />
                                                        </svg>

                                                        Edit freezer
                                                    </button>
                                                </li>
                                            </ul>

                                            @if ($verificationStatus === \App\Enums\VerificationStatus::PENDING_ARRIVAL)
                                                <li>
                                                    <button type="button" data-modal-target="verifyFreezerModal"
                                                        data-modal-toggle="verifyFreezerModal" data-verify-freezer
                                                        data-freezer-id="{{ $freezer->id }}"
                                                        data-freezer-code="{{ $freezer->freezer_code }}"
                                                        data-verification-url="{{ route('admin.freezers.verification.update', [
                                                            'freezer' => $freezer,
                                                            ...request()->only(['search', 'repair_status', 'verification_status', 'sort', 'per_page', 'page']),
                                                        ]) }}"
                                                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-blue-700 hover:bg-blue-50">

                                                        <svg class="size-4" aria-hidden="true" fill="none"
                                                            viewBox="0 0 24 24">

                                                            <circle cx="12" cy="12" r="9"
                                                                stroke="currentColor" stroke-width="2" />

                                                            <path d="m8 12 2.5 2.5L16 9" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" />
                                                        </svg>

                                                        Verifikasi freezer
                                                    </button>
                                                </li>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <div class="mx-auto flex max-w-sm flex-col items-center">
                                            <div
                                                class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                                                <svg class="size-6" aria-hidden="true" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="11" cy="11" r="7" stroke="currentColor"
                                                        stroke-width="2" />

                                                    <path d="m20 20-4-4" stroke="currentColor" stroke-linecap="round"
                                                        stroke-width="2" />
                                                </svg>
                                            </div>

                                            <p class="mt-3 font-semibold text-slate-900">
                                                Data freezer tidak ditemukan
                                            </p>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Ubah kata pencarian atau filter yang sedang digunakan.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-table-pagination :paginator="$freezers" :action="route('admin.freezers.index')" :per-page="$perPage" :query="[
                    'search' => $search,
                    'repair_status' => $selectedRepairStatus,
                    'verification_status' => $selectedVerificationStatus,
                    'sort' => $selectedSort,
                ]"
                    item-label="freezer" aria-label="Pagination freezer" />
            </section>
        </div>
    </main>

    @include('admin.freezers.partials.create-modal')
    @include('admin.freezers.partials.edit-modal')
    @include('admin.freezers.partials.verify-freezer')
@endsection
