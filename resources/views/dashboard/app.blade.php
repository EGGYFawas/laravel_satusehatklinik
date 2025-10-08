@extends('layouts.guest')

@section('title', 'Klinik Sehat - Landing Page')

@push('styles')
<style>
    /* Definisi variabel warna agar konsisten dengan desain */
    :root {
        --brand-primary: #3B82F6; /* Blue-500 */
        --brand-secondary: #6B7280; /* Gray-500 */
        --brand-background: #F3F4F6; /* Gray-100 */
        --text-dark: #1F2937; /* Gray-800 */
        --text-grey: #4B5563; /* Gray-600 */
        --brand-light: #FFFFFF;
    }

    body {
        font-family: 'Inter', sans-serif; /* Menggunakan font yang lebih modern */
    }
    
    .bg-brand-background {
        background-color: var(--brand-background);
    }
    
    .schedule-panel.hidden {
        display: none;
    }

    /* Styling untuk tombol-tombol */
    .btn {
        @apply inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out;
    }
    .btn-primary {
        @apply bg-brand-primary text-white border border-transparent hover:opacity-90;
    }
    .btn-secondary {
        @apply bg-white text-brand-primary border border-gray-300 hover:bg-gray-50;
    }
    .btn-light {
        @apply bg-white text-brand-primary border-2 border-white hover:bg-transparent hover:text-white;
    }
    .btn-outline-light {
        @apply bg-transparent text-white border-2 border-white hover:bg-white hover:text-brand-primary;
    }

    /* Styling untuk link navigasi */
    .nav-link {
        @apply text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full;
    }
    
    /* Efek hover untuk tombol hari */
    #day-buttons .day-button:not(.bg-brand-primary):hover {
        background-color: #e5e7eb;
        color: #1f2937;
    }
