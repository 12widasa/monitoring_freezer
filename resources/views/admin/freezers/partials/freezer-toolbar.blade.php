<form action="{{ route('admin.freezers.index') }}" method="GET"
    class="grid gap-3 border-b-[1.6px] border-slate-100 p-4 md:grid-cols-2 xl:grid-cols-[minmax(280px,1fr)_170px_180px_190px_auto]">

    <input type="hidden" name="per_page" value="{{ $perPage }}">

    {{-- Pencarian --}}
    <div class="relative md:col-span-2 xl:col-span-1">
        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
            <svg class="size-4 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />

                <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
            </svg>
        </div>

        <input type="search" id="freezer-search" name="search" value="{{ $search }}" autocomplete="off"
            placeholder="Cari kode, nomor seri, merek, model, atau pemilik"
            class="block w-full rounded border border-slate-300 bg-white p-2.5 ps-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
    </div>

    {{-- Status reparasi --}}
    <div>
        <label for="repair-status" class="sr-only">
            Status reparasi
        </label>

        <select id="repair-status" name="repair_status" onchange="this.form.submit()"
            class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

            <option value="">Semua reparasi</option>

            @foreach (\App\Enums\RepairStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected($selectedRepairStatus === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status verifikasi --}}
    <div>
        <label for="verification-status" class="sr-only">
            Status verifikasi
        </label>

        <select id="verification-status" name="verification_status" onchange="this.form.submit()"
            class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

            <option value="">Semua verifikasi</option>

            @foreach (\App\Enums\VerificationStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected($selectedVerificationStatus === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Urutan --}}
    <div>
        <label for="freezer-sort" class="sr-only">
            Urutan
        </label>

        <select id="freezer-sort" name="sort" onchange="this.form.submit()"
            class="block w-full rounded border border-slate-300 bg-white p-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

            <option value="latest" @selected($selectedSort === 'latest')>
                Terbaru ditambahkan
            </option>

            <option value="oldest" @selected($selectedSort === 'oldest')>
                Terlama ditambahkan
            </option>

            <option value="updated" @selected($selectedSort === 'updated')>
                Terakhir diperbarui
            </option>

            <option value="owner_asc" @selected($selectedSort === 'owner_asc')>
                Nama pemilik A–Z
            </option>
        </select>
    </div>

    {{-- Hapus filter --}}
    <a href="{{ route('admin.freezers.index', ['per_page' => $perPage]) }}"
        class="inline-flex items-center justify-center gap-2 rounded px-3 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100 xl:justify-start">

        <svg class="size-4 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
            <path d="M4 5h16l-6.5 7.5V18l-3 1.5v-7L4 5Z" stroke="currentColor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" />

            <path d="m17 17 4 4m0-4-4 4" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
        </svg>

        Hapus filter
    </a>
</form>
