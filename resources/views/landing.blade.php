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
    
    <!-- Font Awesome untuk Icon (Penting untuk dekorasi baru) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Script Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                    }
                }
            }
        }
    </script>
    
    <style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Efek Fade Halus */
    #beranda::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.95));
        pointer-events: none;
        z-index: 10;
    }
    </style>
</head>
<body class="bg-white text-gray-900 font-poppins">
    
    <!-- ========== NAVBAR ========== -->
    <nav class="fixed top-0 left-0 right-0 bg-[rgba(226,219,219,0.7)] backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/logo_login.png') }}" alt="Klinik Sehat Logo" class="h-20 w-auto"
                         onerror="this.src='https://placehold.co/100x40/0284C7/FFFFFF?text=Logo'; this.onerror=null;">
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex gap-8">
                    <a href="#beranda" class="text-gray-600 hover:text-blue-600 transition">Beranda</a>
                    <a href="#mengapa-kami" class="text-gray-600 hover:text-blue-600 transition">Mengapa Kami</a>
                    <a href="#jadwal-dokter" class="text-gray-600 hover:text-blue-600 transition">Jadwal Dokter</a>
                    <a href="#tentang-kami" class="text-gray-600 hover:text-blue-600 transition">Tentang Kami</a>
                    <a href="#layanan" class="text-gray-600 hover:text-blue-600 transition">Layanan</a>
                    <a href="#artikel" class="text-gray-600 hover:text-blue-600 transition">Artikel</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex gap-4">
                  <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Masuk</a>
                  <a href="{{ route('register') }}" class="border-2 border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Daftar</a>
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
                <a href="#beranda" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition">Beranda</a>
                <a href="#mengapa-kami" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition">Mengapa Kami</a>
                <a href="#jadwal-dokter" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition">Jadwal Dokter</a>
                <a href="#tentang-kami" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition">Tentang Kami</a>
                <a href="#layanan" class="block px-4 py-2 text-gray-600 hover:text-blue-600 transition">Layanan</a>
                <div class="flex gap-3 mt-4 px-4">
                    <a href="{{ route('login') }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Masuk</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-medium">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== HERO SECTION (DINAMIS) ========== -->
    <!-- 
        LOGIKA: Cek apakah ada data 'hero_image' dari database. 
        Jika ada -> Pakai Storage::url(...)
        Jika tidak -> Pakai asset default
    -->
    <section id="beranda" class="relative pt-32 pb-16 md:pt-40 md:pb-24 bg-cover bg-center" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ !empty($content['hero_image']) ? Storage::url($content['hero_image']) : asset('assets/img/hero-doctor.jpg') }}')">
         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                 <div class="space-y-6">
                     <!-- JUDUL DINAMIS -->
                     <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">
                         {{ $content['hero_title'] ?? 'Kesehatan Anda Adalah Prioritas Kami' }}
                     </h1>
                     
                     <p class="text-lg text-gray-200 leading-relaxed">
                         Klinik Sehat menyediakan layanan kesehatan terpercaya dengan dokter berpengalaman dan fasilitas modern untuk menjaga kesehatan Anda dan keluarga.
                     </p>
                     
                     <div class="flex flex-col sm:flex-row gap-4 pt-4">
                         @guest
                             <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium text-center">
                                 Buat Antrian Berobat
                             </a>
                         @else
                             <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-medium text-center">
                                 Buat Antrian Berobat
                             </a>
                         @endguest
                         
                         <a href="#panduan" class="border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white/10 transition font-medium text-center">
                             Panduan Penggunaan
                         </a>
                     </div>
                 </div>
             </div>
         </div>
     </section>

    <!-- ========== MENGAPA KAMI ========== -->
    <section id="mengapa-kami" class="pt-32 pb-16 md:pt-40 md:pb-24 bg-cover bg-center" style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('{{ asset('assets/img/why-us1.jpg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih Klinik Sehat?</h2>
                <!-- Teks ini dikembalikan lengkap sesuai aslinya -->
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami merevolusi cara Anda berobat. Lupakan antrean panjang dan ketidakpastian. Dengan sistem kami, seluruh proses dari pendaftaran hingga pengambilan obat kini ada dalam genggaman Anda, memberikan kenyamanan, kecepatan, dan kendali penuh atas kesehatan Anda.
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
                    <p class="text-gray-600 text-center">Sistem kami memungkinkan Anda untuk mendaftarkan diri sendiri atau keluarga dan mendapatkan nomor antrean secara instan melalui website. Pantau pergerakan antrean secara real-time dari ponsel Anda.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-blue-50 p-8 rounded-lg hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.757a1 1 0 01-.940 1.017 48.993 48.993 0 01-5.674 0 1 1 0 01-.94-1.017V6.517c0-1.667.341-3.252.975-4.62zM6 12a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Waktu Fleksibel & Terkendali Sepenuhnya</h3>
                    <p class="text-gray-600 text-center">Lihat jadwal dokter yang tersedia dan pilih waktu kunjungan yang paling sesuai dengan kesibukan Anda. Perlu membatalkan karena ada urusan mendadak? Lakukan pembatalan dengan mudah.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-blue-50 p-8 rounded-lg hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rekam Medis Digital & Terintegrasi</h3>
                    <p class="text-gray-600 text-center">Riwayat Kesehatan Anda Aman dan Selalu Tersedia. Setiap kunjungan, diagnosis, dan resep obat akan tercatat secara otomatis dan aman dalam riwayat rekam medis digital Anda.</p>
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
                <button class="day-tab active px-6 py-2 rounded-lg bg-blue-600 text-white font-medium transition" data-day="senin">Senin</button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="selasa">Selasa</button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="rabu">Rabu</button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="kamis">Kamis</button>
                <button class="day-tab px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-blue-600 hover:text-white transition" data-day="jumat">Jumat</button>
            </div>

            <!-- Doctor Schedule Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($doctors as $doctor)
                    @foreach ($doctor->doctorSchedules->groupBy('day_of_week') as $day => $schedulesOnDay)
                        
                        <div class="doctor-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition" 
                             data-day="{{ Str::lower($day) }}">
                             
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{ $doctor->user->photo_url ?? 'https://placehold.co/80x80/E0E7FF/3B82F6?text=Dr' }}"
                                     alt="{{ $doctor->user->name }}" class="w-16 h-16 rounded-full object-cover">
                                
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
        </div>
    </section>

    <!-- ========== TENTANG KAMI SECTION (DINAMIS & CANTIK) ========== -->
    <section id="tentang-kami" class="py-20 md:py-24 bg-blue-50 overflow-hidden relative">
        <!-- Background Pattern -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-blue-100 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-100 opacity-50 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20 items-center">
                
                <!-- IMAGE COMPOSITION AREA -->
                <div class="relative hidden md:block group">
                    <div class="absolute top-6 -left-6 w-full h-full bg-blue-200 rounded-3xl -z-10 transition-transform duration-500 group-hover:rotate-2"></div>
                    
                    <div class="relative rounded-3xl overflow-hidden border-4 border-white shadow-2xl">
                        <!-- GAMBAR DINAMIS -->
                        <img src="{{ !empty($content['about_us_image']) ? Storage::url($content['about_us_image']) : 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=800' }}" 
                             alt="Interior Klinik Sehat Modern" 
                             class="w-full h-[500px] object-cover hover:scale-105 transition-transform duration-700">
                        
                        <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>

                    <!-- Floating Badge -->
                    <div class="absolute -bottom-6 -right-6 bg-white p-5 rounded-2xl shadow-xl border border-gray-100 animate-bounce-slow max-w-xs">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 p-3 rounded-full shrink-0">
                                <i class="fas fa-user-md text-green-600 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Tim Medis</p>
                                <p class="font-bold text-gray-900 text-lg">Dokter Spesialis</p>
                                <div class="flex text-yellow-400 text-xs mt-1">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTENT AREA -->
                <div class="space-y-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-600 text-sm font-semibold mb-4">
                            <i class="fas fa-hospital"></i> Tentang Kami
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                            <!-- JUDUL DINAMIS -->
                            {{ $content['about_us_title'] ?? 'Mitra Kesehatan Terpercaya untuk Keluarga Anda' }}
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        <p class="text-gray-600 text-lg leading-relaxed">
                            Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik di Indonesia. Kami percaya bahwa akses kesehatan haruslah mudah, cepat, dan transparan.
                        </p>
                        <p class="text-gray-600 leading-relaxed">
                            Klinik Sehat tidak hanya sekedar tempat berobat, tetapi partner dalam menjaga kualitas hidup Anda. Didukung oleh teknologi sistem antrian modern dan rekam medis terintegrasi, kami memastikan setiap kunjungan Anda menjadi pengalaman yang menyenangkan.
                        </p>
                    </div>

                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700 font-medium">Fasilitas medis modern & steril</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700 font-medium">Pelayanan ramah & profesional</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700 font-medium">Apotek lengkap & terintegrasi</span>
                        </li>
                    </ul>
                    
                   <!-- <div class="pt-2">
                        <button class="group bg-blue-600 text-white px-8 py-3 rounded-xl font-medium hover:bg-blue-700 transition shadow-lg hover:shadow-blue-200 flex items-center gap-2">
                            Selengkapnya 
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div> -->
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

    <!-- ========== PANDUAN PESAN JANJI ========== -->
    <section id="panduan" class="py-16 md:py-24 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Cara Daftar Antrean Berobat Pasien</h2>
                <p class="text-lg text-blue-100">Ikuti langkah-langkah mudah berikut untuk membuat Antrean Berobat </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                    <h3 class="text-lg font-bold mb-2">Daftar/Login</h3>
                    <p class="text-blue-100">Daftar akun terlebih dahulu jika sudah bisa langsung login ke aplikasi Klinik Sehat</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                    <h3 class="text-lg font-bold mb-2">Ambil Antrean</h3>
                    <p class="text-blue-100">Pada Halaman Dashboard Pilih menu "Ambil Antrian" kemudian isi pada detail pendaftaran yang dituju </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                    <h3 class="text-lg font-bold mb-2">Antrean Berobat</h3>
                    <p class="text-blue-100">Setelah mendapatkan nomor antrean silahkan melakukan checkin terlebih dahulu sebelum memulai pemeriksaan (Wajib), Kode QR Check-in tersedia di meja Administrasi Klinik setelah itu tunggu sampai di panggil untuk melakukan pemeriksaan </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">4</div>
                    <h3 class="text-lg font-bold mb-2">Antrean Apotek</h3>
                    <p class="text-blue-100">Setelah melakukan pemeriksaan sistem otomatis memberikan nomor antrean apotek lalu tunggu sampai nomor antrean anda dipanggil </p>
                </div>
            </div>
        </div>
    </section>
