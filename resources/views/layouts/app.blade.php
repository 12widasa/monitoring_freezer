<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Karyatama System')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 antialiased">
    <!-- Navigation Bar Header -->
    <nav
        class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
        <div class="flex flex-wrap justify-between items-center">
            <div class="flex justify-start items-center">
                <a href="#" class="flex items-center justify-between mr-4">
                    <span class="self-center text-xl font-bold whitespace-nowrap dark:text-white">Karyatama</span>
                </a>
            </div>
            <div class="flex items-center lg:order-2">
                <span class="text-sm font-medium text-gray-900 dark:text-white mr-4">
                    {{ Auth::user()->name }} ({{ Auth::user()->role->label() }})
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:underline font-semibold">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="p-4 pt-20 max-w-7xl mx-auto">
        @yield('content')
    </main>
</body>

</html>
