<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Dokter - Klinik Sehat')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AlpineJS for easy state management -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        /* Warna scrollbar disesuaikan dengan tema dokter */
        ::-webkit-scrollbar-thumb { background: #047857; border-radius: 10px; } 
        .bg-clinic {
            background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }
        .content-wrapper { position: relative; z-index: 2; }
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')

</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: window.innerWidth > 1024 }" @resize.window="sidebarOpen = window.innerWidth > 1024" x-cloak>
    <div class="flex h-screen overflow-hidden">
        <!-- Backdrop for mobile sidebar -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside 
            class="fixed lg:relative inset-y-0 left-0 bg-[#E9E6E6] text-gray-800 flex flex-col transform transition-all duration-300 ease-in-out z-40"
            :class="{
                'w-64': sidebarOpen, 
                'w-20': !sidebarOpen,
                'translate-x-0': sidebarOpen,
                '-translate-x-full lg:translate-x-0': !sidebarOpen
            }"
        >
            <!-- Logo -->
            <div class="p-4 mb-6 transition-all duration-300" :class="sidebarOpen ? 'p-4' : 'p-2'">
                <div class="aspect-square bg-[#f1eff1] p-4 rounded-full shadow-lg flex items-center justify-center">
                    <img src="{{ asset('assets/img/logo_login.png') }}" alt="Logo Klinik" class="w-full h-full object-contain">
                </div>
            </div>
            
            <nav class="flex-grow px-4 overflow-y-auto">
                <ul>
                    @php
                    // Helper function untuk generate menu item
                    // Warna diubah menjadi hijau (#047857) untuk tema dokter
                    function menu_item_dokter($route, $name, $icon) {
                        $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
                        
                        $baseClasses = 'flex items-center p-3 rounded-lg w-full text-left transition-colors duration-200';
                        $activeClasses = 'bg-[#047857] text-white shadow-md'; // Warna hijau emerald-700
                        $inactiveClasses = 'text-gray-600 hover:bg-gray-900/5 hover:text-[#047857]';
                        
                        $linkClasses = $baseClasses . ' ' . ($isActive ? $activeClasses : $inactiveClasses);
                        
                        $url = Route::has($route) ? route($route) : '#';
                        $disabledTooltip = !Route::has($route) ? 'title="Halaman belum tersedia"' : '';

                        return "<li class='mb-4 relative'>
                                    <a href='$url' class='$linkClasses' $disabledTooltip x-data='{ tooltip: false }' @mouseenter='tooltip = true' @mouseleave='tooltip = false'>
                                        <div class='flex-shrink-0'>$icon</div>
                                        <span class='ml-3 whitespace-nowrap' x-show='sidebarOpen' x-transition:enter='transition ease-out duration-200' x-transition:enter-start='opacity-0' x-transition:enter-end='opacity-100' x-transition:leave='transition ease-in duration-100' x-transition:leave-start='opacity-100' x-transition:leave-end='opacity-0'>$name</span>
                                        <div x-show='!sidebarOpen && tooltip' class='absolute left-full ml-4 px-2 py-1 bg-gray-800 text-white text-sm rounded-md shadow-lg z-50 whitespace-nowrap'>
                                            $name
                                        </div>
                                    </a>
                                </li>";
                    }
                    @endphp

                    {{-- Menu khusus untuk Dokter --}}
                    {!! menu_item_dokter('dokter.dashboard', 'Dashboard', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>') !!}
                    {!! menu_item_dokter('#', 'Riwayat Pasien', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>') !!}
                    {!! menu_item_dokter('#', 'Jadwal Saya', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>') !!}
                </ul>
            </nav>
            <div class="px-4 pb-4 mt-auto flex-shrink-0">
                 {!! menu_item_dokter('dokter.profil', 'Profile Akun', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>') !!}
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header/Navbar disesuaikan dengan warna tema dokter --}}
            <header class="bg-[#047857] shadow-md z-20">
                <div class="flex items-center justify-between p-4 text-white">
                    <div class="flex items-center">
                        <button @click.stop="sidebarOpen = !sidebarOpen" class="text-white focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        {{-- Ucapan selamat datang disesuaikan untuk dokter --}}
                        <h2 class="hidden md:block text-xl font-semibold ml-4">Selamat Bekerja, {{ Auth::user()->full_name ?? 'Dokter' }}</h2>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 font-semibold py-2 px-4 rounded-lg">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-clinic relative">
                <div class="content-wrapper p-6 md:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
