<div id="createRepairModal" tabindex="-1" aria-hidden="true" aria-labelledby="createRepairModalTitle"
    class="fixed inset-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden p-4">

    <div class="relative max-h-full w-full max-w-2xl">
        <div
            class="relative flex max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-xl">

            {{-- Header --}}
            <div class="flex shrink-0 items-start justify-between border-b-[1.6px] border-slate-100 p-4">

                <div>
                    <h2 id="createRepairModalTitle" class="text-lg font-semibold text-slate-900">
                        Buat Tugas Reparasi
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih intake terverifikasi dan tentukan teknisi bila
                        sudah tersedia.
                    </p>
                </div>

                <button type="button" data-modal-hide="createRepairModal" aria-label="Tutup modal"
                    class="inline-flex size-9 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100">

                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                    </svg>
                </button>
            </div>

            <form id="createRepairForm" action="{{ route('admin.repairs.store') }}" method="POST"
                class="flex min-h-0 flex-1 flex-col">

                @csrf

                {{-- Body --}}
                <div class="min-h-0 flex-1 overflow-y-auto p-4">
                    <div class="space-y-4">

                        {{-- Service intake --}}
                        <div>
                            <label for="repairServiceIntake" class="mb-2 block text-sm font-medium text-slate-700">

                                Freezer
                                <span class="text-rose-600">*</span>
                            </label>

                            <select id="repairServiceIntake" name="service_intake_id" required
                                @disabled($eligibleIntakes->isEmpty()) @class([
                                    'block w-full rounded bg-white p-2.5 text-sm text-slate-900 focus:ring-blue-500',
                                    'border border-slate-300 focus:border-blue-500' => !$errors->createRepair->has(
                                        'service_intake_id'),
                                    'border border-rose-500 focus:border-rose-500' => $errors->createRepair->has(
                                        'service_intake_id'),
                                    'cursor-not-allowed bg-slate-100 text-slate-500' => $eligibleIntakes->isEmpty(),
                                ])>

                                <option value="">
                                    {{ $eligibleIntakes->isEmpty() ? 'Tidak ada freezer yang tersedia' : 'Pilih freezer terverifikasi' }}
                                </option>

                                @foreach ($eligibleIntakes as $intake)
                                    <option value="{{ $intake->id }}" data-intake-code="{{ $intake->intake_code }}"
                                        data-freezer-code="{{ $intake->freezer->freezer_code }}"
                                        data-freezer-name="{{ trim($intake->freezer->brand . ' ' . $intake->freezer->model) }}"
                                        data-customer-name="{{ $intake->freezer->customer?->company_name ?? 'Pemilik tidak tersedia' }}"
                                        data-complaint="{{ $intake->complaint_note ?: 'Tidak ada catatan keluhan.' }}"
                                        @selected((string) old('service_intake_id', $preselectedIntakeId ?? '') === (string) $intake->id)>

                                        {{ $intake->freezer->freezer_code }}
                                        —
                                        {{ trim($intake->freezer->brand . ' ' . $intake->freezer->model) }}
                                        —
                                        {{ $intake->freezer->customer?->company_name ?? 'Pemilik tidak tersedia' }}
                                    </option>
                                @endforeach
                            </select>

                            <x-form-error :messages="$errors->createRepair->get('service_intake_id')" />

                            <p class="mt-1.5 text-xs leading-4 text-slate-500">
                                Hanya intake terbaru yang sudah diverifikasi dan
                                belum memiliki tugas reparasi yang dapat dipilih.
                            </p>
                        </div>

                        {{-- Ringkasan freezer --}}
                        <section id="repairFreezerSummary" aria-label="Ringkasan freezer"
                            class="hidden rounded border border-slate-200 bg-slate-50 p-3.5">

                            <div class="flex items-start gap-3 border-b-[1.6px] border-slate-200 pb-3">

                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded border border-blue-100 bg-blue-50 text-blue-600">

                                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                                        <path d="M5 4h14v16H5zM8 8h8M8 12h8M9 17h.01" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p id="repairFreezerIdentity" class="font-semibold text-slate-900">
                                    </p>

                                    <p id="repairCustomerName" class="mt-1 text-sm text-slate-500">
                                    </p>

                                    <p id="repairIntakeCode" class="mt-1 text-xs text-slate-400">
                                    </p>
                                </div>
                            </div>

                            <div class="pt-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Keluhan
                                </p>

                                <p id="repairComplaint"
                                    class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                                </p>
                            </div>
                        </section>

                        {{-- Teknisi --}}
                        <div>
                            <label for="repairTechnician" class="mb-2 block text-sm font-medium text-slate-700">

                                Teknisi
                                <span class="font-normal text-slate-400">
                                    (opsional)
                                </span>
                            </label>

                            <select id="repairTechnician" name="technician_id" @class([
                                'block w-full rounded bg-white p-2.5 text-sm text-slate-900 focus:ring-blue-500',
                                'border border-slate-300 focus:border-blue-500' => !$errors->createRepair->has(
                                    'technician_id'),
                                'border border-rose-500 focus:border-rose-500' => $errors->createRepair->has(
                                    'technician_id'),
                            ])>

                                <option value="">
                                    Tetapkan nanti
                                </option>

                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}" @selected((string) old('technician_id') === (string) $technician->id)>

                                        {{ $technician->name }}
                                        —
                                        {{ $technician->active_repairs_count }}
                                        tugas aktif
                                    </option>
                                @endforeach
                            </select>

                            <x-form-error :messages="$errors->createRepair->get('technician_id')" />

                            <p class="mt-1.5 text-xs leading-4 text-slate-500">
                                Tugas tanpa teknisi akan masuk ke antrean belum
                                ditugaskan.
                            </p>
                        </div>

                        {{-- Informasi status awal --}}
                        <div class="flex items-start gap-3 rounded border border-amber-200 bg-amber-50 p-3">

                            <svg class="mt-0.5 size-5 shrink-0 text-amber-600" aria-hidden="true" fill="none"
                                viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                                <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-linecap="round"
                                    stroke-width="2" />
                            </svg>

                            <div>
                                <p class="text-sm font-medium text-amber-900">
                                    Status awal tugas
                                </p>

                                <p class="mt-1 text-xs leading-5 text-amber-800">
                                    Tugas baru dibuat dengan status Dalam
                                    Antrean. Analisis awal diisi oleh teknisi
                                    ketika pemeriksaan dimulai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="flex shrink-0 flex-col-reverse gap-2 border-t-[1.6px] border-slate-100 p-4 sm:flex-row sm:justify-end">

                    <button type="button" data-modal-hide="createRepairModal"
                        class="inline-flex items-center justify-center rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-100">

                        Batal
                    </button>

                    <button id="createRepairSubmit" type="submit" @disabled($eligibleIntakes->isEmpty())
                        @class([
                            'inline-flex items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4 focus:ring-blue-300',
                            'hover:bg-blue-800' => $eligibleIntakes->isNotEmpty(),
                            'cursor-not-allowed opacity-50' => $eligibleIntakes->isEmpty(),
                        ])>

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>

                        Buat Tugas Reparasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const intakeSelect =
            document.getElementById('repairServiceIntake');

        const summary =
            document.getElementById('repairFreezerSummary');

        const freezerIdentity =
            document.getElementById('repairFreezerIdentity');

        const customerName =
            document.getElementById('repairCustomerName');

        const intakeCode =
            document.getElementById('repairIntakeCode');

        const complaint =
            document.getElementById('repairComplaint');

        if (
            !intakeSelect ||
            !summary ||
            !freezerIdentity ||
            !customerName ||
            !intakeCode ||
            !complaint
        ) {
            return;
        }

        const updateSummary = () => {
            const option =
                intakeSelect.options[intakeSelect.selectedIndex];

            if (!option?.value) {
                summary.classList.add('hidden');

                freezerIdentity.textContent = '';
                customerName.textContent = '';
                intakeCode.textContent = '';
                complaint.textContent = '';

                return;
            }

            const freezerCode =
                option.dataset.freezerCode ?? '';

            const freezerName =
                option.dataset.freezerName ?? '';

            freezerIdentity.textContent = [freezerCode, freezerName]
                .filter(Boolean)
                .join(' · ');

            customerName.textContent =
                option.dataset.customerName ?? '';

            intakeCode.textContent =
                option.dataset.intakeCode ?
                `Service intake: ${option.dataset.intakeCode}` :
                '';

            complaint.textContent =
                option.dataset.complaint ??
                'Tidak ada catatan keluhan.';

            summary.classList.remove('hidden');
        };

        intakeSelect.addEventListener(
            'change',
            updateSummary,
        );

        updateSummary();

        @if ($errors->createRepair->any())
            window.setTimeout(() => {
                const trigger = document.querySelector(
                    '[data-modal-target="createRepairModal"]',
                );

                trigger?.click();
            }, 0);
        @endif
    });
</script>
