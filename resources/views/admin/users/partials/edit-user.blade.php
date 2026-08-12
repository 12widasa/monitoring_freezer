@php
    $editErrors = $errors->editUser;
@endphp

<div id="editUserModal" tabindex="-1" aria-hidden="true" aria-labelledby="editUserModalTitle"
    class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">

    <div class="relative max-h-full w-full max-w-2xl p-4">
        <div
            class="relative flex max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 md:p-5">
                <div>
                    <h2 id="editUserModalTitle" class="text-lg font-semibold text-slate-900">
                        Edit pengguna
                    </h2>

                    <p id="editUserModalDescription" class="mt-1 text-sm text-slate-500">
                        Perbarui informasi akun dan data pengguna.
                    </p>
                </div>

                <button type="button" data-modal-hide="editUserModal"
                    class="ms-auto inline-flex size-9 shrink-0 items-center justify-center rounded bg-transparent text-slate-400 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">

                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        <path d="M6 18 18 6M6 6l12 12" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                    </svg>

                    <span class="sr-only">
                        Tutup modal
                    </span>
                </button>
            </div>

            <form id="editUserForm" method="POST" class="flex min-h-0 flex-1 flex-col">
                @csrf
                @method('PATCH')

                <div id="editUserModalBody" class="flex-1 space-y-6 overflow-y-auto p-4 sm:p-5">

                    <div id="editUserErrorSummary" class="hidden rounded border border-rose-200 bg-rose-50 p-3"
                        role="alert" aria-live="assertive" tabindex="-1">

                        <div class="flex items-start gap-2.5">
                            <svg class="mt-0.5 size-5 shrink-0 text-rose-600" aria-hidden="true" fill="none"
                                viewBox="0 0 24 24">

                                <path
                                    d="M12 9v4m0 4h.01M10.3 3.8 2.4 17.5A2 2 0 0 0 4.1 20h15.8a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" />
                            </svg>

                            <div>
                                <p class="text-sm font-semibold text-rose-800">
                                    Data belum dapat disimpan
                                </p>

                                <p id="editUserErrorSummaryMessage" class="mt-0.5 text-xs leading-5 text-rose-700">
                                    Periksa kembali data yang ditandai.
                                </p>
                            </div>
                        </div>
                    </div>

                    @include('admin.users.partials.user-form-fields', [
                        'mode' => 'edit',
                        'formErrors' => $editErrors,
                        'defaultRole' => 'technician',
                        'values' => [],
                    ])
                </div>

                {{-- Footer --}}
                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:justify-end sm:p-5">

                    <button type="button" data-modal-hide="editUserModal"
                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">
                        Batal
                    </button>

                    <button id="editUserSubmitButton" type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">

                        <svg id="editUserSubmitSpinner" class="hidden size-4 animate-spin" aria-hidden="true"
                            fill="none" viewBox="0 0 24 24">

                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />

                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z" />
                        </svg>

                        <span id="editUserSubmitLabel">
                            Simpan perubahan
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const form = document.getElementById('editUserForm');
        const modalElement = document.getElementById('editUserModal');
        const modalBody = document.getElementById('editUserModalBody');
        const modalDescription = document.getElementById(
            'editUserModalDescription'
        );

        const customerFields = document.getElementById(
            'editUserCustomerFields'
        );

        const errorSummary = document.getElementById(
            'editUserErrorSummary'
        );

        const errorSummaryMessage = document.getElementById(
            'editUserErrorSummaryMessage'
        );

        const submitButton = document.getElementById(
            'editUserSubmitButton'
        );

        const submitLabel = document.getElementById(
            'editUserSubmitLabel'
        );

        const submitSpinner = document.getElementById(
            'editUserSubmitSpinner'
        );

        if (
            !form ||
            !modalElement ||
            !modalBody ||
            !modalDescription ||
            !customerFields ||
            !errorSummary ||
            !errorSummaryMessage ||
            !submitButton ||
            !submitLabel ||
            !submitSpinner
        ) {
            return;
        }

        const errorTargets = {
            name: 'edit_user_name',
            username: 'edit_user_username',
            email: 'edit_user_email',
            phone: 'edit_user_phone',
            company_name: 'edit_user_company_name',
            company_phone: 'edit_user_company_phone',
            address: 'edit_user_address',
            password: 'edit_user_password',
        };

        const roleInputs = [
            ...modalElement.querySelectorAll('input[name="role"]'),
        ];

        const customerRequiredInputs = [
            document.getElementById('edit_user_company_name'),
            document.getElementById('edit_user_company_phone'),
            document.getElementById('edit_user_address'),
        ].filter(Boolean);

        const phoneInputs = [
            document.getElementById('edit_user_phone'),
            document.getElementById('edit_user_company_phone'),
        ].filter(Boolean);

        const getInput = (field) => {
            return form.elements.namedItem(field);
        };

        const getErrorContainer = (field) => {
            return form.querySelector(
                `[data-error-for="${field}"]`
            );
        };

        const getFieldTarget = (field) => {
            const targetId = errorTargets[field];

            return targetId ?
                document.getElementById(targetId) :
                null;
        };

        const updateCustomerFields = () => {
            const selectedRole = modalElement.querySelector(
                'input[name="role"]:checked'
            );

            const isCustomer = selectedRole?.value === 'customer';

            customerFields.classList.toggle('hidden', !isCustomer);

            customerRequiredInputs.forEach((input) => {
                input.required = isCustomer;
            });
        };

        const clearFieldInvalidState = (field) => {
            const fieldTarget = getFieldTarget(field);
            const errorContainer = getErrorContainer(field);

            if (errorContainer) {
                errorContainer.replaceChildren();
                errorContainer.classList.add('hidden');
            }

            if (!fieldTarget) {
                return;
            }

            fieldTarget.removeAttribute('aria-invalid');
            fieldTarget.removeAttribute('aria-describedby');

            fieldTarget.classList.remove(
                'border-rose-500',
                'focus:border-rose-500'
            );

            fieldTarget.classList.add(
                'border-slate-300',
                'focus:border-blue-500'
            );
        };

        const clearAllErrors = () => {
            Object.keys(errorTargets).forEach(clearFieldInvalidState);

            errorSummary.classList.add('hidden');
            errorSummaryMessage.textContent =
                'Periksa kembali data yang ditandai.';
        };

        const setFieldInvalidState = (field, messages) => {
            const fieldTarget = getFieldTarget(field);
            const errorContainer = getErrorContainer(field);

            if (errorContainer) {
                errorContainer.replaceChildren();

                messages.forEach((message) => {
                    const item = document.createElement('li');
                    item.textContent = message;
                    errorContainer.appendChild(item);
                });

                errorContainer.classList.remove('hidden');

                if (!errorContainer.id) {
                    errorContainer.id = `edit_user_${field}_error`;
                }
            }

            if (!fieldTarget) {
                return;
            }

            fieldTarget.setAttribute('aria-invalid', 'true');

            if (errorContainer?.id) {
                fieldTarget.setAttribute(
                    'aria-describedby',
                    errorContainer.id
                );
            }

            fieldTarget.classList.remove(
                'border-slate-300',
                'focus:border-blue-500'
            );

            fieldTarget.classList.add(
                'border-rose-500',
                'focus:border-rose-500'
            );
        };

        const scrollAndFocusField = (field, smooth = true) => {
            updateCustomerFields();

            const fieldTarget = getFieldTarget(field);

            if (!fieldTarget) {
                modalBody.scrollTo({
                    top: 0,
                    behavior: smooth ? 'smooth' : 'auto',
                });

                errorSummary.focus({
                    preventScroll: true,
                });

                return;
            }

            fieldTarget.scrollIntoView({
                behavior: smooth ? 'smooth' : 'auto',
                block: 'center',
            });

            window.setTimeout(() => {
                fieldTarget.focus({
                    preventScroll: true,
                });
            }, smooth ? 300 : 0);
        };

        const showValidationErrors = (errors) => {
            clearAllErrors();
            updateCustomerFields();

            const fields = Object.keys(errors);

            fields.forEach((field) => {
                const messages = Array.isArray(errors[field]) ?
                    errors[field] : [errors[field]];

                setFieldInvalidState(field, messages);
            });

            const totalErrors = fields.reduce((total, field) => {
                return total + (
                    Array.isArray(errors[field]) ?
                    errors[field].length :
                    1
                );
            }, 0);

            errorSummaryMessage.textContent =
                totalErrors === 1 ?
                'Ada 1 data yang perlu diperbaiki. Anda akan diarahkan ke bagian tersebut.' :
                `Ada ${totalErrors} data yang perlu diperbaiki. Anda akan diarahkan ke kesalahan pertama.`;

            errorSummary.classList.remove('hidden');

            const firstField = fields[0];

            if (firstField) {
                scrollAndFocusField(firstField);
                return;
            }

            modalBody.scrollTo({
                top: 0,
                behavior: 'smooth',
            });

            errorSummary.focus({
                preventScroll: true,
            });
        };

        const showSystemError = (message) => {
            clearAllErrors();

            errorSummaryMessage.textContent = message;
            errorSummary.classList.remove('hidden');

            modalBody.scrollTo({
                top: 0,
                behavior: 'smooth',
            });

            window.setTimeout(() => {
                errorSummary.focus({
                    preventScroll: true,
                });
            }, 300);
        };

        const setSubmitting = (isSubmitting) => {
            submitButton.disabled = isSubmitting;
            submitSpinner.classList.toggle('hidden', !isSubmitting);
            submitLabel.textContent = isSubmitting ?
                'Menyimpan...' :
                'Simpan perubahan';
        };

        const fillForm = (button) => {
            clearAllErrors();
            form.reset();

            let user = {};

            try {
                user = JSON.parse(button.dataset.user ?? '{}');
            } catch (error) {
                console.error(error);

                showSystemError(
                    'Data pengguna tidak dapat dibaca. Muat ulang halaman dan coba kembali.'
                );

                return;
            }

            form.action = button.dataset.updateUrl ?? '';

            const values = {
                name: user.name ?? '',
                username: user.username ?? '',
                email: user.email ?? '',
                phone: user.phone ?? '',
                company_name: user.company_name ?? '',
                company_phone: user.company_phone ?? '',
                address: user.address ?? '',
                password: '',
            };

            Object.entries(values).forEach(([field, value]) => {
                const input = getInput(field);

                if (input) {
                    input.value = value;
                }
            });

            roleInputs.forEach((input) => {
                input.checked = input.value === user.role;
            });

            modalDescription.textContent =
                user.name ?
                `Perbarui informasi akun ${user.name}.` :
                'Perbarui informasi akun dan data pengguna.';

            updateCustomerFields();

            modalBody.scrollTo({
                top: 0,
                behavior: 'auto',
            });
        };

        document.querySelectorAll('[data-edit-user]').forEach((button) => {
            button.addEventListener('click', () => {
                const dropdownElement = button.closest(
                    '[id^="userActionDropdown"]'
                );

                if (dropdownElement?.id) {
                    const dropdownInstance =
                        window.FlowbiteInstances?.getInstance(
                            'Dropdown',
                            dropdownElement.id
                        );

                    dropdownInstance?.hide();
                }

                fillForm(button);
            });
        });

        phoneInputs.forEach((input) => {
            input.addEventListener('input', () => {
                input.value = input.value
                    .replace(/[^0-9+]/g, '')
                    .replace(/(?!^)\+/g, '');
            });
        });

        form.querySelectorAll('input, textarea').forEach((input) => {
            input.addEventListener('input', () => {
                const field = input.name;

                if (
                    field &&
                    Object.prototype.hasOwnProperty.call(
                        errorTargets,
                        field
                    )
                ) {
                    clearFieldInvalidState(field);
                }
            });
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (submitButton.disabled) {
                return;
            }

            clearAllErrors();
            updateCustomerFields();

            if (!form.action) {
                showSystemError(
                    'Tujuan penyimpanan pengguna tidak ditemukan. Tutup modal lalu coba kembali.'
                );

                return;
            }

            if (!form.checkValidity()) {
                const invalidField = form.querySelector(':invalid');

                if (invalidField) {
                    const field = invalidField.name;

                    scrollAndFocusField(field);

                    window.setTimeout(() => {
                        invalidField.reportValidity();
                    }, 300);
                }

                return;
            }

            setSubmitting(true);

            try {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                let payload = {};

                try {
                    payload = await response.json();
                } catch {
                    payload = {};
                }

                if (response.status === 422) {
                    showValidationErrors(payload.errors ?? {});
                    return;
                }

                if (!response.ok) {
                    showSystemError(
                        payload.message ??
                        'Terjadi kendala saat memperbarui pengguna. Data Anda tetap ada. Silakan coba kembali.'
                    );

                    return;
                }

                window.location.assign(
                    payload.redirect_url ??
                    @json(route('admin.users.index'))
                );
            } catch (error) {
                console.error(error);

                showSystemError(
                    'Tidak dapat terhubung ke server. Periksa koneksi internet, lalu tekan Simpan perubahan kembali.'
                );
            } finally {
                setSubmitting(false);
            }
        });

        updateCustomerFields();
    });
</script>
