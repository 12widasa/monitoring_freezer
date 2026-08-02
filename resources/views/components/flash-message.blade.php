@php
    $messages = [
        'success' => [
            'title' => 'Berhasil.',
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'button' => 'text-emerald-700 hover:bg-emerald-100 focus:ring-emerald-300',
        ],
        'error' => [
            'title' => 'Gagal.',
            'wrapper' => 'border-rose-200 bg-rose-50 text-rose-800',
            'button' => 'text-rose-700 hover:bg-rose-100 focus:ring-rose-300',
        ],
        'warning' => [
            'title' => 'Perhatian.',
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-800',
            'button' => 'text-amber-700 hover:bg-amber-100 focus:ring-amber-300',
        ],
    ];
@endphp

@foreach ($messages as $type => $config)
    @if (session($type))
        <div id="admin-{{ $type }}-alert" data-auto-dismiss="4000"
            class="flex items-start gap-3 rounded border p-4 text-sm {{ $config['wrapper'] }}" role="alert">

            @if ($type === 'success')
                <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" />
                </svg>
            @elseif ($type === 'error')
                <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />

                    <path d="M12 7v6M12 17h.01" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
            @else
                <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"
                        stroke="currentColor" stroke-linejoin="round" stroke-width="2" />

                    <path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
            @endif

            <div class="min-w-0 flex-1">
                <span class="font-medium">
                    {{ $config['title'] }}
                </span>

                {{ session($type) }}
            </div>

            <button type="button" data-dismiss-target="#admin-{{ $type }}-alert" aria-label="Tutup"
                class="-m-1 inline-flex size-7 shrink-0 items-center justify-center rounded focus:outline-none focus:ring-2 {{ $config['button'] }}">

                <span class="sr-only">Tutup</span>

                <svg class="size-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
            </button>
        </div>
    @endif
@endforeach

<script>
    document.querySelectorAll('[data-auto-dismiss]').forEach((alertElement) => {
        const delay = Number(alertElement.dataset.autoDismiss);
        const dismissButton = alertElement.querySelector('[data-dismiss-target]');

        if (!Number.isFinite(delay) || !dismissButton) {
            return;
        }

        window.setTimeout(() => {
            if (alertElement.isConnected) {
                dismissButton.click();
            }
        }, delay);
    });
</script>
