<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Klinik Sehat - Petugas Loket')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #38a89d; border-radius: 10px; }
        .bg-clinic-image {
            background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')

</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: window.innerWidth > 1024 }" @resize.window="sidebarOpen = window.innerWidth > 1024" x-cloak>
    <div class="flex h-screen overflow-hidden">
        <!-- Backdrop -->
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
                    function menu_item_petugas($route, $name, $icon) {
                        $isActive = request()->routeIs($route) || request()->routeIs($route . '.*');
                        $baseClasses = 'flex items-center p-3 rounded-lg w-full text-left transition-colors duration-200';
                        // Warna Mint Green (#ABDCD6) dan warna gelap pendamping (#38a89d / #2c7a7b)
                        $activeClasses = 'bg-[#38a89d] text-white shadow-md';
                        $inactiveClasses = 'text-gray-600 hover:bg-gray-900/5 hover:text-[#38a89d]';
                        $linkClasses = $baseClasses . ' ' . ($isActive ? $activeClasses : $inactiveClasses);
                        $url = Route::has($route) ? route($route) : '#';

                        return "<li class='mb-4 relative'>
                                    <a href='$url' class='$linkClasses' x-data='{ tooltip: false }' @mouseenter='tooltip = true' @mouseleave='tooltip = false'>
                                        <div class='flex-shrink-0'>$icon</div>
                                        <span class='ml-3 whitespace-nowrap' x-show='sidebarOpen' x-transition:enter='transition ease-out duration-200' x-transition:enter-start='opacity-0' x-transition:enter-end='opacity-100' x-transition:leave='transition ease-in duration-100' x-transition:leave-start='opacity-100' x-transition:leave-end='opacity-0'>$name</span>
                                        <div x-show='!sidebarOpen && tooltip' class='absolute left-full ml-4 px-2 py-1 bg-gray-800 text-white text-sm rounded-md shadow-lg z-50 whitespace-nowrap'>
                                            $name
                                        </div>
                                    </a>
                                </li>";
                    }
                    @endphp

                    {{-- PERUBAHAN: Menambahkan menu baru "Antrean Pasien Offline" --}}
                    {!! menu_item_petugas('petugas-loket.antrean-offline', 'Antrean Pasien Offline', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>') !!}

                    {!! menu_item_petugas('petugas-loket.dashboard', 'Dashboard Apotek', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>') !!}
                    
                </ul>
            </nav>
            <div class="px-4 pb-4 mt-auto flex-shrink-0">
                 {!! menu_item_petugas('#', 'Profil Akun', '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>') !!}
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-[#ABDCD6] shadow-md z-20">
                <div class="flex items-center justify-between p-4 text-slate-800">
                    <div class="flex items-center">
                        <button @click.stop="sidebarOpen = !sidebarOpen" class="text-slate-800 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <h2 class="hidden md:block text-xl font-semibold ml-4">Selamat Bekerja, {{ Auth::user()->full_name }}</h2>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg">Keluar</button></form>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto relative bg-clinic-image">
                <div class="absolute inset-0 bg-gray-100/80 backdrop-blur-sm"></div>
                <div class="relative z-10 p-6 md:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