<!-- ========== ARTIKEL ========== -->
<section id="artikel" class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Artikel Kesehatan</h2>
            <p class="text-lg text-gray-600">Informasi dan tips kesehatan terkini dari para ahli</p>
        </div>

        {{-- Grid Artikel --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse ($articles as $article)
                <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                    
                    {{-- Gambar (Sekarang bisa diklik) --}}
                    <a href="{{ route('artikel.show', $article->slug) }}" class="block overflow-hidden group">
                        <img src="{{ asset('storage/' . $article->image_url) }}" 
                             alt="{{ $article->title }}" 
                             class="w-full h-48 object-cover transition duration-500 group-hover:scale-105"
                             onerror="this.src='https://placehold.co/600x400/E0E7FF/3B82F6?text=Artikel+Kesehatan'; this.onerror=null;">
                    </a>

                    <div class="p-6 flex flex-col flex-grow">
                        {{-- Kategori Label --}}
                        <div class="mb-3">
                            <span class="inline-block bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">
                                Info Kesehatan
                            </span>
                        </div>

                        {{-- Judul (Bisa diklik) --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600 transition">
                            <a href="{{ route('artikel.show', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </h3>

                        {{-- Cuplikan Isi --}}
                        <p class="text-gray-600 mb-4 text-sm line-clamp-3 flex-grow">
                            {{ Str::limit(strip_tags($article->content), 100, '...') }}
                        </p>

                        {{-- Tombol Baca Selengkapnya --}}
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <a href="{{ route('artikel.show', $article->slug) }}" class="text-blue-600 font-medium hover:text-blue-800 inline-flex items-center transition">
                                Baca Selengkapnya 
                                <svg class="w-4 h-4 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                {{-- State Kosong --}}
                <div class="col-span-1 md:col-span-3 text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <p class="text-gray-600 font-medium">Belum ada artikel untuk ditampilkan saat ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Tombol Lihat Semua (Navigasi ke Index) --}}
        @if(isset($articles) && $articles->isNotEmpty())
            <div class="text-center mt-12">
                <a href="{{ route('artikel.index') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg transition shadow-md hover:shadow-lg">
                    Lihat Semua Artikel
                </a>
            </div>
        @endif
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

                <!-- Contact Info (DINAMIS TELEPON) -->
                <div>
                    <h4 class="font-bold text-white mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <span>📞</span>
                            <span class="text-gray-400">{{ $content['contact_phone'] ?? '(021) 1234-5678' }}</span>
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
                    this.onerror = null; 
                };
            });
        });
    </script>
</body>
</html>