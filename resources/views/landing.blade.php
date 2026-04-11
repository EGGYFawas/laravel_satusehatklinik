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

    // Format Nomor WhatsApp (Otomatis mengubah awalan 0 jadi 62)
    $waNumber = preg_replace('/[^0-9]/', '', $phone);
    if (str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
    $waLink = "https://wa.me/" . $waNumber;

    // Relasi Data Master
    $doctors = class_exists('\App\Models\Doctor') ? \App\Models\Doctor::with(['user', 'doctorSchedules'])->get() : collect([]);
    $articles = class_exists('\App\Models\Article') ? \App\Models\Article::latest()->take(3)->get() : collect([]);
    
    if (class_exists('\App\Models\Poli')) {
        $polis = \App\Models\Poli::all();
    } elseif (class_exists('\App\Models\Polyclinic')) {
        $polis = \App\Models\Polyclinic::all();
    } else {
        $polis = collect([]);
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $clinicName }} - Layanan Kesehatan Terpercaya</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $primaryColor }}',
                        secondary: '{{ $secondaryColor }}',
                    },
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
    body { font-family: 'Poppins', sans-serif; }
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
    
    <!-- NAVBAR FIX -->
    <nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('landing') ?? url('/') }}" class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-secondary shadow-sm flex items-center justify-center bg-white">
                            <img src="{{ $logoUrl }}" alt="Logo Klinik" class="h-full w-full object-cover scale-125" onerror="this.src='https://placehold.co/60x60/0284C7/FFFFFF?text=Logo'; this.onerror=null;">
                        </div>
                        <span class="text-xl font-bold tracking-tight text-gray-800 uppercase leading-tight hidden sm:block">
                            Sistem Informasi <br> <span class="text-primary text-lg">{{ $clinicName }}</span>
                        </span>
                    </a>
                </div>

                <div class="hidden lg:flex items-center gap-8">
                    <a href="#beranda" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Beranda</a>
                    <a href="#mengapa-kami" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Mengapa Kami</a>
                    <a href="#jadwal-dokter" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Jadwal Dokter</a>
                    <a href="#tentang-kami" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Tentang Kami</a>
                    <a href="#layanan" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Layanan</a>
                    <a href="#artikel" class="text-sm font-semibold text-gray-600 hover:text-primary transition">Artikel</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') ?? url('/admin') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition shadow-md">
                            Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition shadow-md">
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
                <a href="#beranda" class="block px-4 py-2 text-gray-600 hover:text-primary transition">Beranda</a>
                <a href="#mengapa-kami" class="block px-4 py-2 text-gray-600 hover:text-primary transition">Mengapa Kami</a>
                <a href="#jadwal-dokter" class="block px-4 py-2 text-gray-600 hover:text-primary transition">Jadwal Dokter</a>
                <a href="#tentang-kami" class="block px-4 py-2 text-gray-600 hover:text-primary transition">Tentang Kami</a>
                <a href="#layanan" class="block px-4 py-2 text-gray-600 hover:text-primary transition">Layanan</a>
                <div class="flex flex-col gap-3 mt-4 px-4">
                    @auth
                        <a href="{{ route('dashboard') ?? url('/admin') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-lg hover:bg-primary/90 transition font-medium">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-lg hover:bg-primary/90 transition font-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="w-full text-center border-2 border-primary text-primary px-4 py-3 rounded-lg hover:bg-secondary transition font-medium">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HEADER BERANDA -->
    <section id="beranda" class="relative pt-32 pb-16 md:pt-40 md:pb-24 bg-cover bg-center" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ $clinicSetting?->hero_image ? asset('storage/' . $clinicSetting->hero_image) : asset('assets/img/hero-doctor.jpg') }}')">
         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                 <div class="space-y-6">
                     <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">
                         {{ $clinicSetting?->hero_title ?? 'Kesehatan Anda Adalah Prioritas Kami' }}
                     </h1>
                     <p class="text-lg text-gray-200 leading-relaxed">
                         {{ $clinicName }} menyediakan layanan kesehatan terpercaya dengan dokter berpengalaman dan fasilitas modern.
                     </p>
                     <div class="flex flex-col sm:flex-row gap-4 pt-4">
                         @auth
                             <a href="{{ route('dashboard') ?? url('/admin') }}" class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary/90 transition font-medium text-center shadow-lg">Dashboard Saya</a>
                         @else
                             <a href="{{ route('login') }}" class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary/90 transition font-medium text-center shadow-lg">Buat Antrian Berobat</a>
                         @endauth
                         <a href="#panduan" class="border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white/10 transition font-medium text-center">Panduan Penggunaan</a>
                     </div>
                 </div>
             </div>
         </div>
     </section>

    <!-- MENGAPA KAMI -->
    <section id="mengapa-kami" class="pt-32 pb-16 md:pt-40 md:pb-24 bg-cover bg-center" 
             style="background-image: linear-gradient(rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.85)), url('{{ $clinicSetting?->why_us_image ? asset('storage/' . $clinicSetting->why_us_image) : asset('assets/img/why-us1.jpg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih {{ $clinicName }}?</h2>
                <p class="text-lg text-gray-800 max-w-2xl mx-auto font-medium">
                    Kami merevolusi cara Anda berobat. Lupakan antrean panjang dan ketidakpastian. Dengan sistem kami, seluruh proses dari pendaftaran hingga pengambilan obat kini ada dalam genggaman Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-lg hover:shadow-xl transition border-t-4 border-primary">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Pendaftaran Real-time</h3>
                    <p class="text-gray-600 text-center">Sistem kami memungkinkan Anda untuk mendaftarkan diri sendiri dan mendapatkan nomor antrean secara instan melalui website.</p>
                </div>
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-lg hover:shadow-xl transition border-t-4 border-primary">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.757a1 1 0 01-.940 1.017 48.993 48.993 0 01-5.674 0 1 1 0 01-.94-1.017V6.517c0-1.667.341-3.252.975-4.62zM6 12a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Waktu Fleksibel</h3>
                    <p class="text-gray-600 text-center">Lihat jadwal dokter yang tersedia dan pilih waktu kunjungan yang paling sesuai dengan kesibukan Anda.</p>
                </div>
                <div class="bg-white/95 backdrop-blur-sm p-8 rounded-xl shadow-lg hover:shadow-xl transition border-t-4 border-primary">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rekam Medis Digital</h3>
                    <p class="text-gray-600 text-center">Setiap kunjungan, diagnosis, dan resep obat akan tercatat otomatis dan aman dalam riwayat rekam medis Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- JADWAL DOKTER -->
    <section id="jadwal-dokter" class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Jadwal Dokter</h2>
                <p class="text-lg text-gray-600">Pilih hari untuk melihat jadwal dokter spesialis kami</p>
            </div>

            <div class="flex flex-wrap gap-3 justify-center mb-12">
                <button class="day-tab active px-6 py-2 rounded-full bg-primary text-white font-medium transition shadow-sm" data-day="senin">Senin</button>
                <button class="day-tab px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-700 font-medium hover:bg-primary hover:text-white transition shadow-sm" data-day="selasa">Selasa</button>
                <button class="day-tab px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-700 font-medium hover:bg-primary hover:text-white transition shadow-sm" data-day="rabu">Rabu</button>
                <button class="day-tab px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-700 font-medium hover:bg-primary hover:text-white transition shadow-sm" data-day="kamis">Kamis</button>
                <button class="day-tab px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-700 font-medium hover:bg-primary hover:text-white transition shadow-sm" data-day="jumat">Jumat</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($doctors as $doctor)
                    @if($doctor->doctorSchedules)
                        @foreach ($doctor->doctorSchedules->groupBy('day_of_week') as $day => $schedulesOnDay)
                            <div class="doctor-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition" data-day="{{ Str::lower($day) }}">
                                <div class="flex items-center gap-4 mb-5">
                                    <img src="{{ $doctor->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($doctor->user->name ?? 'Dr').'&background=random' }}" alt="{{ $doctor->user->name ?? 'Dokter' }}" class="w-16 h-16 rounded-full object-cover border-2 border-secondary">
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $doctor->user->name ?? 'Dr. Anonim' }}</h3>
                                        <p class="text-sm text-primary font-semibold">{{ $doctor->specialization ?? 'Umum' }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 flex-wrap">
                                    @foreach ($schedulesOnDay as $schedule)
                                        <span class="bg-secondary text-primary font-medium text-xs px-4 py-1.5 rounded-full border border-primary/10">
                                            <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">Jadwal dokter belum tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- TENTANG KAMI -->
    <section id="tentang-kami" class="py-20 md:py-24 bg-secondary overflow-hidden relative">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-primary/20 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-primary/20 opacity-50 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20 items-center">
                
                <div class="relative hidden md:block group">
                    <div class="absolute top-6 -left-6 w-full h-full bg-primary/30 rounded-3xl -z-10 transition-transform duration-500 group-hover:rotate-2"></div>
                    
                    <div class="relative rounded-3xl overflow-hidden border-4 border-white shadow-2xl">
                        <img src="{{ $clinicSetting?->about_us_image ? asset('storage/' . $clinicSetting->about_us_image) : 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=800' }}" 
                             alt="Interior Klinik" 
                             class="w-full h-[500px] object-cover hover:scale-105 transition-transform duration-700">
                        <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>

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

                <div class="space-y-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-primary/20 text-primary text-sm font-bold mb-4 shadow-sm">
                            <i class="fas fa-hospital"></i> Tentang Kami
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                            {{ $clinicSetting?->about_us_title ?? 'Mitra Kesehatan Terpercaya untuk Keluarga Anda' }}
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- DESKRIPSI DINAMIS MENDUKUNG PARAGRAF/ENTER -->
                        <p class="text-gray-600 text-lg leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($clinicSetting?->about_us_description ?? "Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik di Indonesia. Kami percaya bahwa akses kesehatan haruslah mudah, cepat, dan transparan.\n\nKlinik Sehat tidak hanya sekedar tempat berobat, tetapi partner dalam menjaga kualitas hidup Anda.")) !!}
                        </p>
                    </div>

                    <ul class="space-y-3 pt-2">
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
                </div>
            </div>
        </div>
    </section>

    <!-- LAYANAN POLI DINAMIS -->
    <section id="layanan" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
                <p class="text-lg text-gray-600">Berbagai layanan kesehatan komprehensif untuk kebutuhan Anda</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($polis as $poli)
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                        <div class="w-14 h-14 bg-secondary rounded-xl flex items-center justify-center mb-6">
                            <i class="{{ $poli->icon ?? 'fas fa-stethoscope' }} text-primary text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $poli->name }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $poli->description ?? 'Layanan konsultasi kesehatan profesional.' }}</p>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition">
                        <div class="w-14 h-14 bg-secondary rounded-xl flex items-center justify-center mb-6"><i class="fas fa-stethoscope text-primary text-2xl"></i></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Poli Umum</h3>
                        <p class="text-gray-600">Pemeriksaan kesehatan rutin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- PANDUAN -->
    <section id="panduan" class="py-16 md:py-24 bg-primary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Cara Daftar Antrean Berobat</h2>
                <p class="text-lg text-white/80">Ikuti langkah-langkah mudah berikut</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Line connector for desktop -->
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-white/20"></div>
                
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-white text-primary rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg shadow-black/10">1</div>
                    <h3 class="text-xl font-bold mb-3">Daftar/Login</h3>
                    <p class="text-white/80 text-sm leading-relaxed">Buat akun atau masuk jika sudah terdaftar di sistem kami.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-white text-primary rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg shadow-black/10">2</div>
                    <h3 class="text-xl font-bold mb-3">Ambil Antrean</h3>
                    <p class="text-white/80 text-sm leading-relaxed">Pilih menu "Ambil Antrian" dan lengkapi detail pendaftaran.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-white text-primary rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg shadow-black/10">3</div>
                    <h3 class="text-xl font-bold mb-3">Check-in Klinik</h3>
                    <p class="text-white/80 text-sm leading-relaxed">Scan QR di meja Administrasi sebelum memulai pemeriksaan.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-white text-primary rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-lg shadow-black/10">4</div>
                    <h3 class="text-xl font-bold mb-3">Selesai & Apotek</h3>
                    <p class="text-white/80 text-sm leading-relaxed">Dapatkan nomor antrean apotek otomatis untuk tebus obat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ARTIKEL -->
    <section id="artikel" class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Artikel Kesehatan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($articles as $article)
                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100">
                        <a href="{{ route('artikel.show', $article->slug) }}" class="block overflow-hidden group">
                            <img src="{{ asset('storage/' . $article->image_url) }}" alt="{{ $article->title }}" class="w-full h-52 object-cover transition duration-500 group-hover:scale-105">
                        </a>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 line-clamp-2 hover:text-primary transition">
                                <a href="{{ route('artikel.show', $article->slug) }}">{{ $article->title }}</a>
                            </h3>
                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <a href="{{ route('artikel.show', $article->slug) }}" class="text-primary font-bold hover:text-primary/80 inline-flex items-center transition">
                                    Baca Selengkapnya <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12"><p class="text-gray-500">Belum ada artikel.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FOOTER ASLI DENGAN FITUR WA & EMAIL -->
    <footer class="bg-slate-900 text-slate-300 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16 border-b border-slate-800 pb-16">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg overflow-hidden bg-white flex items-center justify-center">
                             @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo" class="h-full w-full object-cover scale-125" onerror="this.src='https://placehold.co/40x40/0284C7/FFFFFF?text=Logo'; this.onerror=null;">
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
                        <li><a href="#beranda" class="hover:text-primary transition-colors text-sm">Beranda</a></li>
                        <li><a href="#tentang-kami" class="hover:text-primary transition-colors text-sm">Tentang Kami</a></li>
                        <li><a href="#layanan" class="hover:text-primary transition-colors text-sm">Layanan Medis</a></li>
                        <li><a href="#jadwal-dokter" class="hover:text-primary transition-colors text-sm">Jadwal Dokter</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Layanan Populer</h4>
                    <ul class="space-y-3">
                        @forelse($polis->take(4) as $poli)
                            <li><a href="#layanan" class="hover:text-primary transition-colors text-sm">{{ $poli->name }}</a></li>
                        @empty
                            <li><a href="#layanan" class="hover:text-primary transition-colors text-sm">Poli Umum</a></li>
                            <li><a href="#layanan" class="hover:text-primary transition-colors text-sm">Poli Gigi & Mulut</a></li>
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

    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Berhasil menyalin: ' + text);
            });
        }
    </script>
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

                // Update active tab styles dynamically using the new theme colors
                dayTabs.forEach(t => {
                    t.classList.remove('bg-primary', 'text-white');
                    t.classList.add('bg-gray-200', 'text-gray-700');
                });
                tab.classList.remove('bg-gray-200', 'text-gray-700');
                tab.classList.add('bg-primary', 'text-white');

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