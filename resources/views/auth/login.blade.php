<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Karyatama</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-md space-y-6">

        <!-- Header Logo — sekarang center -->
        <header class="flex items-center justify-center mb-1">
            <img src="{{ asset('assets/Logo.png') }}" alt="Karyatama" class="size-25 object-contain">
            <div>
                <h1 class="font-bold text-lg leading-tight">Karyatama</h1>
                <p class="text-xs text-slate-500">Sistem Monitoring Reparasi Freezer</p>
            </div>
        </header>

        <!-- Form Card dengan glow blob di belakang -->
        <div class="relative">
            <!-- Glow blob: elemen terpisah, blur ekstrem, di belakang card -->
            <div class="absolute -inset-2 bg-blue-400/25 blur-2xl rounded-full -z-10"></div>
            <main class="relative bg-white border border-slate-200 p-8 rounded-2xl shadow-xl">
                <h2 class="text-2xl font-bold mb-1">Masuk ke akun Anda</h2>
                <p class="text-sm text-slate-500 mb-6">Masuk untuk melihat informasi dan perkembangan reparasi freezer.
                </p>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-300 rounded-lg text-xs text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="identity" class="block text-xs font-medium text-slate-600 mb-1.5">Email atau nama
                            pengguna</label>
                        <input type="text" name="identity" id="identity" value="{{ old('identity') }}" required
                            autofocus autocomplete="username"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-400"
                            placeholder="Masukkan email atau nama pengguna">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-600 mb-1.5">Kata sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                autocomplete="current-password"
                                class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-400"
                                placeholder="Masukkan kata sandi">
                            <button type="button" id="togglePassword" aria-label="Tampilkan kata sandi"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- [Pasti] Fitur Remember Me Checkbox -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" id="remember"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            <span class="text-xs text-slate-600">Ingat akun saya</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 font-semibold rounded-xl text-sm text-white transition-colors shadow-lg shadow-blue-600/30">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-xs text-slate-500 flex items-center justify-between">
                    <span>Kesulitan masuk? <a href="#" class="text-blue-600 hover:underline">Hubungi
                            admin.</a></span>
                </div>

                <!-- Garis dipindah ke bawah "Hubungi admin" -->
                <div class="mt-6 pt-6 border-t border-slate-200 flex items-center gap-2 text-xs text-slate-400">
                    <svg class="size-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Akses hanya untuk pengguna terdaftar.</span>
                </div>
            </main>
        </div>

        <footer class="text-center text-xs text-slate-400">
            © CV. Karyatama Agung Abadi
        </footer>

    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    </script>

</body>

</html>
