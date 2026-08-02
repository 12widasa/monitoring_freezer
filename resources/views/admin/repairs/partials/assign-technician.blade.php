@php
    $assignmentErrorBag = $errors->getBag('updateRepairTechnician');
    $hasAssignmentErrors = $assignmentErrorBag->any();
@endphp

<div id="assignTechnicianModal" tabindex="-1" aria-hidden="true"
    data-has-validation-errors="{{ $hasAssignmentErrors ? '1' : '0' }}"
    class="fixed inset-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden p-4">

    <div class="relative max-h-full w-full max-w-lg">
        <form id="assignTechnicianForm" method="POST" action=""
            class="relative flex max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-xl">

            @csrf
            @method('PATCH')

            {{-- Header --}}
            <div class="flex shrink-0 items-start justify-between border-b-[1.6px] border-slate-100 p-4">
                <div class="min-w-0">
                    <h2 id="assignmentModalTitle" class="text-lg font-semibold text-slate-900">
                        Tetapkan Teknisi
                    </h2>

                    <p id="assignmentModalDescription" class="mt-1 text-sm text-slate-500">
                        Pilih teknisi yang akan menangani tugas ini.
                    </p>
                </div>

                <button type="button" data-modal-hide="assignTechnicianModal"
                    aria-label="Tutup modal penugasan teknisi"
                    class="inline-flex size-9 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">

                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                <div class="space-y-4">

                    {{-- Ringkasan tugas --}}
                    <section aria-label="Ringkasan tugas reparasi"
                        class="rounded border border-slate-200 bg-slate-50 p-3">

                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded border border-rose-100 bg-rose-50 text-rose-600">

                                <svg class="size-6" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3" />

                                    <rect x="9" y="2" width="6" height="4" rx="1" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="M9 12h6m-6 4h4" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="2" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p id="assignmentRepairCode" class="font-semibold text-slate-900">
                                    —
                                </p>

                                <p id="assignmentFreezerCode" class="mt-0.5 text-sm text-slate-500">
                                    —
                                </p>

                                <p id="assignmentFreezerName" class="mt-0.5 text-sm text-slate-500">
                                    —
                                </p>
                            </div>

                            <span id="assignmentCurrentStatus"
                                class="inline-flex shrink-0 items-center rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">
                                Belum Ditugaskan
                            </span>
                        </div>
                    </section>

                    {{-- Pilihan teknisi --}}
                    <fieldset>
                        <legend class="mb-2 text-sm font-medium text-slate-700">
                            Pilih Teknisi
                            <span class="text-rose-600">*</span>
                        </legend>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                <svg class="size-4 text-slate-400" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">

                                    <circle cx="11" cy="11" r="8" stroke="currentColor"
                                        stroke-width="2" />

                                    <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="2" />
                                </svg>
                            </div>

                            <input type="search" id="technicianCombobox" autocomplete="off" placeholder="Cari teknisi"
                                aria-controls="technicianDropdown" aria-expanded="false"
                                class="block w-full rounded border border-slate-300 bg-white p-2.5 ps-10 pe-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">

                            <div id="technicianDropdown"
                                class="absolute start-0 top-full z-[60] mt-1 hidden w-full overflow-hidden rounded border border-slate-200 bg-white shadow-lg">

                                <ul id="technicianOptions" class="max-h-60 overflow-y-auto p-1.5 text-sm text-slate-700"
                                    aria-label="Daftar teknisi">

                                    @foreach ($technicians as $technician)
                                        @php
                                            $technicianInitials = collect(preg_split('/\s+/', trim($technician->name)))
                                                ->filter()
                                                ->take(2)
                                                ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                                ->implode('');
                                        @endphp

                                        <li data-technician-option data-name="{{ mb_strtolower($technician->name) }}">

                                            <button type="button" data-technician-id="{{ $technician->id }}"
                                                data-technician-name="{{ $technician->name }}"
                                                class="flex w-full items-center gap-3 rounded px-3 py-2.5 text-left hover:bg-slate-100 focus:bg-slate-100 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50">

                                                <div
                                                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">

                                                    {{ $technicianInitials }}
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate font-semibold text-slate-900">
                                                        {{ $technician->name }}
                                                    </p>

                                                    <p class="mt-0.5 text-xs text-slate-500">
                                                        {{ $technician->active_repairs_count }}
                                                        tugas aktif
                                                    </p>
                                                </div>

                                                <span data-current-label
                                                    class="hidden text-xs font-medium text-slate-500">
                                                    Saat ini
                                                </span>

                                                <svg data-selected-icon class="hidden size-4 shrink-0 text-blue-600"
                                                    aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                                    <path d="m5 12 4 4L19 6" stroke="currentColor"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" />
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach

                                    <li id="technicianEmptyState"
                                        class="{{ $technicians->isEmpty() ? '' : 'hidden' }} px-3 py-6 text-center text-sm text-slate-500">

                                        Teknisi tidak ditemukan.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <input type="hidden" name="technician_id" id="selectedTechnicianId"
                            value="{{ old('technician_id') }}">

                        @if ($assignmentErrorBag->has('technician_id'))
                            <p class="mt-2 text-sm text-rose-600">
                                {{ $assignmentErrorBag->first('technician_id') }}
                            </p>
                        @endif

                        <p class="mt-2 text-xs leading-4 text-slate-500">
                            Jumlah tugas aktif membantu admin mempertimbangkan beban kerja teknisi.
                        </p>
                    </fieldset>

                    {{-- Alasan pergantian --}}
                    <div id="assignmentReasonContainer" class="hidden">

                        <label for="assignmentReason" class="mb-2 block text-sm font-medium text-slate-700">

                            Alasan Pergantian
                            <span class="text-rose-600">*</span>
                        </label>

                        <textarea id="assignmentReason" name="reason" rows="4" maxlength="1000"
                            placeholder="Contoh: Teknisi sebelumnya sakit dan tidak dapat melanjutkan pekerjaan."
                            class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">{{ old('reason') }}</textarea>

                        @if ($assignmentErrorBag->has('reason'))
                            <p class="mt-2 text-sm text-rose-600">
                                {{ $assignmentErrorBag->first('reason') }}
                            </p>
                        @endif

                        <p class="mt-2 text-xs leading-4 text-slate-500">
                            Alasan akan disimpan dalam histori penugasan dan log reparasi.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="flex shrink-0 flex-col-reverse gap-2 border-t-[1.6px] border-slate-100 p-4 sm:flex-row sm:justify-end">

                <button type="button" data-modal-hide="assignTechnicianModal"
                    class="inline-flex items-center justify-center rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">

                    Batal
                </button>

                <button type="submit" id="assignmentSubmitButton"
                    class="inline-flex items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">

                    <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                        <circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="2" />

                        <path d="M3 21a7 7 0 0 1 11.5-5.4M19 16v6m3-3h-6" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>

                    <span id="assignmentSubmitLabel">
                        Tetapkan Teknisi
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('assignTechnicianModal');
        const form = document.getElementById('assignTechnicianForm');
        const triggers = document.querySelectorAll('[data-assignment-trigger]');

        const title = document.getElementById('assignmentModalTitle');
        const description = document.getElementById('assignmentModalDescription');
        const repairCode = document.getElementById('assignmentRepairCode');
        const freezerCode = document.getElementById('assignmentFreezerCode');
        const freezerName = document.getElementById('assignmentFreezerName');
        const currentStatus = document.getElementById('assignmentCurrentStatus');

        const combobox = document.getElementById('technicianCombobox');
        const dropdown = document.getElementById('technicianDropdown');
        const selectedTechnicianId =
            document.getElementById('selectedTechnicianId');

        const technicianOptions = Array.from(
            document.querySelectorAll('[data-technician-option]'),
        );

        const technicianButtons = Array.from(
            document.querySelectorAll('[data-technician-id]'),
        );

        const emptyState =
            document.getElementById('technicianEmptyState');

        const reasonContainer =
            document.getElementById('assignmentReasonContainer');

        const reason =
            document.getElementById('assignmentReason');

        const submitLabel =
            document.getElementById('assignmentSubmitLabel');

        if (
            !modal ||
            !form ||
            !combobox ||
            !dropdown ||
            !selectedTechnicianId
        ) {
            return;
        }

        let preserveOldInput =
            modal.dataset.hasValidationErrors === '1';

        const hideTechnicianDropdown = () => {
            dropdown.classList.add('hidden');
            combobox.setAttribute('aria-expanded', 'false');
        };

        const showTechnicianDropdown = () => {
            dropdown.classList.remove('hidden');
            combobox.setAttribute('aria-expanded', 'true');
        };

        const updateSelectedOption = () => {
            technicianButtons.forEach((button) => {
                const isSelected =
                    button.dataset.technicianId ===
                    selectedTechnicianId.value;

                button
                    .querySelector('[data-selected-icon]')
                    ?.classList.toggle('hidden', !isSelected);
            });
        };

        const configureModal = (trigger) => {
            const currentTechnicianId =
                trigger.dataset.currentTechnicianId ?? '';

            const currentTechnicianName =
                trigger.dataset.currentTechnicianName ?? '';

            const isReplacement =
                currentTechnicianId !== '';

            form.action = trigger.dataset.updateUrl;

            title.textContent = isReplacement ?
                'Ganti Teknisi' :
                'Tetapkan Teknisi';

            description.textContent = isReplacement ?
                'Pilih teknisi pengganti dan jelaskan alasan pergantian.' :
                'Pilih teknisi yang akan menangani tugas ini.';

            submitLabel.textContent = isReplacement ?
                'Ganti Teknisi' :
                'Tetapkan Teknisi';

            repairCode.textContent =
                trigger.dataset.repairCode || '—';

            freezerCode.textContent =
                trigger.dataset.freezerCode ?
                `Freezer ${trigger.dataset.freezerCode}` :
                '—';

            freezerName.textContent =
                trigger.dataset.freezerName || '—';

            currentStatus.textContent = isReplacement ?
                `Saat ini: ${currentTechnicianName}` :
                'Belum Ditugaskan';

            reasonContainer.classList.toggle(
                'hidden',
                !isReplacement,
            );

            reason.required = isReplacement;

            technicianButtons.forEach((button) => {
                const isCurrent =
                    button.dataset.technicianId ===
                    currentTechnicianId;

                button.disabled = isCurrent;

                button
                    .querySelector('[data-current-label]')
                    ?.classList.toggle('hidden', !isCurrent);
            });

            if (!preserveOldInput) {
                selectedTechnicianId.value = '';
                combobox.value = '';
                reason.value = '';
            } else {
                const selectedButton = technicianButtons.find(
                    (button) =>
                    button.dataset.technicianId ===
                    selectedTechnicianId.value,
                );

                if (selectedButton) {
                    combobox.value =
                        selectedButton.dataset.technicianName;
                }
            }

            updateSelectedOption();
            hideTechnicianDropdown();

            preserveOldInput = false;
            modal.dataset.hasValidationErrors = '0';
        };

        triggers.forEach((trigger) => {
            trigger.addEventListener('click', () => {
                configureModal(trigger);
            });
        });

        combobox.addEventListener('focus', () => {
            showTechnicianDropdown();
        });

        combobox.addEventListener('click', () => {
            showTechnicianDropdown();
        });

        combobox.addEventListener('input', () => {
            const keyword =
                combobox.value.trim().toLocaleLowerCase('id-ID');

            let visibleCount = 0;

            technicianOptions.forEach((option) => {
                const matches =
                    option.dataset.name.includes(keyword);

                option.classList.toggle('hidden', !matches);

                if (matches) {
                    visibleCount++;
                }
            });

            emptyState.classList.toggle(
                'hidden',
                visibleCount > 0,
            );

            showTechnicianDropdown();
        });

        technicianButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (button.disabled) {
                    return;
                }

                selectedTechnicianId.value =
                    button.dataset.technicianId;

                combobox.value =
                    button.dataset.technicianName;

                updateSelectedOption();
                hideTechnicianDropdown();
            });
        });

        document.addEventListener('click', (event) => {
            if (
                !combobox.contains(event.target) &&
                !dropdown.contains(event.target)
            ) {
                hideTechnicianDropdown();
            }
        });

        document
            .querySelectorAll(
                '[data-modal-hide="assignTechnicianModal"]',
            )
            .forEach((button) => {
                button.addEventListener(
                    'click',
                    hideTechnicianDropdown,
                );
            });

        form.addEventListener('submit', (event) => {
            if (selectedTechnicianId.value === '') {
                event.preventDefault();

                combobox.focus();
                showTechnicianDropdown();
            }
        });

        if (modal.dataset.hasValidationErrors === '1') {
            const trigger = triggers[0];

            if (trigger) {
                window.setTimeout(() => {
                    trigger.click();
                }, 0);
            }
        }
    });
</script>
