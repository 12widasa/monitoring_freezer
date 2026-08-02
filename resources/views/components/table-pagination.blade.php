@props([
    'paginator',
    'action',
    'perPage' => 10,
    'perPageOptions' => [10, 25, 50],
    'query' => [],
    'itemLabel' => 'data',
    'ariaLabel' => 'Pagination',
])

<nav class="flex flex-col items-center gap-3 border-t border-slate-200 bg-white px-4 py-3 sm:px-5 xl:flex-row xl:justify-between"
    aria-label="{{ $ariaLabel }}">

    <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:text-left">
        <form action="{{ $action }}" method="GET">
            @foreach ($query as $name => $value)
                @if ($value !== null && $value !== '')
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endif
            @endforeach

            <label>
                <span class="sr-only">
                    Jumlah {{ $itemLabel }} per halaman
                </span>

                <select name="per_page" onchange="this.form.submit()"
                    class="rounded border border-slate-300 bg-white p-2.5 pe-9 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected((int) $perPage === (int) $option)>
                            {{ $option }} per halaman
                        </option>
                    @endforeach
                </select>
            </label>
        </form>

        <p class="text-sm text-slate-500">
            @if ($paginator->total() > 0)
                Menampilkan

                <span class="font-semibold text-slate-900">
                    {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
                </span>

                dari

                <span class="font-semibold text-slate-900">
                    {{ $paginator->total() }}
                </span>
            @else
                Tidak ada {{ $itemLabel }} untuk ditampilkan
            @endif
        </p>
    </div>

    <div class="w-full xl:w-auto">
        <ul class="flex flex-wrap items-center justify-center gap-1.5 text-sm">
            <li>
                @if ($paginator->onFirstPage())
                    <span
                        class="inline-flex cursor-not-allowed items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-400">

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>

                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-100">

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>

                        Sebelumnya
                    </a>
                @endif
            </li>

            @for ($page = 1; $page <= max(1, $paginator->lastPage()); $page++)
                <li>
                    @if ($page === $paginator->currentPage())
                        <span aria-current="page"
                            class="flex size-9 items-center justify-center rounded bg-blue-700 font-medium text-white">

                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                            class="flex size-9 items-center justify-center rounded border border-slate-200 bg-white font-medium text-slate-600 hover:bg-slate-100">

                            {{ $page }}
                        </a>
                    @endif
                </li>
            @endfor

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-100">

                        Berikutnya

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" />
                        </svg>
                    </a>
                @else
                    <span
                        class="inline-flex cursor-not-allowed items-center gap-1 rounded border border-slate-200 bg-white px-3 py-2 font-medium text-slate-400">

                        Berikutnya

                        <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" />
                        </svg>
                    </span>
                @endif
            </li>
        </ul>
    </div>
</nav>
