<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen p-4 transition-transform -translate-x-full sm:translate-x-0"
    aria-label="Sidebar">
    <div
        class="h-full px-4 py-5 overflow-y-auto bg-white border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
        <div class="space-y-6">

            <!-- Logo Header -->
            <div class="flex items-center gap-3 px-1">
                <img src="{{ asset('assets/Logo.png') }}" alt="Karyatama Logo" class="size-10 object-contain">
                <div>
                    <h2 class="font-bold text-base text-slate-900 leading-tight">Karyatama</h2>
                    <p class="text-xs text-slate-500 font-normal">Sistem Monitoring Freezer</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">

                <!-- Beranda (Lucide: House) -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="size-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-500' }}"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                        <path
                            d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- Freezer (Lucide: Snowflake) -->
                <a href="{{ route('admin.freezers.index') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.freezers*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="size-5 {{ request()->routeIs('admin.freezers*') ? 'text-blue-600' : 'text-slate-500' }}"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <line x1="2" x2="22" y1="12" y2="12" />
                        <line x1="12" x2="12" y1="2" y2="22" />
                        <path d="m20 16-4-4 4-4" />
                        <path d="m4 8 4 4-4 4" />
                        <path d="m16 4-4 4-4-4" />
                        <path d="m8 20 4-4 4 4" />
                    </svg>
                    <span>Freezer</span>
                </a>

                <!-- Tugas Reparasi (Lucide: Wrench) -->
                <a href="{{ route('admin.repairs.index') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.repairs*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="size-5 {{ request()->routeIs('admin.repairs*') ? 'text-blue-600' : 'text-slate-500' }}"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path
                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                    </svg>
                    <span>Tugas Reparasi</span>
                </a>

                <!-- Pengguna (Lucide: Users) -->
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <svg class="size-5 {{ request()->routeIs('admin.users*') ? 'text-blue-600' : 'text-slate-500' }}"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>

                    <span>Pengguna</span>
                </a>

            </nav>
        </div>

        <!-- Footer Sidebar: Profil Pengguna dengan Flowbite Dropdown Placement right-start -->
        <div class="pt-4 border-t border-slate-200/80">
            <!-- Trigger Button -->
            <button id="userProfileDropdownButton" data-dropdown-toggle="userProfileDropdown"
                data-dropdown-placement="right-start" type="button"
                class="w-full flex items-center justify-between p-1.5 rounded-xl hover:bg-slate-100/80 transition-colors group text-left focus:outline-none">

                <div class="flex items-center gap-3 overflow-hidden">
                    <!-- Avatar Bulat Biru Muda dengan Inisial Nama -->
                    <div
                        class="size-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <!-- Nama & Role Pengguna -->
                    <div class="truncate">
                        <p
                            class="text-sm font-semibold text-slate-800 truncate leading-snug group-hover:text-slate-900">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-slate-500 font-normal truncate capitalize">
                            {{ Auth::user()->role->value ?? Auth::user()->role }}
                        </p>
                    </div>
                </div>

                <!-- Chevron Icon Dropdown (Lucide: ChevronRight) -->
                <svg class="size-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-200 shrink-0"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    viewBox="0 0 24 24">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>

            <!-- Flowbite Flyout Menu -->
            <div id="userProfileDropdown"
                class="z-50 hidden w-56 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 divide-y divide-slate-100">
                <!-- Info Header Ringkas dalam Menu -->
                <div class="px-3 py-2.5">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>

                <!-- Tombol Keluar (Lucide: LogOut) -->
                <div class="pt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-red-600 rounded-xl hover:bg-red-50 transition-colors">
                            <svg class="size-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" x2="9" y1="12" y2="12" />
                            </svg>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</aside>
