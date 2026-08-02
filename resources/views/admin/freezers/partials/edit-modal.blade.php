<div id="editFreezerModal" tabindex="-1" aria-hidden="true" aria-labelledby="editFreezerModalTitle"
    class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">

    <div class="relative max-h-full w-full max-w-3xl p-4">
        <div
            class="relative flex max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 md:p-5">
                <div>
                    <h2 id="editFreezerModalTitle" class="text-lg font-semibold text-slate-900">
                        Edit freezer
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Perbarui informasi administratif unit
                        <span id="editFreezerCode" class="font-medium text-slate-700"></span>.
                    </p>
                </div>

                <button type="button" data-modal-hide="editFreezerModal"
                    class="ms-auto inline-flex size-9 shrink-0 items-center justify-center rounded bg-transparent text-slate-400 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">
                    <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>

                    <span class="sr-only">Tutup modal</span>
                </button>
            </div>

            <form id="editFreezerForm" action="" method="POST" enctype="multipart/form-data"
                class="flex min-h-0 flex-1 flex-col">

                @csrf
                @method('PATCH')

                <input id="editing_freezer_id" name="editing_freezer_id" type="hidden"
                    value="{{ old('editing_freezer_id') }}">
                <div class="flex-1 space-y-6 overflow-y-auto p-4 sm:p-5">
                    <section class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Dokumentasi Unit
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Perbarui dokumentasi foto aset freezer.
                            </p>
                        </div>

                        <div class="relative">
                            <label for="editCustomerSearch" class="mb-2 block text-sm font-medium text-slate-900">
                                Pelanggan atau perusahaan
                                <span class="text-red-600">*</span>
                            </label>

                            <input id="edit_customer_id" name="customer_id" type="hidden"
                                value="{{ old('customer_id') }}">

                            <div class="relative">
                                <svg class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"
                                    aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor"
                                        stroke-width="2" />
                                    <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="2" />
                                </svg>

                                <input id="editCustomerSearch" type="search" autocomplete="off"
                                    placeholder="Cari pelanggan atau perusahaan"
                                    class="block w-full rounded border border-slate-300 bg-white p-2.5 ps-9 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div id="editCustomerDropdown"
                                class="absolute inset-x-0 top-full z-50 mt-1 hidden max-h-60 overflow-y-auto rounded border border-slate-200 bg-white p-1 shadow-lg">
                                @forelse (($customers ?? []) as $customer)
                                    <button type="button" data-edit-customer-option
                                        data-customer-id="{{ $customer->id }}"
                                        data-customer-name="{{ $customer->company_name }}"
                                        class="block w-full rounded px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100">
                                        {{ $customer->company_name }}
                                    </button>
                                @empty
                                    <p class="px-3 py-2 text-sm text-slate-500">
                                        Data pelanggan belum tersedia.
                                    </p>
                                @endforelse

                                <p id="editCustomerEmptyState" class="hidden px-3 py-2 text-sm text-slate-500">
                                    Pelanggan tidak ditemukan.
                                </p>
                            </div>
                        </div>
                    </section>

                    <div class="border-t border-slate-200"></div>

                    <section class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Identitas freezer
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Masukkan informasi yang tertera pada unit atau dokumen aset.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="edit_brand" class="mb-2 block text-sm font-medium text-slate-900">
                                    Merek
                                    <span class="text-red-600">*</span>
                                </label>

                                <input id="edit_brand" name="brand" type="text" required
                                    placeholder="Contoh: Modena"
                                    class="block w-full rounded border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="edit_model" class="mb-2 block text-sm font-medium text-slate-900">
                                    Model
                                    <span class="text-red-600">*</span>
                                </label>

                                <input id="edit_model" name="model" type="text" required
                                    placeholder="Contoh: MD-320"
                                    class="block w-full rounded border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="edit_serial_number" class="mb-2 block text-sm font-medium text-slate-900">
                                    Nomor seri
                                    <span class="font-normal text-slate-500">(opsional)</span>
                                </label>

                                <input id="edit_serial_number" name="serial_number" type="text"
                                    placeholder="Masukkan nomor seri unit jika tersedia"
                                    class="block w-full rounded border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="edit_capacity_liter"
                                    class="mb-2 block text-sm font-medium text-slate-900">
                                    Kapasitas
                                </label>

                                <div class="relative">
                                    <input id="edit_capacity_liter" name="capacity_liter" type="number"
                                        min="1" inputmode="numeric" placeholder="320"
                                        class="block w-full rounded border border-slate-300 bg-slate-50 p-2.5 pe-14 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">

                                    <span
                                        class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-sm text-slate-500">
                                        liter
                                    </span>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_estimated_age" class="mb-2 block text-sm font-medium text-slate-900">
                                    Perkiraan usia
                                </label>

                                <select id="edit_estimated_age" name="estimated_age"
                                    class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih perkiraan usia</option>
                                    <option value="<1 tahun">&lt; 1 tahun</option>
                                    <option value="1-3 tahun">1–3 tahun</option>
                                    <option value=">3 tahun">&gt; 3 tahun</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <div class="border-t border-slate-200"></div>

                    <section class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Informasi awal
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Catat keluhan dan dokumentasi awal freezer.
                            </p>
                        </div>

                        <div>
                            <label for="edit_photo" class="mb-2 block text-sm font-medium text-slate-900">
                                Foto freezer
                            </label>

                            <div id="editPhotoDropzone"
                                class="relative mt-3 flex min-h-40 w-full cursor-pointer overflow-hidden rounded border-2 border-dashed border-slate-300 bg-slate-50 text-center transition hover:bg-slate-100">

                                {{-- Kondisi ketika belum ada foto --}}
                                <div id="editPhotoPlaceholder"
                                    class="flex w-full flex-col items-center justify-center px-4 py-6">

                                    <svg class="mb-2 size-7 text-slate-500" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <path d="M12 16V4m0 0-4 4m4-4 4 4M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    <p class="text-sm text-slate-600">
                                        <span class="font-semibold text-blue-700">
                                            Pilih foto baru
                                        </span>
                                        atau seret dan lepas
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Kosongkan jika foto tidak ingin diubah
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        JPG, PNG, atau WebP · maksimal 2 MB
                                    </p>
                                </div>

                                {{-- Kondisi ketika foto tersedia --}}
                                <div id="editPhotoPreviewWrapper" class="relative hidden w-full bg-slate-100">

                                    <div class="flex min-h-40 max-h-[28rem] w-full items-center justify-center p-3">

                                        <img id="editPhotoPreview" src="" alt="Preview foto freezer"
                                            class="max-h-[26rem] max-w-full rounded object-contain">
                                    </div>

                                    <div
                                        class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-slate-950/70 px-3 py-2 text-left text-white">

                                        <div class="min-w-0 flex-1">
                                            <p id="editPhotoFileName" class="truncate text-xs font-medium">
                                            </p>

                                            <p id="editPhotoStatus" class="mt-0.5 text-[11px] text-slate-200">
                                                Foto freezer saat ini
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded bg-white/15 px-2 py-1 text-[11px] font-medium">
                                            Ganti foto
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <input id="edit_photo" name="photo" type="file"
                                accept="image/jpeg,image/png,image/webp" class="hidden">

                            <x-form-error :messages="$errors->updateFreezer->get('photo')" />
                        </div>
                    </section>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:justify-end sm:p-5">
                    <button type="button" data-modal-hide="editFreezerModal"
                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">
                        Batal
                    </button>

                    <button type="submit"
                        class="w-full rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const search = document.getElementById('editCustomerSearch');
        const dropdown = document.getElementById('editCustomerDropdown');
        const customerId = document.getElementById('edit_customer_id');
        const options = [...document.querySelectorAll('[data-edit-customer-option]')];
        const emptyState = document.getElementById('editCustomerEmptyState');

        const form = document.getElementById('editFreezerForm');
        const editingFreezerId = document.getElementById('editing_freezer_id');
        const freezerCode = document.getElementById('editFreezerCode');
        const brand = document.getElementById('edit_brand');
        const model = document.getElementById('edit_model');
        const serialNumber = document.getElementById('edit_serial_number');
        const capacityLiter = document.getElementById('edit_capacity_liter');
        const estimatedAge = document.getElementById('edit_estimated_age');

        const photoInput = document.getElementById('edit_photo');
        const photoDropzone = document.getElementById('editPhotoDropzone');
        const photoPlaceholder = document.getElementById('editPhotoPlaceholder');
        const photoPreviewWrapper = document.getElementById(
            'editPhotoPreviewWrapper'
        );
        const photoPreview = document.getElementById('editPhotoPreview');
        const photoFileName = document.getElementById('editPhotoFileName');
        const photoStatus = document.getElementById('editPhotoStatus');

        const editButtons = [...document.querySelectorAll('[data-edit-freezer]')];

        if (
            !search ||
            !dropdown ||
            !customerId ||
            !form ||
            !editingFreezerId ||
            !freezerCode ||
            !brand ||
            !model ||
            !serialNumber ||
            !capacityLiter ||
            !estimatedAge ||
            !photoInput ||
            !photoDropzone ||
            !photoPlaceholder ||
            !photoPreviewWrapper ||
            !photoPreview ||
            !photoFileName ||
            !photoStatus
        ) {
            return;
        }

        const filterOptions = () => {
            const query = search.value.trim().toLowerCase();
            let visibleCount = 0;

            options.forEach((option) => {
                const visible = option.dataset.customerName
                    .toLowerCase()
                    .includes(query);

                option.classList.toggle('hidden', !visible);
                visibleCount += visible ? 1 : 0;
            });

            emptyState?.classList.toggle(
                'hidden',
                visibleCount > 0 || options.length === 0
            );
        };

        search.addEventListener('focus', () => {
            dropdown.classList.remove('hidden');
            filterOptions();
        });

        search.addEventListener('input', () => {
            customerId.value = '';
            dropdown.classList.remove('hidden');
            filterOptions();
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                customerId.value = option.dataset.customerId;
                search.value = option.dataset.customerName;
                dropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', (event) => {
            if (!search.parentElement.parentElement.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        let temporaryPreviewUrl = null;

        const clearTemporaryPreviewUrl = () => {
            if (temporaryPreviewUrl !== null) {
                URL.revokeObjectURL(temporaryPreviewUrl);
                temporaryPreviewUrl = null;
            }
        };

        const showPhotoPlaceholder = () => {
            clearTemporaryPreviewUrl();

            photoPreview.removeAttribute('src');
            photoFileName.textContent = '';
            photoStatus.textContent = '';

            photoPreviewWrapper.classList.add('hidden');
            photoPlaceholder.classList.remove('hidden');
        };

        const showPhotoPreview = (
            source,
            fileName,
            statusText,
        ) => {
            clearTemporaryPreviewUrl();

            if (!source) {
                showPhotoPlaceholder();

                return;
            }

            photoPreview.src = source;
            photoFileName.textContent = fileName || 'Foto freezer';
            photoStatus.textContent = statusText;

            photoPlaceholder.classList.add('hidden');
            photoPreviewWrapper.classList.remove('hidden');
        };

        const showSelectedPhoto = (file) => {
            clearTemporaryPreviewUrl();

            if (!file) {
                return;
            }

            temporaryPreviewUrl = URL.createObjectURL(file);

            photoPreview.src = temporaryPreviewUrl;
            photoFileName.textContent = file.name;
            photoStatus.textContent = 'Foto baru yang akan disimpan';

            photoPlaceholder.classList.add('hidden');
            photoPreviewWrapper.classList.remove('hidden');
        };

        const fillEditForm = (button) => {
            form.action = button.dataset.updateUrl;

            editingFreezerId.value = button.dataset.freezerId ?? '';
            freezerCode.textContent = button.dataset.freezerCode ?? '';

            customerId.value = button.dataset.customerId ?? '';
            search.value = button.dataset.customerName ?? '';

            brand.value = button.dataset.brand ?? '';
            model.value = button.dataset.model ?? '';
            serialNumber.value = button.dataset.serialNumber ?? '';
            capacityLiter.value = button.dataset.capacityLiter ?? '';
            estimatedAge.value = button.dataset.estimatedAge ?? '';

            photoInput.value = '';

            const photoUrl = button.dataset.photoUrl ?? '';
            const photoName = button.dataset.photoName ?? '';

            showPhotoPreview(
                photoUrl,
                photoName,
                'Foto freezer saat ini',
            );
        };

        editButtons.forEach((button) => {
            button.addEventListener('click', () => {
                fillEditForm(button);

                const dropdownElement = button.closest(
                    '[id^="freezerActionsDropdown-"]'
                );

                if (!dropdownElement) {
                    return;
                }

                const dropdownInstance = window.FlowbiteInstances?.getInstance(
                    'Dropdown',
                    dropdownElement.id
                );

                if (dropdownInstance) {
                    dropdownInstance.hide();

                    return;
                }

                dropdownElement.classList.add('hidden');
            });
        });

        const allowedPhotoTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (
            photoInput &&
            photoDropzone &&
            photoPlaceholder &&
            photoPreviewWrapper &&
            photoPreview &&
            photoFileName &&
            photoStatus
        ) {
            photoDropzone.addEventListener('click', () => {
                photoInput.click();
            });

            photoInput.addEventListener('change', () => {
                const file = photoInput.files[0] ?? null;

                if (!file) {
                    return;
                }

                showSelectedPhoto(file);
            });

            photoDropzone.addEventListener('dragover', (event) => {
                event.preventDefault();

                photoDropzone.classList.add(
                    'border-blue-500',
                    'bg-blue-50',
                );
            });

            photoDropzone.addEventListener('dragleave', (event) => {
                if (photoDropzone.contains(event.relatedTarget)) {
                    return;
                }

                photoDropzone.classList.remove(
                    'border-blue-500',
                    'bg-blue-50',
                );
            });

            photoDropzone.addEventListener('drop', (event) => {
                event.preventDefault();

                photoDropzone.classList.remove(
                    'border-blue-500',
                    'bg-blue-50',
                );

                const file = event.dataTransfer.files[0] ?? null;

                if (
                    !file ||
                    !allowedPhotoTypes.includes(file.type)
                ) {
                    return;
                }

                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);
                photoInput.files = dataTransfer.files;

                showSelectedPhoto(file);
            });
        }
    });
</script>
