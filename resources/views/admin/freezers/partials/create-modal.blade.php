@php
    $createErrors = $errors->createFreezer;
    $selectedCustomer = collect($customers ?? [])->firstWhere('id', (int) old('customer_id'));
@endphp

<div id="createFreezerModal" tabindex="-1" aria-hidden="true" aria-labelledby="createFreezerModalTitle"
    class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">

    <div class="relative max-h-full w-full max-w-3xl p-4">
        <div
            class="relative flex max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 md:p-5">
                <div>
                    <h2 id="createFreezerModalTitle" class="text-lg font-semibold text-slate-900">
                        Tambah freezer
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Daftarkan administrasi unit sebelum tiba dan diverifikasi di bengkel.
                    </p>
                </div>

                <button type="button" data-modal-hide="createFreezerModal"
                    class="ms-auto inline-flex size-9 shrink-0 items-center justify-center rounded bg-transparent text-slate-400 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200">
                    <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>

                    <span class="sr-only">Tutup modal</span>
                </button>
            </div>

            <form id="createFreezerForm"
                action="{{ route(
                    'admin.freezers.store',
                    request()->only(['search', 'repair_status', 'verification_status', 'sort', 'per_page', 'page']),
                ) }}"
                method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                @csrf
                <div class="flex-1 space-y-6 overflow-y-auto p-4 sm:p-5">
                    <section class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Pemilik freezer
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Pilih perusahaan yang tercatat sebagai pemilik aset.
                            </p>
                        </div>

                        <div class="relative">
                            <label for="customerSearch" class="mb-2 block text-sm font-medium text-slate-900">
                                Pelanggan atau perusahaan
                                <span class="text-red-600">*</span>
                            </label>

                            <input id="customer_id" name="customer_id" type="hidden" value="{{ old('customer_id') }}">

                            <div class="relative">
                                <svg class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"
                                    aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor"
                                        stroke-width="2" />
                                    <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round"
                                        stroke-width="2" />
                                </svg>

                                <input id="customerSearch" type="search" autocomplete="off"
                                    value="{{ $selectedCustomer?->company_name }}"
                                    placeholder="Cari pelanggan atau perusahaan" @class([
                                        'block w-full rounded bg-white p-2.5 ps-9 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                        'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                            'customer_id'),
                                        'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                            'customer_id'),
                                    ])>
                            </div>

                            <div id="customerDropdown"
                                class="absolute inset-x-0 top-full z-50 mt-1 hidden max-h-60 overflow-y-auto rounded border border-slate-200 bg-white p-1 shadow-lg">
                                @forelse (($customers ?? []) as $customer)
                                    <button type="button" data-customer-option data-customer-id="{{ $customer->id }}"
                                        data-customer-name="{{ $customer->company_name }}"
                                        class="block w-full rounded px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100">
                                        {{ $customer->company_name }}
                                    </button>
                                @empty
                                    <p class="px-3 py-2 text-sm text-slate-500">
                                        Data pelanggan belum tersedia.
                                    </p>
                                @endforelse

                                <p id="customerEmptyState" class="hidden px-3 py-2 text-sm text-slate-500">
                                    Pelanggan tidak ditemukan.
                                </p>
                            </div>

                            <x-form-error :messages="$createErrors->get('customer_id')" />
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
                                <label for="brand" class="mb-2 block text-sm font-medium text-slate-900">
                                    Merek
                                    <span class="text-red-600">*</span>
                                </label>

                                <input id="brand" name="brand" type="text" required value="{{ old('brand') }}"
                                    placeholder="Contoh: Modena" @class([
                                        'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                        'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                            'brand'),
                                        'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                            'brand'),
                                    ])>

                                <x-form-error :messages="$createErrors->get('brand')" />
                            </div>

                            <div>
                                <label for="model" class="mb-2 block text-sm font-medium text-slate-900">
                                    Model
                                    <span class="text-red-600">*</span>
                                </label>

                                <input id="model" name="model" type="text" required value="{{ old('model') }}"
                                    placeholder="Contoh: MD-320" @class([
                                        'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                        'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                            'model'),
                                        'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                            'model'),
                                    ])>

                                <x-form-error :messages="$createErrors->get('model')" />
                            </div>

                            <div>
                                <label for="serial_number" class="mb-2 block text-sm font-medium text-slate-900">
                                    Nomor seri
                                    <span class="font-normal text-slate-500">(opsional)</span>
                                </label>

                                <input id="serial_number" name="serial_number" type="text"
                                    value="{{ old('serial_number') }}"
                                    placeholder="Masukkan nomor seri unit jika tersedia" @class([
                                        'block w-full rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                        'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                            'serial_number'),
                                        'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                            'serial_number'),
                                    ])>

                                <x-form-error :messages="$createErrors->get('serial_number')" />
                            </div>

                            <div>
                                <label for="capacity_liter" class="mb-2 block text-sm font-medium text-slate-900">
                                    Kapasitas
                                </label>

                                <div class="relative">
                                    <input id="capacity_liter" name="capacity_liter" type="number" min="1"
                                        inputmode="numeric" value="{{ old('capacity_liter') }}" placeholder="320"
                                        @class([
                                            'block w-full rounded bg-slate-50 p-2.5 pe-14 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                            'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                                'capacity_liter'),
                                            'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                                'capacity_liter'),
                                        ])>

                                    <span
                                        class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-sm text-slate-500">
                                        liter
                                    </span>
                                </div>

                                <x-form-error :messages="$createErrors->get('capacity_liter')" />
                            </div>

                            <div class="md:col-span-2">
                                <label for="estimated_age" class="mb-2 block text-sm font-medium text-slate-900">
                                    Perkiraan usia
                                </label>

                                <select id="estimated_age" name="estimated_age"
                                    class="block w-full rounded border border-slate-300 bg-white p-2.@class([
                                        'block w-full rounded bg-white p-2.5 text-sm text-slate-900 focus:ring-blue-500',
                                        'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                            'estimated_age'),
                                        'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                            'estimated_age'),
                                    ])5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih perkiraan usia</option>

                                    <option value="<1 tahun" @selected(old('estimated_age') === '<1 tahun')>
                                        &lt; 1 tahun
                                    </option>

                                    <option value="1-3 tahun" @selected(old('estimated_age') === '1-3 tahun')>
                                        1–3 tahun
                                    </option>

                                    <option value=">3 tahun" @selected(old('estimated_age') === '>3 tahun')>
                                        &gt; 3 tahun
                                    </option>
                                </select>

                                <x-form-error :messages="$createErrors->get('estimated_age')" />
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
                            <label for="complaint_note" class="mb-2 block text-sm font-medium text-slate-900">
                                Keluhan awal
                            </label>

                            <textarea id="complaint_note" name="complaint_note" rows="4"
                                placeholder="Jelaskan keluhan atau kondisi awal freezer" @class([
                                    'block w-full resize-y rounded bg-slate-50 p-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-blue-500',
                                    'border border-slate-300 focus:border-blue-500' => !$createErrors->has(
                                        'complaint_note'),
                                    'border border-rose-500 focus:border-rose-500' => $createErrors->has(
                                        'complaint_note'),
                                ])>{{ old('complaint_note') }}</textarea>

                            <x-form-error :messages="$createErrors->get('complaint_note')" />
                        </div>

                        <div>
                            <label for="photo" class="mb-2 block text-sm font-medium text-slate-900">
                                Foto freezer
                            </label>

                            <div id="photoDropzone"
                                class="relative flex min-h-40 w-full cursor-pointer overflow-hidden rounded border-2 border-dashed border-slate-300 bg-slate-50 text-center transition hover:bg-slate-100">

                                {{-- Kondisi sebelum foto dipilih --}}
                                <div id="photoPlaceholder"
                                    class="flex w-full flex-col items-center justify-center px-4 py-6">

                                    <svg class="mb-3 size-8 text-slate-500" aria-hidden="true" fill="none"
                                        viewBox="0 0 24 24">
                                        <path d="M12 16V4m0 0-4 4m4-4 4 4M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" />
                                    </svg>

                                    <p class="text-sm text-slate-600">
                                        <span class="font-semibold text-blue-700">
                                            Klik untuk memilih foto
                                        </span>
                                        atau seret dan lepas
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        JPG, PNG, atau WebP · maksimal 1 foto
                                    </p>
                                </div>

                                {{-- Kondisi setelah foto dipilih --}}
                                <div id="photoPreviewWrapper" class="relative hidden w-full bg-slate-100">

                                    <div class="flex min-h-40 max-h-[28rem] w-full items-center justify-center p-3">

                                        <img id="photoPreview" src="" alt="Preview foto freezer"
                                            class="max-h-[26rem] max-w-full rounded object-contain">
                                    </div>

                                    <div
                                        class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-slate-950/70 px-3 py-2 text-left text-white">

                                        <div class="min-w-0 flex-1">
                                            <p id="photoFileName" class="truncate text-xs font-medium">
                                            </p>

                                            <p class="mt-0.5 text-[11px] text-slate-200">
                                                Foto baru yang akan disimpan
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded bg-white/15 px-2 py-1 text-[11px] font-medium">
                                            Ganti foto
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <input id="photo" name="photo" type="file"
                                accept="image/jpeg,image/png,image/webp" class="hidden">

                            <x-form-error :messages="$createErrors->get('photo')" />

                            @if ($createErrors->has('photo'))
                                <p class="mt-1 text-xs text-slate-500">
                                    Pilih ulang foto setelah memperbaiki data form.
                                </p>
                            @endif


                        </div>
                    </section>

                    <div class="flex gap-3 rounded border border-blue-200 bg-blue-50 p-4 text-blue-800">
                        <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="2" />
                            <path stroke-linecap="round" stroke-width="2" d="M12 11v5m0-8h.01" />
                        </svg>

                        <div>
                            <p class="text-sm font-medium">
                                Status awal: Menunggu kedatangan
                            </p>

                            <p class="mt-1 text-xs leading-5 text-blue-700">
                                Verifikasi dilakukan admin setelah nomor seri unit fisik dicocokkan.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:justify-end sm:p-5">
                    <button type="button" data-modal-hide="createFreezerModal"
                        class="w-full rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">
                        Batal
                    </button>

                    <button type="submit"
                        class="w-full rounded bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:w-auto">
                        Simpan freezer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($createErrors->any())
            window.setTimeout(() => {
                document
                    .querySelector('[data-modal-target="createFreezerModal"]')
                    ?.click();
            }, 0);
        @endif
        const search = document.getElementById('customerSearch');
        const dropdown = document.getElementById('customerDropdown');
        const customerId = document.getElementById('customer_id');
        const options = [...document.querySelectorAll('[data-customer-option]')];
        const emptyState = document.getElementById('customerEmptyState');

        if (!search || !dropdown || !customerId) {
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

        const photoInput = document.getElementById('photo');
        const photoDropzone = document.getElementById('photoDropzone');
        const photoPlaceholder = document.getElementById('photoPlaceholder');
        const photoPreviewWrapper = document.getElementById('photoPreviewWrapper');
        const photoPreview = document.getElementById('photoPreview');
        const photoFileName = document.getElementById('photoFileName');

        if (
            photoInput &&
            photoDropzone &&
            photoPlaceholder &&
            photoPreviewWrapper &&
            photoPreview &&
            photoFileName
        ) {
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            let previewUrl = null;

            const showSelectedFile = (file) => {
                if (previewUrl !== null) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                if (!file) {
                    photoPreview.removeAttribute('src');
                    photoFileName.textContent = '';

                    photoPreviewWrapper.classList.add('hidden');
                    photoPlaceholder.classList.remove('hidden');

                    return;
                }

                previewUrl = URL.createObjectURL(file);

                photoPreview.src = previewUrl;
                photoFileName.textContent = file.name;

                photoPlaceholder.classList.add('hidden');
                photoPreviewWrapper.classList.remove('hidden');
            };

            photoDropzone.addEventListener('click', () => {
                photoInput.click();
            });

            photoInput.addEventListener('change', () => {
                showSelectedFile(photoInput.files[0] ?? null);
            });

            photoDropzone.addEventListener('dragover', (event) => {
                event.preventDefault();

                photoDropzone.classList.add(
                    'border-blue-500',
                    'bg-blue-50',
                );
            });

            photoDropzone.addEventListener('dragleave', () => {
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

                if (!file || !allowedTypes.includes(file.type)) {
                    return;
                }

                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(file);
                photoInput.files = dataTransfer.files;

                showSelectedFile(file);
            });
        }
    });
</script>
