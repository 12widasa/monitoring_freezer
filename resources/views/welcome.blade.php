<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Testing Component Flowbite - {{ config('app.name', 'Laravel') }}</title>

    @fonts

    <!-- Styles / Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white antialiased min-h-screen">

    <!-- KETERANGAN: Testing Component 1 - Flowbite Navbar & Dark Mode Trigger -->
    <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <a href="#" class="flex items-center">
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Monitoring
                    Freezer</span>
            </a>
            <div class="flex items-center gap-2">
                <span
                    class="text-xs bg-blue-100 text-blue-800 font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    Flowbite v4 Active
                </span>
            </div>
        </div>
    </nav>

    <main class="max-w-screen-xl mx-auto p-6 space-y-8">

        <header class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl font-bold">Pengujian Komponen Interaktif Flowbite</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gunakan halaman ini untuk memverifikasi CSS Tailwind v4
                dan JavaScript Flowbite berjalan sempurna.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- KETERANGAN: Testing Component 2 - Flowbite Card & Tooltip (Menguji JS Popover/Tooltip) -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-lg font-bold tracking-tight">1. Tooltip & Button Test</h5>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Arahkan kursor (*hover*) ke tombol di bawah
                    untuk menguji apakah skrip JS Flowbite berhasil membaca atribut data.</p>

                <button data-tooltip-target="tooltip-test" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Hover Saya
                </button>

                <div id="tooltip-test" role="tooltip"
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                    [Pasti] JS Flowbite Berfungsi!
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>

            <!-- KETERANGAN: Testing Component 3 - Flowbite Modal (Menguji JS Data-Toggle & Backdrop) -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-lg font-bold tracking-tight">2. Interactive Modal Test</h5>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Klik tombol di bawah untuk membuka modal
                    dialog. Ini menguji event handler modal bawaan Flowbite.</p>

                <button data-modal-target="default-modal" data-modal-toggle="default-modal"
                    class="block text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    type="button">
                    Buka Modal Dialog
                </button>
            </div>

        </div>

        <!-- KETERANGAN: Testing Component 4 - Flowbite Form Input & Select -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <h5 class="mb-4 text-lg font-bold tracking-tight">3. Form Input Styling Test</h5>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" onsubmit="event.preventDefault();">
                <div>
                    <label for="freezer_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                        Freezer</label>
                    <input type="text" id="freezer_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Contoh: Freezer Lab A" required />
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                        Operasional</label>
                    <select id="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Pilih status</option>
                        <option value="normal">Normal</option>
                        <option value="warning">Peringatan Suhu</option>
                        <option value="critical">Kritis / Mati</option>
                    </select>
                </div>
            </form>
        </div>

    </main>

    <!-- Target Modal HTML untuk Component 3 -->
    <div id="default-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Status Tes Modal
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        [Pasti] Jika popup ini muncul dan background menjadi redup saat tombol diklik, JavaScript
                        Flowbite telah terintegrasi dengan benar di aplikasi Laravel Anda.
                    </p>
                </div>
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="default-modal" type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
