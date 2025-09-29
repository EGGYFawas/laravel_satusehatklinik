<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk fungsionalitas dropdown & sidebar mobile yang lebih mudah -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body class="bg-slate-100 antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-100">
        <!-- MODIFIKASI: Sidebar dengan gradasi, responsif, dan transisi -->
        <aside 
            class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col text-white transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 bg-gradient-to-b from-[#4F46E5] to-[#0F172A]"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                
            <!-- Kontainer utama untuk area logo, diberi padding atas dan samping -->
            <div class="p-4">
    <div class="aspect-square bg-[#f1eff1] p-4 rounded-full shadow-lg flex items-center justify-center">
        <img src="{{ asset('assets/img/logo_login.png') }}" alt="Logo Perusahaan" class="w-full h-full object-contain">
    </div>
</div>
            <!-- MODIFIKASI: Struktur Navigasi Baru dengan Dropdown & Animasi Hover -->
            <nav class="flex-1 px-4 py-4 space-y-2">
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 hover:bg-indigo-700 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : '' }}">
                    <i class="fa-solid fa-house-chimney w-6 text-center mr-3"></i>
                    Dashboard
                </a>

                <!-- Menu Master Data (Dropdown dengan Alpine.js) -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 hover:bg-indigo-700">
                        <span class="flex items-center">
                            <i class="fa-solid fa-database w-6 text-center mr-3"></i>
                            Master Data
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-2 space-y-2 pl-8">
                        <a href="#" class="block px-4 py-2 text-sm rounded-lg transition-colors duration-200 hover:bg-indigo-700 hover:bg-opacity-50">Kelola Pasien</a>
                        <a href="#" class="block px-4 py-2 text-sm rounded-lg transition-colors duration-200 hover:bg-indigo-700 hover:bg-opacity-50">Kelola Dokter</a>
                        <a href="#" class="block px-4 py-2 text-sm rounded-lg transition-colors duration-200 hover:bg-indigo-700 hover:bg-opacity-50">Kelola Petugas</a>
                    </div>
                </div>

                <!-- Menu Kelola Jadwal Dokter -->
                <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 hover:bg-indigo-700">
                    <i class="fa-solid fa-calendar-days w-6 text-center mr-3"></i>
                    Kelola Jadwal Dokter
                </a>

                <!-- Menu Kelola Artikel -->
                <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 hover:bg-indigo-700">
                    <i class="fa-solid fa-newspaper w-6 text-center mr-3"></i>
                    Kelola Artikel
                </a>
            </nav>

            <!-- Profil Pengguna -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=4F46E5&color=fff" alt="Admin Avatar">
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-indigo-200">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
                    </div>
                </div>
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mt-4 w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- MODIFIKASI: Header dengan Tombol Hamburger untuk Mobile -->
            <header class="h-20 flex items-center justify-between px-4 sm:px-8 border-b bg-white">
                <!-- Tombol Hamburger (hanya terlihat di mobile) -->
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md text-slate-500 hover:bg-slate-200 hover:text-slate-800">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Quick search..." class="pl-10 pr-4 py-2 w-full max-w-xs border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button class="p-2 rounded-full hover:bg-slate-200">
                        <i class="fa-solid fa-bell text-xl text-slate-600"></i>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-4 sm:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>