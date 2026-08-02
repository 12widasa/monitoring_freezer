<div id="verifyFreezerModal" tabindex="-1" aria-hidden="true" aria-labelledby="verifyFreezerModalTitle"
    class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">

    <div class="relative max-h-full w-full max-w-lg p-4">
        <div
            class="relative flex max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-lg">

            {{-- Header --}}
            <div
                class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 px-4 py-3 md:px-5 md:py-4">

                <div>
                    <h2 id="verifyFreezerModalTitle" class="text-lg font-semibold text-slate-900">
                        Verifikasi data freezer
                    </h2>

                    <p class="mt-1 text-sm leading-5 text-slate-500">
                        Konfirmasikan hasil pemeriksaan unit fisik.
                    </p>
                </div>

                <button type="button" data-modal-hide="verifyFreezerModal"
                    class="ms-auto inline-flex size-9 shrink-0 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">

                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                        <path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>

                    <span class="sr-only">
                        Tutup modal
                    </span>
                </button>
            </div>

            <form id="verifyFreezerForm" action="" method="POST" class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PATCH')

                <input id="verificationFreezerId" name="verification_freezer_id" type="hidden"
                    value="{{ old('verification_freezer_id') }}">

                {{-- Content --}}
                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-4 md:p-5">
                    <div class="flex gap-3 rounded border border-blue-200 bg-blue-50 p-4 text-blue-800">

                        <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                            <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                        </svg>

                        <div>
                            <p class="text-sm font-semibold">
                                Pastikan pemeriksaan fisik sudah dilakukan
                            </p>

                            <p class="mt-1 text-sm leading-6 text-blue-700">
                                Cocokkan data pada halaman detail dengan unit
                                fisik.
                            </p>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="mb-3 text-sm font-medium text-slate-900">
                            Hasil verifikasi
                            <span class="text-red-600">*</span>
                        </legend>

                        <div class="space-y-3">
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded border border-slate-200 p-3 hover:bg-slate-50">

                                <input id="verificationResultVerified" name="verification_result" type="radio"
                                    value="{{ \App\Enums\VerificationStatus::VERIFIED->value }}"
                                    @checked(old('verification_result') === \App\Enums\VerificationStatus::VERIFIED->value)
                                    class="mt-0.5 size-4 border-slate-300 bg-slate-100 text-blue-600 focus:ring-2 focus:ring-blue-500">

                                <span>
                                    <span class="block text-sm font-semibold text-slate-900">
                                        Data sesuai
                                    </span>

                                    <span class="mt-1 block text-xs leading-5 text-slate-500">
                                        Unit fisik sesuai dengan informasi yang
                                        tercatat pada sistem.
                                    </span>
                                </span>
                            </label>

                            <label
                                class="flex cursor-pointer items-start gap-3 rounded border border-slate-200 p-3 hover:bg-slate-50">

                                <input id="verificationResultRejected" name="verification_result" type="radio"
                                    value="{{ \App\Enums\VerificationStatus::REJECTED->value }}"
                                    @checked(old('verification_result') === \App\Enums\VerificationStatus::REJECTED->value)
                                    class="mt-0.5 size-4 border-slate-300 bg-slate-100 text-rose-600 focus:ring-2 focus:ring-rose-500">

                                <span>
                                    <span class="block text-sm font-semibold text-slate-900">
                                        Data tidak sesuai
                                    </span>

                                    <span class="mt-1 block text-xs leading-5 text-slate-500">
                                        Terdapat ketidaksesuaian antara data
                                        sistem dan unit fisik.
                                    </span>
                                </span>
                            </label>
                        </div>

                        <x-form-error :messages="$errors->verifyServiceIntake->get('verification_result')" />
                    </fieldset>

                    <div>
                        <label for="conditionNote" class="mb-2 block text-sm font-medium text-slate-900">

                            Kondisi unit saat diterima

                            <span class="font-normal text-slate-500">
                                (opsional)
                            </span>
                        </label>

                        <textarea id="conditionNote" name="condition_note" rows="3"
                            placeholder="Contoh: unit kotor, pintu penyok, kabel terkelupas, atau kondisi fisik lainnya"
                            @class([
                                'block w-full resize-y rounded bg-white p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                'border border-slate-300 focus:border-blue-500' => !$errors->verifyServiceIntake->has(
                                    'condition_note'),
                                'border border-rose-500 focus:border-rose-500' => $errors->verifyServiceIntake->has(
                                    'condition_note'),
                            ])>{{ old('condition_note') }}</textarea>

                        <x-form-error :messages="$errors->verifyServiceIntake->get('condition_note')" />

                    </div>

                    <div id="rejectionReasonField" @class([
                        'hidden' =>
                            old('verification_result') !==
                            \App\Enums\VerificationStatus::REJECTED->value,
                    ])>

                        <label for="rejectionReason" class="mb-2 block text-sm font-medium text-slate-900">

                            Alasan penolakan
                            <span class="text-red-600">*</span>
                        </label>

                        <textarea id="rejectionReason" name="rejection_reason" rows="4"
                            placeholder="Jelaskan data atau kondisi fisik yang tidak sesuai" @class([
                                'block w-full resize-y rounded bg-white p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-rose-500',
                                'border border-slate-300 focus:border-rose-500' => !$errors->verifyServiceIntake->has(
                                    'rejection_reason'),
                                'border border-rose-500 focus:border-rose-500' => $errors->verifyServiceIntake->has(
                                    'rejection_reason'),
                            ])>{{ old('rejection_reason') }}</textarea>

                        <x-form-error :messages="$errors->verifyServiceIntake->get('rejection_reason')" />
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="flex shrink-0 flex-col-reverse gap-3 border-t border-slate-200 bg-white px-4 py-3 sm:flex-row sm:justify-end md:px-5 md:py-4">

                    <button type="button" data-modal-hide="verifyFreezerModal"
                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">

                        Batal
                    </button>

                    <button id="verifyFreezerSubmit" type="submit" disabled
                        class="w-full cursor-not-allowed rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white opacity-50 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">

                        Simpan verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('verifyFreezerForm');
        const freezerIdInput =
            document.getElementById('verificationFreezerId');

        const title =
            document.getElementById('verifyFreezerModalTitle');

        const verifiedRadio =
            document.getElementById('verificationResultVerified');

        const rejectedRadio =
            document.getElementById('verificationResultRejected');

        const conditionNote =
            document.getElementById('conditionNote');

        const rejectionField =
            document.getElementById('rejectionReasonField');

        const rejectionReason =
            document.getElementById('rejectionReason');

        const submitButton =
            document.getElementById('verifyFreezerSubmit');

        const verifyButtons =
            document.querySelectorAll('[data-verify-freezer]');

        if (
            !form ||
            !freezerIdInput ||
            !title ||
            !verifiedRadio ||
            !rejectedRadio ||
            !conditionNote ||
            !rejectionField ||
            !rejectionReason ||
            !submitButton
        ) {
            return;
        }

        const disableSubmit = () => {
            submitButton.disabled = true;

            submitButton.classList.add(
                'cursor-not-allowed',
                'opacity-50',
            );
        };

        const enableSubmit = () => {
            submitButton.disabled = false;

            submitButton.classList.remove(
                'cursor-not-allowed',
                'opacity-50',
            );
        };

        const useVerifiedStyle = () => {
            submitButton.textContent = 'Ya, verifikasi';

            submitButton.classList.remove(
                'bg-rose-700',
                'hover:bg-rose-800',
                'focus:ring-rose-300',
            );

            submitButton.classList.add(
                'bg-blue-700',
                'hover:bg-blue-800',
                'focus:ring-blue-300',
            );
        };

        const useRejectedStyle = () => {
            submitButton.textContent = 'Tolak verifikasi';

            submitButton.classList.remove(
                'bg-blue-700',
                'hover:bg-blue-800',
                'focus:ring-blue-300',
            );

            submitButton.classList.add(
                'bg-rose-700',
                'hover:bg-rose-800',
                'focus:ring-rose-300',
            );
        };

        const updateVerificationState = () => {
            if (verifiedRadio.checked) {
                rejectionField.classList.add('hidden');
                rejectionReason.required = false;
                rejectionReason.value = '';

                useVerifiedStyle();
                enableSubmit();

                return;
            }

            if (rejectedRadio.checked) {
                rejectionField.classList.remove('hidden');
                rejectionReason.required = true;

                useRejectedStyle();

                rejectionReason.value.trim() ?
                    enableSubmit() :
                    disableSubmit();

                return;
            }

            rejectionField.classList.add('hidden');
            rejectionReason.required = false;

            submitButton.textContent = 'Simpan verifikasi';

            disableSubmit();
        };

        const fillVerificationForm = (
            button,
            preserveValues = false,
        ) => {
            const freezerId =
                button.dataset.freezerId ?? '';

            const freezerCode =
                button.dataset.freezerCode ?? '';

            const verificationUrl =
                button.dataset.verificationUrl ?? '';

            form.action = verificationUrl;
            freezerIdInput.value = freezerId;

            title.textContent = freezerCode ?
                `Verifikasi ${freezerCode}` :
                'Verifikasi data freezer';

            if (!preserveValues) {
                verifiedRadio.checked = false;
                rejectedRadio.checked = false;
                conditionNote.value = '';
                rejectionReason.value = '';
            }

            updateVerificationState();
        };

        verifyButtons.forEach((button) => {
            button.addEventListener('click', () => {
                fillVerificationForm(button);

                const dropdownElement = button.closest(
                    '[id^="freezerActionsDropdown-"]',
                );

                if (!dropdownElement) {
                    return;
                }

                const dropdownInstance =
                    window.FlowbiteInstances?.getInstance(
                        'Dropdown',
                        dropdownElement.id,
                    );

                if (dropdownInstance) {
                    dropdownInstance.hide();
                    return;
                }

                dropdownElement.classList.add('hidden');
            });
        });

        verifiedRadio.addEventListener(
            'change',
            updateVerificationState,
        );

        rejectedRadio.addEventListener(
            'change',
            updateVerificationState,
        );

        rejectionReason.addEventListener('input', () => {
            if (rejectedRadio.checked) {
                rejectionReason.value.trim() ?
                    enableSubmit() :
                    disableSubmit();
            }
        });

        @if ($errors->verifyServiceIntake->any())
            window.setTimeout(() => {
                const failedFreezerId = @js(old('verification_freezer_id'));

                const failedButton = [...verifyButtons].find(
                    (button) =>
                    button.dataset.freezerId ===
                    String(failedFreezerId),
                );

                if (!failedButton) {
                    return;
                }

                fillVerificationForm(
                    failedButton,
                    true,
                );

                failedButton.click();
            }, 0);
        @endif
    });
</script>
