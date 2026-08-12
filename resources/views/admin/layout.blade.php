<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - Karyatama System')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen antialiased">

    <!-- Include Sidebar khusus Admin -->
    @include('admin.partials.sidebar')

    <!-- Main Content Wrapper (Memberi margin-left 64 agar tidak tertutup sidebar di layar sm ke atas) -->
    <div class="p-4 sm:ml-64 min-h-screen flex flex-col justify-between">

        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
            type="button"
            class="mb-4 inline-flex items-center rounded-lg border border-slate-200 bg-white p-2 text-sm text-slate-500 shadow-sm hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 sm:hidden">
            <span class="sr-only">Buka sidebar</span>

            <svg class="size-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                <path clip-rule="evenodd" fill-rule="evenodd"
                    d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" />
            </svg>
        </button>

        <!-- Dynamic Main Content Section -->
        <main class="flex-1 space-y-4">
            <x-flash-message />

            @yield('content')
        </main>

        <!-- Footer Area -->
        <footer class="mt-6 pt-4 border-t border-slate-200/60 text-center text-xs text-slate-400">
            © CV. Karyatama Agung Abadi — Sistem Monitoring Reparasi Freezer
        </footer>

    </div>

    <script>
        window.addEventListener('pageshow', () => {
            document
                .querySelectorAll('[data-dropdown-toggle]')
                .forEach((trigger) => {
                    const targetId = trigger.dataset.dropdownToggle;
                    const target = document.getElementById(targetId);

                    target?.classList.add('hidden');
                });
        });
    </script>

</body>

</html>
