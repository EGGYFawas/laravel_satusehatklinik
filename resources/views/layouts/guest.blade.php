<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', 'Klinik Sehat')</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AlpineJS (Masih dimuat jika Anda membutuhkannya untuk hal lain) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite/Tailwind CSS -->
    @vite('resources/css/app.css')

    {{-- Stack untuk style tambahan per halaman --}}
    @stack('styles')
</head>
<body class="font-poppins bg-brand-background text-text-dark antialiased">

    {{-- [MODIFIKASI] Header diganti dengan Navbar dari Landing Page --}}
    <nav class="fixed top-0 left-0 right-0 bg-[rgba(226,219,219,0.7)] backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}"> {{-- Link logo ke landing --}}
                        <img src="{{ asset('assets/img/logo_login.png') }}" alt="Klinik Sehat Logo" class="h-20 w-auto"
                             onerror="this.src='https://placehold.co/100x40/0284C7/FFFFFF?text=Logo'; this.onerror=null;">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex gap-8">
                    {{-- [PERBAIKAN] Link diubah menjadi route absolut --}}
                    <a href="{{ route('landing') }}#beranda" class="text-gray-600 hover:text-blue-600 transition">Beranda</a>
                    <a href="{{ route('landing') }}#mengapa-kami" class="text-gray-600 hover:text-blue-600 transition">Mengapa Kami</a>
                    <a href="{{ route('landing') }}#jadwal-dokter" class="text-gray-600 hover:text-blue-600 transition">Jadwal Dokter</a>
                    <a href="{{ route('landing') }}#tentang-kami" class="text-gray-600 hover:text-blue-600 transition">Tentang Kami</a>
                    <a href="{{ route('landing') }}#layanan" class="text-gray-600 hover:text-blue-600 transition">Layanan</a>
                    <a href="{{ route('artikel.index') }}" class="text-gray-600 hover:text-blue-600 transition">Artikel</a> {{-- Link ke halaman artikel --}}
                </div>

                <!-- Login/Register Buttons -->
                <div class="hidden lg:flex gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="border-2 border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="border-2 border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Daftar</a>
                    @endauth
                </div>

                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-blue-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-4 space-y-2">
                {{-- [PERBAIKAN] Link diubah menjadi route absolut --}}
                <a href="{{ route('landing') }}#beranda" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Beranda</a>
                <a href="{{ route('landing') }}#mengapa-kami" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Mengapa Kami</a>
                <a href="{{ route('landing') }}#jadwal-dokter" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Jadwal Dokter</a>
                <a href="{{ route('landing') }}#tentang-kami" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Tentang Kami</a>
                <a href="{{ route('landing') }}#layanan" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Layanan</a>
                <a href="{{ route('artikel.index') }}" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition mobile-nav-link">Artikel</a>
                
                <div class="flex gap-3 mt-4 px-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-center border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    {{-- [/MODIFIKASI] --}}

    {{-- Konten utama dari setiap halaman (landing, artikel-index, artikel-show) akan masuk di sini --}}
    <main class="overflow-x-hidden">
        @yield('content')
    </main>

    {{-- [MODIFIKASI] Footer diganti dengan versi lengkap dari landing page --}}
    <footer class="bg-gray-900 text-gray-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('assets/img/logo_login.png') }}" alt="Klinik Sehat Logo" class="h-8 w-8 object-contain">
                        <span class="font-bold text-white text-lg">Klinik Sehat</span>
                    </div>
                    <p class="text-gray-400 text-sm">Layanan kesehatan terpercaya untuk Anda dan keluarga</p>
                </div>

                <!-- Tautan Cepat -->
                <div>
                    <h4 class="font-bold text-white mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        {{-- [PERBAIKAN] Link diubah agar berfungsi di semua halaman --}}
                        <li><a href="{{ route('landing') }}#beranda" class="text-gray-400 hover:text-blue-400 transition">Beranda</a></li>
                        <li><a href="{{ route('landing') }}#layanan" class="text-gray-400 hover:text-blue-400 transition">Layanan</a></li>
                        <li><a href="{{ route('landing') }}#jadwal-dokter" class="text-gray-400 hover:text-blue-400 transition">Jadwal Dokter</a></li>
                        <li><a href="{{ route('artikel.index') }}" class="text-gray-400 hover:text-blue-400 transition">Artikel</a></li>
                        <li><a href="{{ route('landing') }}#tentang-kami" class="text-gray-400 hover:text-blue-400 transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- [PENAMBAHAN BARU] Akun -->
                <div>
                    <h4 class="font-bold text-white mb-4">Akun</h4>
                    <ul class="space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-blue-400 transition">Dashboard Saya</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-blue-400 transition">Login Pasien</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-blue-400 transition">Daftar Akun Baru</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-bold text-white mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <span class="text-blue-400">📞</span>
                            <span class="text-gray-400">(021) 1234-5678</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-blue-400">✉️</span>
                            <span class="text-gray-400">info@kliniksehat.id</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-blue-400">📍</span>
                            <span class="text-gray-400">Jl. Kesehatan No. 123, Jakarta</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Klinik Sehat. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>
    {{-- [/MODIFIKASI] --}}

    {{-- Stack untuk script tambahan per halaman --}}
    @stack('scripts')

    {{-- [PENAMBAHAN BARU] Skrip untuk Mobile Menu --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuBtn) {
                menuBtn.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Menutup menu saat link di klik (untuk navigasi #)
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                });
            });
        });
    </script>
</body>
</html>