</style>
{{-- Import Font Inter --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')
@php
// Data ini nantinya akan diambil dari database melalui controller
$schedulesByDay = [
    'senin' => [
        ['poli' => 'Poli Umum', 'doctor' => 'dr. Anisa Pujianti', 'time' => '08:00 - 12:00 WIB'],
        ['poli' => 'Poli Ibu dan Anak', 'doctor' => 'dr. Budiman Fawwas', 'time' => '08:00 - 12:00 WIB'],
    ],
    'selasa' => [
        ['poli' => 'Poli Umum', 'doctor' => 'dr. Anisa Pujianti', 'time' => '08:00 - 12:00 WIB'],
        ['poli' => 'Poli Ibu dan Anak', 'doctor' => 'dr. Budi Santoso', 'time' => '12:00 - 15:00 WIB'],
        ['poli' => 'Poli Gigi', 'doctor' => 'dr. Amelia Lestari', 'time' => '13:00 - 17:00 WIB'],
    ],
    'rabu' => [
        ['poli' => 'Poli Gigi', 'doctor' => 'dr. Amelia Lestari', 'time' => '09:00 - 13:00 WIB'],
        ['poli' => 'Poli Ibu dan Anak', 'doctor' => 'dr. Budiman Fawwas', 'time' => '12:00 - 15:00 WIB'],
        ['poli' => 'Poli Umum', 'doctor' => 'dr. Anisa Pujianti', 'time' => '13:00 - 17:00 WIB'],
    ],
    'kamis' => [
        ['poli' => 'Poli Ibu dan Anak', 'doctor' => 'dr. Budi Santoso', 'time' => '14:00 - 18:00 WIB'],
        ['poli' => 'Poli Gigi', 'doctor' => 'dr. Berlian', 'time' => '13:00 - 17:00 WIB'],
    ],
    'jumat' => [
        ['poli' => 'Poli Umum', 'doctor' => 'dr. Anisa Pujianti', 'time' => '08:00 - 12:00 WIB'],
        ['poli' => 'Poli Gigi', 'doctor' => 'dr. Amelia Lestari', 'time' => '13:00 - 17:00 WIB'],
    ],
    'sabtu' => [], // Tidak ada jadwal
];
@endphp
<!-- Header -->
<header class="bg-white py-4 sticky top-0 z-[1000] shadow-md">
    <div class="max-w-7xl mx-auto px-6">
        <nav class="flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-brand-primary no-underline">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                </svg>
                Klinik Sehat
            </a>
            <ul class="hidden lg:flex gap-10 list-none">
                <li><a href="#beranda" class="nav-link">Beranda</a></li>
                <li><a href="#layanan" class="nav-link">Layanan Kami</a></li>
                <li><a href="#jadwal-dokter" class="nav-link">Dokter</a></li>
                <li><a href="#tentang" class="nav-link">Tentang Kami</a></li>
                <li><a href="#artikel" class="nav-link">Artikel Kesehatan</a></li>
            </ul>

            @auth
            <div class="hidden lg:flex gap-4">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Keluar</button>
                </form>
            </div>
            @else
            <div class="hidden lg:flex gap-4">
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Daftar</a>
            </div>
            @endauth

            <div class="lg:hidden cursor-pointer z-[1010]" id="burger-menu">
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300"></div>
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300"></div>
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300"></div>
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Navigation -->
<div class="fixed top-0 h-screen w-[70%] bg-white shadow-lg z-[1005] transition-transform duration-300 ease-in-out flex flex-col items-center justify-center pt-20 -right-full" id="mobile-nav">
    <ul class="list-none w-full text-center">
        <li class="my-5"><a href="#beranda" class="nav-link-mobile">Beranda</a></li>
        <li class="my-5"><a href="#layanan" class="nav-link-mobile">Layanan Kami</a></li>
        <li class="my-5"><a href="#jadwal-dokter" class="nav-link-mobile">Dokter</a></li>
        <li class="my-5"><a href="#tentang" class="nav-link-mobile">Tentang Kami</a></li>
        <li class="my-5"><a href="#artikel" class="nav-link-mobile">Artikel Kesehatan</a></li>
    </ul>
    <div class="mt-10 flex flex-col gap-4 w-4/5">
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary w-full">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="btn btn-secondary w-full">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary w-full">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-secondary w-full">Daftar</a>
        @endauth
    </div>
</div>

<main class="overflow-x-hidden bg-brand-background">
    <!-- Hero Section -->
    <section id="beranda" class="relative min-h-[90vh] bg-cover bg-center flex items-center text-white" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-blue-600/70"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-[1]">
            <div class="max-w-xl text-center md:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-[56px] leading-tight font-bold mb-4">Kami Hadir untuk Anda.</h1>
                <p class="text-base md:text-lg mb-8 max-w-lg mx-auto md:mx-0">Kesehatan adalah fondasi utama untuk mencapai kebahagiaan dan kualitas hidup yang lebih baik. Dengan perawatan yang tepat sejak dini, Anda bisa menikmati hari-hari penuh energi dan produktivitas.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ route('register') }}" class="btn btn-light">Buat Antrean Berobat</a>
                    <a href="#panduan" class="btn btn-outline-light">Panduan Penggunaan</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section id="layanan" class="py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-4 text-text-dark">Mengapa Memilih Kami ?</h2>
            <p class="text-center text-base md:text-lg text-text-grey max-w-3xl mx-auto mb-12">Kami merevolusi cara Anda berobat, lupakan antrean panjang dan ketidakpastian. Dengan sistem kami, seluruh proses dari pendaftaran hingga pengambilan obat kini ada dalam genggaman Anda, memberikan kenyamanan, kecepatan, dan kendali penuh atas kesehatan Anda.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold mb-2">Pendaftaran 100% Online & Real-time</h3>
                    <p class="text-text-grey text-sm">Daftar dari mana saja, kapan saja. Pantau nomor antrean Anda secara langsung dari ponsel, hilangkan waktu tunggu yang tidak perlu.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
                    <h3 class="text-lg font-semibold mb-2">Waktu Fleksibel & Terkendali Sepenuhnya</h3>
                    <p class="text-text-grey text-sm">Pilih jadwal dokter dan waktu kunjungan yang paling sesuai dengan kesibukan Anda. Sistem kami memberikan notifikasi dan estimasi waktu panggilan.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold mb-2">Rekam Medis Digital & Terintegrasi</h3>
                    <p class="text-text-grey text-sm">Akses riwayat kesehatan, diagnosis, dan resep obat Anda aman dan terpusat untuk diagnosis yang lebih akurat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Schedule Section -->
    <section id="jadwal-dokter" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-12 text-text-dark">Jadwal Dokter Klinik Sehat</h2>
            <div class="flex justify-center mb-8">
                <div id="day-buttons" class="flex flex-wrap justify-center space-x-1 bg-gray-200 p-1 rounded-full">
                    @foreach (array_keys($schedulesByDay) as $day)
                        <button data-day="{{ $day }}" class="day-button px-6 py-2 text-sm font-medium rounded-full transition-colors duration-200">{{ ucfirst($day) }}</button>
                    @endforeach
                </div>
            </div>

            <div id="schedule-panels">
                 @foreach ($schedulesByDay as $day => $schedules)
                    <div id="{{ $day }}" class="schedule-panel hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($schedules as $schedule)
                            <div class="bg-brand-primary text-white p-5 rounded-lg shadow-lg flex items-center gap-4">
                                <div class="bg-white/20 p-3 rounded-full">
                                   <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-md">{{ $schedule['poli'] }}</h3>
                                    <p class="text-sm opacity-90">{{ $schedule['doctor'] }}</p>
                                    <p class="text-sm opacity-70 mt-1 flex items-center"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $schedule['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500 py-8">
                                Tidak ada jadwal dokter pada hari ini.
                            </div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="tentang" class="py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                <div class="flex-1">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop"
                        alt="Tentang Klinik Sehat" class="w-full rounded-2xl shadow-xl">
                </div>
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-text-dark">Tentang Kami</h2>
                    <p class="text-text-grey leading-relaxed text-justify">Klinik Sehat lahir dari sebuah gagasan untuk memodernisasi dan menyederhanakan akses layanan kesehatan primer di Indonesia. Kami percaya bahwa setiap orang berhak mendapatkan perawatan kesehatan berkualitas tanpa harus mengorbankan waktu dan kenyamanan. Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik, di mana setiap pasien dapat mengelola kesehatannya dengan kendali penuh di ujung jari mereka.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Medical Services Section -->
    <section id="layanan-medis" class="py-16 md:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-text-dark">Layanan Medis Klinik Sehat</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                    <h3 class="text-lg font-semibold text-text-dark">Poli Umum</h3>
                </div>
                <div class="bg-brand-primary text-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow transform hover:-translate-y-2">
                    <h3 class="text-lg font-semibold">Poli Ibu & Anak</h3>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow">
                    <h3 class="text-lg font-semibold text-text-dark">Poli Gigi</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Guide Section -->
    <section id="panduan" class="py-16 md:py-20">
        <div class="max-w-5xl mx-auto px-6 text-center">
             <h2 class="text-3xl md:text-4xl font-bold mb-4 text-text-dark">Cara Menggunakan Website Klinik Sehat</h2>
             <p class="text-text-grey max-w-2xl mx-auto">Panduan langkah-demi-langkah untuk pendaftaran, membuat janji temu, hingga mengakses rekam medis digital Anda.</p>
             <a href="#" class="mt-8 btn btn-primary">Lihat Panduan Lengkap</a>
        </div>
    </section>

    <!-- Articles Section -->
    <section id="artikel" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-12 text-text-dark">Artikel Kesehatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Article Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://plus.unsplash.com/premium_photo-1661769750558-4e1b5c47a505?q=80&w=2070&auto=format&fit=crop" alt="Pentingnya Pemeriksaan Rutin" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Pentingnya Pemeriksaan Rutin</h3>
                        <p class="text-sm text-text-grey mb-4">Deteksi dini penyakit melalui pemeriksaan kesehatan secara berkala.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
                <!-- Article Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=2120&auto=format&fit=crop" alt="Tips Menjaga Kesehatan Fisik dan Mental" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Menjaga Kesehatan Fisik & Mental</h3>
                        <p class="text-sm text-text-grey mb-4">Panduan keseimbangan kesehatan fisik dan mental sehari-hari.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
                <!-- Article Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1540420773420-2850a26b0f51?q=80&w=2070&auto=format&fit=crop" alt="Makanan Sehat" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Pentingnya Makanan Sehat</h3>
                        <p class="text-sm text-text-grey mb-4">Diet seimbang untuk menjaga fungsi tubuh dan kesehatan mental.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 text-center md:text-left">
            <div>
                <h4 class="text-lg font-semibold mb-4">Lokasi</h4>
                <p class="text-sm text-gray-400 leading-relaxed">Jl. Ir. H. Soekarno No.345, <br>Tangerang, Indonesia.</p>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Navigasi</h4>
                <ul class="list-none space-y-2 text-sm">
                    <li><a href="#beranda" class="text-gray-400 hover:text-white">Beranda</a></li>
                    <li><a href="#layanan" class="text-gray-400 hover:text-white">Layanan</a></li>
                    <li><a href="#tentang" class="text-gray-400 hover:text-white">Tentang</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Hubungi Kami</h4>
                 <ul class="list-none space-y-2 text-sm text-gray-400">
                     <li>kliniksehat@email.com</li>
                     <li>(021) 123-4567</li>
                 </ul>
            </div>
             <div>
                <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.08 6.2c-.78.35-1.62.58-2.5.68a4.4 4.4 0 001.9-2.4 8.8 8.8 0 01-2.76 1.05c-.73-.78-1.77-1.26-2.9-1.26-2.2 0-4 1.8-4 4 0 .3.04.6.1.9a11.3 11.3 0 01-8.2-4.16c-.3.5-.47 1.1-.47 1.75 0 1.4.7 2.63 1.78 3.35-.65-.02-1.27-.2-1.8-.5v.05c0 1.95 1.4 3.58 3.25 3.95-.34.1-.7.14-1.06.14-.26 0-.52-.03-.77-.07a4 4 0 003.73 2.78A8.8 8.8 0 012.6 18.1a12.5 12.5 0 006.75 2c8.1 0 12.54-6.7 12.54-12.54v-.56c.86-.62 1.6-1.4 2.2-2.28z"></path></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                         <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.08 2.525c.636-.247 1.363-.416 2.427.465C9.53 2.013 9.884 2 12.315 2zM12 7a5 5 0 100 10 5 5 0 000-10zm0-2a7 7 0 110 14 7 7 0 010-14zm6.406-2.186a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center mt-10 pt-5 border-t border-gray-700 text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} Klinik Sehat. All Rights Reserved.</p>
        </div>
    </div>
</footer>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const burgerMenu = document.getElementById('burger-menu');
        const mobileNav = document.getElementById('mobile-nav');
        const mainNavLinks = document.querySelectorAll('a[href^="#"]');

        burgerMenu.addEventListener('click', () => {
            mobileNav.classList.toggle('-right-full');
            mobileNav.classList.toggle('right-0');
            document.body.classList.toggle('overflow-hidden');
        });

        mainNavLinks.forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                if (mobileNav.classList.contains('right-0')) {
                    mobileNav.classList.add('-right-full');
                    mobileNav.classList.remove('right-0');
                    document.body.classList.remove('overflow-hidden');
                }

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        const dayButtons = document.querySelectorAll('.day-button');
        const schedulePanels = document.querySelectorAll('.schedule-panel');

        function activateDay(selectedDay) {
            dayButtons.forEach(button => {
                if (button.dataset.day === selectedDay) {
                    button.classList.add('bg-brand-primary', 'text-white');
                    button.classList.remove('text-gray-700');
                } else {
                    button.classList.remove('bg-brand-primary', 'text-white');
                    button.classList.add('text-gray-700');
                }
            });
            
            schedulePanels.forEach(panel => {
                if (panel.id === selectedDay) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });
        }

        dayButtons.forEach(button => {
            button.addEventListener('click', () => {
                activateDay(button.dataset.day);
            });
        });
        
        const dayIndex = new Date().getDay();
        const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        let initialDay = days[dayIndex];
        
        const scheduleData = @json($schedulesByDay);
        if (initialDay === 'minggu' || !scheduleData.hasOwnProperty(initialDay) || scheduleData[initialDay].length === 0) {
            initialDay = 'senin'; // Default to Monday if today is Sunday, or no schedule for today
        }
        
        activateDay(initialDay);
    });
</script>
@endpush
@endsection

