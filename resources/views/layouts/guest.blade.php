@php
    $clinicSetting = \App\Models\ClinicSetting::first();
    
    // Dynamic Theme Colors
    $primaryColor = $clinicSetting?->primary_color ?? '#2563eb'; 
    $secondaryColor = $clinicSetting?->secondary_color ?? '#eff6ff'; 

    // Identitas
    $clinicName = $clinicSetting?->name ?? 'Klinik Sehat';
    $address = $clinicSetting?->address ?? 'Jl. Kesehatan No. 123, Jakarta';
    $phone = $clinicSetting?->phone ?? '08123456789';
    $email = $clinicSetting?->email ?? 'info@kliniksehat.id';
    $logoUrl = $clinicSetting?->logo ? asset('storage/' . $clinicSetting->logo) : asset('assets/img/logo_login.png');

    // Format Nomor WhatsApp
    $waNumber = preg_replace('/[^0-9]/', '', $phone);
    if (str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
    $waLink = "https://wa.me/" . $waNumber;

    if (class_exists('\App\Models\Poli')) {
        $polis = \App\Models\Poli::all();
    } elseif (class_exists('\App\Models\Polyclinic')) {
        $polis = \App\Models\Polyclinic::all();
    } else {
        $polis = collect([]);
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', $clinicName)</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite/Tailwind CSS -->
    @vite('resources/css/app.css')

    <!-- Inject Tailwind untuk Navbar & Footer bawaan Landing Page -->
    <style>
        .text-primary { color: {{ $primaryColor }}; }
        .bg-primary { background-color: {{ $primaryColor }}; }
        .border-primary { border-color: {{ $primaryColor }}; }
        .hover\:text-primary:hover { color: {{ $primaryColor }}; }
        .hover\:bg-primary:hover { background-color: {{ $primaryColor }}; }
        .text-secondary { color: {{ $secondaryColor }}; }
        .bg-secondary { background-color: {{ $secondaryColor }}; }
        .border-secondary { border-color: {{ $secondaryColor }}; }
        .hover\:bg-secondary:hover { background-color: {{ $secondaryColor }}; }
    </style>

    @stack('styles')
</head>
<body class="font-poppins bg-brand-background text-text-dark antialiased">

    <!-- NAVBAR DARI LANDING PAGE -->
    <nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('landing') ?? url('/') }}" class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-secondary shadow-sm flex items-center justify-center bg-white">
                            <img src="{{ $logoUrl }}" alt="Logo Klinik" class="h-full w-full object-cover scale-125" onerror="this.src='{{ asset('assets/img/logo_login.png') }}';">
                        </div>
                        <span class="text-xl font-bold tracking-tight text-gray-800 uppercase leading-tight hidden sm:block">
                            Sistem Informasi <br> <span class="text-primary text-lg">{{ $clinicName }}</span>
                        </span>
                    </a>
                </div>

                <div class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('landing') }}#beranda" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Beranda</a>
                    <a href="{{ route('landing') }}#mengapa-kami" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Mengapa Kami</a>
                    <a href="{{ route('landing') }}#jadwal-dokter" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Jadwal Dokter</a>
                    <a href="{{ route('landing') }}#tentang-kami" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Tentang Kami</a>
                    <a href="{{ route('landing') }}#layanan" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Layanan</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') ?? url('/admin') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:opacity-90 transition shadow-md">
                            Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:opacity-90 transition shadow-md">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-primary border-2 border-primary rounded-lg hover:bg-secondary transition">
                            Daftar
                        </a>
                    @endauth
                </div>

                <button id="mobile-menu-btn" class="lg:hidden text-gray-600 hover:text-primary transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 py-4 space-y-2">
                <a href="{{ route('landing') }}#beranda" class="block px-4 py-2 text-gray-600 hover:text-primary transition mobile-nav-link">Beranda</a>
                <a href="{{ route('landing') }}#mengapa-kami" class="block px-4 py-2 text-gray-600 hover:text-primary transition mobile-nav-link">Mengapa Kami</a>
                <a href="{{ route('landing') }}#jadwal-dokter" class="block px-4 py-2 text-gray-600 hover:text-primary transition mobile-nav-link">Jadwal Dokter</a>
                <a href="{{ route('landing') }}#tentang-kami" class="block px-4 py-2 text-gray-600 hover:text-primary transition mobile-nav-link">Tentang Kami</a>
                <a href="{{ route('landing') }}#layanan" class="block px-4 py-2 text-gray-600 hover:text-primary transition mobile-nav-link">Layanan</a>
                <div class="flex flex-col gap-3 mt-4 px-4">
                    @auth
                        <a href="{{ route('dashboard') ?? url('/admin') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-lg hover:opacity-90 transition font-medium">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-lg hover:opacity-90 transition font-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="w-full text-center border-2 border-primary text-primary px-4 py-3 rounded-lg hover:bg-secondary transition font-medium">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="overflow-x-hidden pt-20">
        @yield('content')
    </main>

    <!-- FOOTER DARI LANDING PAGE -->
    <footer class="bg-slate-900 text-slate-300 pt-20 pb-10 mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16 border-b border-slate-800 pb-16">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg overflow-hidden bg-white flex items-center justify-center">
                             @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo" class="h-full w-full object-cover scale-125" onerror="this.src='{{ asset('assets/img/logo_login.png') }}';">
                            @else
                                <div class="w-full h-full bg-primary flex items-center justify-center text-white"><i class="fas fa-plus"></i></div>
                            @endif
                        </div>
                        <span class="text-xl font-bold text-primary uppercase">{{ $clinicName }}</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-sm">
                        Memberikan pelayanan kesehatan terbaik dengan sentuhan kasih dan teknologi medis terkini demi kesembuhan keluarga Anda.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('landing') }}#beranda" class="hover:text-primary transition-colors text-sm">Beranda</a></li>
                        <li><a href="{{ route('landing') }}#tentang-kami" class="hover:text-primary transition-colors text-sm">Tentang Kami</a></li>
                        <li><a href="{{ route('landing') }}#layanan" class="hover:text-primary transition-colors text-sm">Layanan Medis</a></li>
                        <li><a href="{{ route('landing') }}#jadwal-dokter" class="hover:text-primary transition-colors text-sm">Jadwal Dokter</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Layanan Populer</h4>
                    <ul class="space-y-3">
                        @forelse($polis->take(4) as $poli)
                            <li><a href="{{ route('landing') }}#layanan" class="hover:text-primary transition-colors text-sm">{{ $poli->name }}</a></li>
                        @empty
                            <li><a href="{{ route('landing') }}#layanan" class="hover:text-primary transition-colors text-sm">Poli Umum</a></li>
                            <li><a href="{{ route('landing') }}#layanan" class="hover:text-primary transition-colors text-sm">Poli Gigi & Mulut</a></li>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Kontak Kami</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                            <span class="text-sm">{{ $address }}</span>
                        </li>
                        <li class="flex items-center gap-3 group">
                            <i class="fas fa-phone text-primary"></i>
                            <a href="{{ $waLink }}" target="_blank" class="text-sm group-hover:text-white group-hover:underline transition-colors" title="Chat WhatsApp Kami">
                                {{ $phone }}
                            </a>
                        </li>
                        <li class="flex items-center gap-3 group">
                            <i class="fas fa-envelope text-primary"></i>
                            <a href="mailto:{{ $email }}" class="text-sm group-hover:text-white group-hover:underline transition-colors" title="Kirim Email">
                                {{ $email }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} {{ $clinicName }}. Hak Cipta Dilindungi.<br>
                Powered by DistyMedic SaaS
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuBtn) {
                menuBtn.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

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