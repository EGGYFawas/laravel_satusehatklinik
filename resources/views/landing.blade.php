<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Sehat - Layanan Kesehatan Terpercaya</title>
    
    <!-- Impor Font Poppins dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Script Tailwind untuk mengaktifkan kelas kustom (seperti font-['Poppins'] dan bg-[rgba(...)]) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Konfigurasi Tailwind untuk menambahkan font Poppins
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Gaya tambahan untuk memastikan font diterapkan dengan benar -->
    <style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    /* --- PERUBAHAN: EFEK FADE ANTAR SECTION MENJADI LEBIH HALUS --- */
    #beranda::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        /* Mengatur ketinggian efek fade agar lebih panjang dan halus */
        height: 50px; /* Nilai ini bisa Anda sesuaikan (misal: 120px, 180px) */
        /* Menggunakan rgba() untuk kontrol opacity pada warna putih di akhir gradient */
        background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.95)); /* Mengurangi sedikit opacity putih */
        pointer-events: none; /* Memastikan elemen ini tidak bisa di-klik */
        z-index: 10; /* Memastikan di atas gambar hero */
    }
</style>
</head>
<!-- Menambahkan kelas font-poppins untuk menerapkan font ke seluruh halaman -->
<body class="bg-white text-gray-900 font-poppins">
    <nav class="fixed top-0 left-0 right-0 bg-white backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}">
                        <img src="{{ asset('assets/img/logo_login.png') }}" alt="Klinik Sehat Logo" class="h-20 w-auto"
                             onerror="this.src='https://placehold.co/100x40/0284C7/FFFFFF?text=Logo'; this.onerror=null;">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex gap-8">
                    {{-- [MODIFIKASI] Link diubah menjadi route absolut --}}
                    <a href="{{ route('landing') }}#beranda" class="text-gray-600 hover:text-blue-600 transition">Beranda</a>
                    <a href="{{ route('landing') }}#mengapa-kami" class="text-gray-600 hover:text-blue-600 transition">Mengapa Kami</a>
                    <a href="{{ route('landing') }}#jadwal-dokter" class="text-gray-600 hover:text-blue-600 transition">Jadwal Dokter</a>
                    <a href="{{ route('landing') }}#tentang-kami" class="text-gray-600 hover:text-blue-600 transition">Tentang Kami</a>
                    <a href="{{ route('landing') }}#layanan" class="text-gray-600 hover:text-blue-600 transition">Layanan</a>
                    <a href="{{ route('artikel.index') }}" class="text-gray-600 hover:text-blue-600 transition">Artikel</a>
                </div>

                <!-- Login and Register buttons -->
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

    <!-- ========== HERO SECTION ========== -->
    {{-- [MODIFIKASI] Menambahkan 'min-h-screen' dan 'flex items-center' agar full-screen dan konten di tengah --}}
    {{-- [MODIFIKASI] Menghapus padding atas/bawah (pt-32 pb-16 md:pt-40 md:pb-24) dan menggantinya dengan padding untuk navbar (pt-16) --}}
    <section id="beranda" class="relative min-h-screen flex items-center pt-16 bg-cover bg-center" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/hero-doctor.jpg') }}')">
         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                 <!-- Text Content -->
                 <div class="space-y-6 text-center md:text-left">
                     <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">
                         Kesehatan Anda Adalah Prioritas Kami
                     </h1>
                     <p class="text-lg text-gray-200 leading-relaxed">
                         Klinik Sehat menyediakan layanan kesehatan terpercaya dengan dokter berpengalaman dan fasilitas modern untuk menjaga kesehatan Anda dan keluarga.
                     </p>
                     <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center md:justify-start">
                         
                         <!-- Logika Tombol Buat Antrian -->
                         @guest
                             <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                 Buat Antrian Berobat
                             </a>
                         @else
                             <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                 Buat Antrian Berobat
                             </a>
                         @endguest
                         
                         <a href="#panduan" class="border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white/10 transition font-medium">
                             Panduan Penggunaan
                         </a>
                         
                     </div>
                 </div>
             </div>
         </div>
     </section>


    <!-- ========== MENGAPA KAMI ========== -->
    <!-- PERUBAHAN: Mengganti bg-white dengan gambar, padding, dan overlay putih transparan -->
    <section id="mengapa-kami" class="pt-32 pb-16 md:pt-40 md:pb-24 bg-cover bg-center" style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('{{ asset('assets/img/why-us1.jpg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <!-- Teks (text-gray-900) ini akan terlihat jelas di atas overlay putih -->
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih Klinik Sehat?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami merevolusi cara Anda berobat. Lupakan antrean panjang dan ketidakpastian. Dengan sistem kami, 
seluruh proses dari pendaftaran hingga pengambilan obat kini ada dalam genggaman Anda, 
memberikan kenyamanan, kecepatan, dan kendali penuh atas kesehatan Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-blue-50 p-8 rounded-lg hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Pendaftaran 100% Online & Real-time</h3>
                    <p class="text-gray-600 text-center">Sistem kami memungkinkan Anda untuk mendaftarkan diri sendiri atau keluarga dan mendapatkan nomor antrean secara instan melalui website. Pantau pergerakan antrean secara real-time dari ponsel Anda dan datanglah ke klinik hanya saat giliran Anda sudah dekat. Tidak ada lagi waktu yang terbuang untuk menunggu.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-blue-50 p-8 rounded-lg hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.757a1 1 0 01-.940 1.017 48.993 48.993 0 01-5.674 0 1 1 0 01-.94-1.017V6.517c0-1.667.341-3.252.975-4.62zM6 12a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Waktu Fleksibel & Terkendali Sepenuhnya</h3>
                    <p class="text-gray-600 text-center">Lihat jadwal dokter yang tersedia dan pilih waktu kunjungan yang paling sesuai dengan kesibukan Anda. Perlu membatalkan karena ada urusan mendadak? Lakukan pembatalan dengan mudah melalui dashboard Anda, memberikan slot bagi pasien lain. Sistem kami dirancang untuk beradaptasi dengan hidup Anda, bukan sebaliknya.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-blue-50 p-8 rounded-lg hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rekam Medis Digital & Terintegrasi</h3>
                    <p class="text-gray-600 text-center">Riwayat Kesehatan Anda Aman dan Selalu Tersedia.
Setiap kunjungan, diagnosis, dan resep obat akan tercatat secara otomatis dan aman dalam riwayat rekam medis digital Anda. Dokter dapat dengan mudah melihat riwayat kesehatan Anda pada kunjungan berikutnya untuk memberikan perawatan yang lebih akurat dan personal. Akses riwayat kesehatan Anda kapan pun dibutuhkan, langsung dari profil Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== JADWAL DOKTER ========== -->
    <section id="jadwal-dokter" class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Jadwal Dokter</h2>
                <p class="text-lg text-gray-600">Pilih hari untuk melihat jadwal dokter spesialis kami</p>
            </div>

            <!-- Day Tabs -->
            <div class="flex flex-wrap gap-3 justify-center mb-12">
                <button class="day-tab active px-6 py-2 rounded-lg bg-blue-600 text-white font-medium transition" data-day="senin">
                    Senin
                </button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="selasa">
                    Selasa
                </button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="rabu">
                    Rabu
                </button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="kamis">
                    Kamis
                </button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="jumat">
                    Jumat
                </button>
            </div>

            <!-- Doctor Schedule Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    @foreach ($doctors as $doctor)
        
        @foreach ($doctor->doctorSchedules->groupBy('day_of_week') as $day => $schedulesOnDay)
            
            <div class="doctor-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition" 
                 data-day="{{ Str::lower($day) }}">
                 
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ $doctor->user->photo_url ?? 'https://placehold.co/80x80/E0E7FF/3B82F6?text=Dr' }}"
                         alt="{{ $doctor->user->name }}" class="w-16 h-16 rounded-full">
                    
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $doctor->user->name }}</h3>
                        
                        <p class="text-sm text-blue-600">{{ $doctor->specialization }}</p>
                    </div>
                </div>
                
                <div class="flex gap-2 flex-wrap">
                    @foreach ($schedulesOnDay as $schedule)
                        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </span>
                    @endforeach
                </div>
            </div>

        @endforeach
    @endforeach
    
    </div>
    </section>

    <!-- ========== TENTANG KAMI ========== -->
    <section id="tentang-kami" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Image -->
                <div class="hidden md:block">
                    <img src="https://placehold.co/500x400/E0E7FF/3B82F6?text=Interior+Klinik" 
                         alt="Clinic interior" class="rounded-lg shadow-lg w-full">
                </div>

                <!-- Content -->
                <div class="space-y-6">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Tentang Klinik Sehat</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik di Indonesia, di mana setiap pasien dapat mengelola kesehatannya dengan kendali penuh di ujung jari mereka. Kami menyediakan solusi kesehatan terintegrasi yang mudah diakses, didukung oleh teknologi terkini dan tim medis profesional.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Dengan tim dokter spesialis yang berpengalaman dan fasilitas medis modern, Klinik Sehat siap memberikan pelayanan kesehatan terbaik. Kami juga terus berinovasi untuk meningkatkan kualitas layanan.
                    </p>

                    <div class="grid grid-cols-3 gap-4 pt-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">500+</p>
                            <p class="text-gray-600 text-sm">Pasien Per Bulan</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">10+</p>
                            <p class="text-gray-600 text-sm">Dokter Spesialis</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">14</p>
                            <p class="text-gray-600 text-sm">Tahun Berdiri</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== LAYANAN ========== -->
    <section id="layanan" class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
                <p class="text-lg text-gray-600">Berbagai layanan kesehatan komprehensif untuk kebutuhan Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1.5A4.5 4.5 0 017.5 15h5a4.5 4.5 0 014.5 4.5V21zm4-6h-4v-4h4v4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Poli Umum</h3>
                    <p class="text-gray-600">Pemeriksaan kesehatan rutin dan konsultasi dengan dokter umum berpengalaman</p>
                </div>

                <!-- Service 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Poli Ibu & Anak</h3>
                    <p class="text-gray-600">Layanan pemeriksaan kesehatan dan konsultasi dengan dokter spesialis anak dan kebidanan berpengalaman</p>
                </div>

                <!-- Service 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Poli Gigi</h3>
                    <p class="text-gray-600">Pemeriksaan laboratorium lengkap dengan hasil akurat dan cepat</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========== PANDUAN BEROBAT ONLINE ========== -->
    <section id="panduan" class="py-16 md:py-24 bg-[#24306E] text-white"> {{-- [MODIFIKASI] Warna disesuaikan dengan tema --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Alur Berobat Online Klinik Kami</h2>
                <p class="text-lg text-gray-200 max-w-2xl mx-auto">Ikuti 4 langkah mudah untuk berobat, dari pendaftaran di rumah hingga terima obat.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1: Daftar & Ambil Antrean -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white text-[#24306E] rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-bold mb-2">Daftar & Ambil Antrean</h3>
                    <p class="text-gray-200">Login atau buat akun baru. Buka dashboard Anda dan ambil antrean dengan memilih Poli, Dokter, serta mengisi keluhan Anda.</p>
                </div>

                <!-- Step 2: Datang & Check-In QR -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white text-[#24306E] rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-bold mb-2">Datang & Check-In</h3>
                    <p class="text-gray-200">Datang ke klinik. Buka kembali dashboard Anda, klik tombol "Check-In", dan pindai (scan) QR code yang tersedia di meja pendaftaran.</p>
                </div>

                <!-- Step 3: Pantau Panggilan Dokter -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white text-[#24306E] rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-bold mb-2">Pantau Panggilan & Periksa</h3>
                    <p class="text-gray-200">Duduk dan pantau dashboard Anda. Saat status berubah menjadi "Giliran Anda!", segera masuk ke ruang dokter untuk pemeriksaan.</p>
                </div>

                <!-- Step 4: Ambil Obat & Selesai -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white text-[#24306E] rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        4
                    </div>
                    <h3 class="text-xl font-bold mb-2">Ambil Obat & Selesai</h3>
                    <p class="text-gray-200">Setelah diperiksa, Anda akan otomatis mendapat nomor antrean apotek (misal: APT-001). Pantau statusnya hingga "Siap Diambil", lalu ambil obat Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== ARTIKEL ========== -->
    <section id="artikel" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Artikel Kesehatan</h2>
                <p class="text-lg text-gray-600">Informasi dan tips kesehatan terkini dari para ahli</p>
            </div>

            {{-- [MODIFIKASI UTAMA] Mengganti grid statis dengan loop dinamis --}}
            @if(isset($articles) && $articles->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                        {{-- Menggunakan gaya dari dashboard pasien --}}
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-2 transition-transform duration-300">
                            {{-- [PERBAIKAN] Menggunakan route('artikel.show') yang baru --}}
                            <a href="{{ route('artikel.show', $article->slug) }}">
                                <img src="{{ $article->image_url ?? 'https://placehold.co/600x400/ABDCD6/24306E?text=Klinik+Sehat' }}" alt="Gambar Artikel: {{ $article->title }}" class="w-full h-48 object-cover">
                            </a>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">{{ $article->title }}</h3>
                                <p class="text-gray-600 text-sm flex-grow">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                                {{-- [PERBAIKAN] Menggunakan route('artikel.show') yang baru --}}
                                <a href="{{ route('artikel.show', $article->slug) }}" class="text-sm text-[#24306E] font-semibold mt-4 self-start hover:underline">Baca Selengkapnya &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tombol untuk melihat semua artikel --}}
                <div class="text-center mt-16">
                    {{-- [PERBAIKAN] Menggunakan route('artikel.index') yang baru --}}
                    <a href="{{ route('artikel.index') }}" class="inline-block text-center py-3 px-8 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border-2 border-[#24306E] bg-[#24306E] text-white hover:bg-transparent hover:text-[#24306E]">
                        Lihat Semua Artikel
                    </a>
                </div>

            @else
                <div class="text-center text-gray-500 py-16 bg-gray-50 rounded-xl shadow-inner">
                    <p>Belum ada artikel kesehatan yang diterbitkan.</p>
                </div>
            @endif
            {{-- [/MODIFIKASI UTAMA] --}}
            
        </div>
    </section>
    
    <!-- ========== FOOTER ========== -->
    <footer class="bg-gray-900 text-gray-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">KS</span>
                        </div>
                        <span class="font-bold text-white">Klinik Sehat</span>
                    </div>
                    <p class="text-gray-400 text-sm">Layanan kesehatan terpercaya untuk Anda dan keluarga</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-white mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#beranda" class="text-gray-400 hover:text-blue-400 transition">Beranda</a></li>
                        <li><a href="#layanan" class="text-gray-400 hover:text-blue-400 transition">Layanan</a></li>
                        <li><a href="#tentang-kami" class="text-gray-400 hover:text-blue-400 transition">Tentang Kami</a></li>
                        <li><a href="#artikel" class="text-gray-400 hover:text-blue-400 transition">Artikel</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-bold text-white mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <span>📞</span>
                            <span class="text-gray-400">(021) 1234-5678</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span>✉️</span>
                            <span class="text-gray-400">info@klinikehat.id</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>📍</span>
                            <span class="text-gray-400">Jl. Kesehatan No. 123, Jakarta</span>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h4 class="font-bold text-white mb-4">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <span class="text-white">f</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <span class="text-white">𝕏</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <span class="text-white">📸</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <span class="text-white">▶</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 Klinik Sehat. Hak cipta dilindungi. Semua hak reservasi.</p>
            </div>
        </div>
    </footer>

    <!-- ========== SCRIPTS ========== -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Day Tabs - Schedule Filter
        const dayTabs = document.querySelectorAll('.day-tab');
        const doctorCards = document.querySelectorAll('.doctor-card');

        dayTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const selectedDay = tab.getAttribute('data-day');

                // Update active tab
                dayTabs.forEach(t => {
                    t.classList.remove('bg-blue-600', 'text-white');
                    t.classList.add('bg-gray-200', 'text-gray-700');
                });
                tab.classList.remove('bg-gray-200', 'text-gray-700');
                tab.classList.add('bg-blue-600', 'text-white');

                // Filter doctor cards
                doctorCards.forEach(card => {
                    if (card.getAttribute('data-day') === selectedDay) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Initialize - Show Monday doctors by default
        doctorCards.forEach(card => {
            if (card.getAttribute('data-day') !== 'senin') {
                card.style.display = 'none';
            }
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Tutup menu mobile setelah mengklik link
                    if (!mobileMenu.classList.contains('hidden')) {
                         mobileMenu.classList.add('hidden');
                    }
                }
            });
        });

        // Mengganti placeholder gambar yang rusak
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('img').forEach(function(img) {
                img.onerror = function() {
                    const width = img.getAttribute('width') || img.clientWidth || 100;
                    const height = img.getAttribute('height') || img.clientHeight || 100;
                    this.src = `https://placehold.co/${width}x${height}/E0E7FF/3B82F6?text=Image`;
                    this.onerror = null; // Mencegah loop tak terbatas jika placeholder juga gagal
                };
            });
        });
    </script>
</body>
</html>