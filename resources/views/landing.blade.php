@extends('layouts.guest')

@section('title', 'Klinik Sehat - Landing Page')

@push('styles')
<style>
    /* Custom styles can be added here if needed */
    .bg-brand-background {
        background-color: #f8f9fa;
    }

</style>
@endpush

@section('content')
<!-- Header -->
<header class="bg-white py-4 sticky top-0 z-[1000] shadow-md transition-all duration-300 ease-in-out">
    <div class="max-w-7xl mx-auto px-6">
        <nav class="flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-brand-primary no-underline">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd"></path>
                </svg>
                Klinik Sehat
            </a>
            <ul class="hidden lg:flex gap-10 list-none">
                <li><a href="#beranda"
                        class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Beranda</a>
                </li>
                <li><a href="#layanan"
                        class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Layanan
                        Kami</a></li>
                <li><a href="#jadwal-dokter"
                        class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Dokter</a>
                </li>
                <li><a href="#tentang"
                        class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Tentang
                        Kami</a></li>
                <li><a href="#artikel"
                        class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Artikel
                        Kesehatan</a></li>
            </ul>

            @auth
            <div class="hidden lg:flex gap-4">
                <a href="{{ route('dashboard') }}"
                    class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text hover:opacity-90 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300 hover:bg-gray-50 hover:border-brand-primary">Keluar</button>
                </form>
            </div>
            @else
            <div class="hidden lg:flex gap-4">
                <a href="{{ route('login') }}"
                    class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text hover:opacity-90 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none">Masuk</a>
                <a href="{{ route('register') }}"
                    class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300 hover:bg-gray-50 hover:border-brand-primary">Daftar</a>
            </div>
            @endauth

            <div class="lg:hidden cursor-pointer z-[1010]" id="burger-menu">
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300 ease-in-out"></div>
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300 ease-in-out"></div>
                <div class="w-6 h-0.5 bg-brand-primary m-1.5 transition-all duration-300 ease-in-out"></div>
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Navigation -->
<div class="fixed top-0 h-screen w-[70%] bg-white shadow-lg z-[1005] transition-transform duration-400 ease-in-out flex flex-col items-center justify-center pt-20 -right-full"
    id="mobile-nav">
    <ul class="list-none w-full text-center">
        <li class="my-5"><a href="#beranda" class="nav-link-mobile">Beranda</a></li>
        <li class="my-5"><a href="#layanan" class="nav-link-mobile">Layanan Kami</a></li>
        <li class="my-5"><a href="#jadwal-dokter" class="nav-link-mobile">Dokter</a></li>
        <li class="my-5"><a href="#tentang" class="nav-link-mobile">Tentang Kami</a></li>
        <li class="my-5"><a href="#artikel" class="nav-link-mobile">Artikel Kesehatan</a></li>
    </ul>
    <div class="mt-10 flex flex-col gap-4 w-4/5">
        @auth
        <a href="{{ route('dashboard') }}"
            class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                class="w-full text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300">Keluar</button>
        </form>
        @else
        <a href="{{ route('login') }}"
            class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text">Masuk</a>
        <a href="{{ route('register') }}"
            class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300">Daftar</a>
        @endauth
    </div>
</div>

<main class="overflow-x-hidden bg-brand-background">
    <!-- Hero Section -->
    <section id="beranda" class="relative min-h-[90vh] bg-cover bg-center flex items-center text-white"
        style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-brand-primary/60"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-[1]">
            <div class="max-w-xl text-center md:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-[56px] leading-tight font-bold mb-4">Kami Hadir untuk Anda.</h1>
                <p class="text-base md:text-lg mb-8 max-w-md mx-auto md:mx-0">Kesehatan adalah fondasi utama untuk
                    mencapai kebahagiaan dan kualitas hidup yang lebih baik. Dengan perawatan yang tepat sejak dini,
                    Anda bisa menikmati hari-hari penuh energi dan produktivitas.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ route('register') }}"
                        class="inline-block text-center py-3 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border-2 border-white bg-white text-brand-primary hover:bg-transparent hover:text-white hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none text-lg">Buat
                        Antrean Berobat</a>
                    <a href="#panduan"
                        class="inline-block text-center py-3 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border-2 border-white bg-transparent text-white hover:bg-white hover:text-brand-primary hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none text-lg">Panduan
                        Penggunaan</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section id="layanan" class="py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-4 text-text-dark">Mengapa Memilih Kami ?</h2>
            <p class="text-center text-base md:text-lg text-text-grey max-w-3xl mx-auto mb-12">Kami merevolusi cara
                Anda berobat, lupakan antrean panjang dan ketidakpastian. Dengan sistem kami, seluruh proses dari
                pendaftaran hingga pengambilan obat kini ada dalam genggaman Anda, memberikan kenyamanan, kecepatan,
                dan kendali penuh atas kesehatan Anda.</p>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                <!-- Card 1 -->
                <div
                    class="p-8 rounded-2xl transition-all duration-300 ease-in-out flex flex-col text-left bg-white text-text-dark shadow-lg hover:-translate-y-2 hover:shadow-2xl border-l-4 border-brand-primary">
                    <div class="text-5xl font-bold text-brand-primary mb-4">100%</div>
                    <h3 class="text-xl font-semibold mb-3">Pendaftaran 100% Online & Real-time</h3>
                    <p class="text-text-grey text-sm leading-relaxed">Daftar dari mana saja, kapan saja. Pantau nomor
                        antrean Anda secara langsung dari ponsel, hilangkan waktu tunggu yang tidak perlu. Kami
                        memastikan proses pendaftaran yang mudah, cepat, dan transparan.</p>
                </div>
                <!-- Card 2 -->
                <div
                    class="p-8 rounded-2xl transition-all duration-300 ease-in-out flex flex-col text-left bg-white text-text-dark shadow-lg hover:-translate-y-2 hover:shadow-2xl border-l-4 border-yellow-500">
                    <div class="text-5xl font-bold text-yellow-500 mb-4">W</div>
                    <h3 class="text-xl font-semibold mb-3">Waktu Fleksibel & Terkendali Sepenuhnya</h3>
                    <p class="text-text-grey text-sm leading-relaxed">Pilih jadwal dokter dan waktu kunjungan yang
                        paling sesuai dengan kesibukan Anda. Sistem kami memberikan notifikasi pengingat dan estimasi
                        waktu panggilan, sehingga Anda bisa datang tepat waktu.</p>
                </div>
                <!-- Card 3 -->
                <div
                    class="p-8 rounded-2xl transition-all duration-300 ease-in-out flex flex-col text-left bg-white text-text-dark shadow-lg hover:-translate-y-2 hover:shadow-2xl border-l-4 border-green-500">
                    <div class="text-5xl font-bold text-green-500 mb-4">RMD</div>
                    <h3 class="text-xl font-semibold mb-3">Rekam Medis Digital & Terintegrasi</h3>
                    <p class="text-text-grey text-sm leading-relaxed">Akses riwayat kesehatan, diagnosis, dan resep
                        obat Anda aman dan terpusat. Dokter dapat melihat riwayat medis Anda dengan cepat untuk
                        memberikan diagnosis yang lebih akurat dan perawatan yang lebih efektif.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Schedule Section -->
    <section id="jadwal-dokter" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-12 text-text-dark">Jadwal Dokter Klinik Sehat
            </h2>
            <div class="flex justify-center mb-8">
                <div class="flex space-x-1 bg-gray-200 p-1 rounded-full">
                    <button class="px-6 py-2 text-sm font-medium text-gray-700 rounded-full">Senin</button>
                    <button class="px-6 py-2 text-sm font-medium text-white bg-brand-primary rounded-full">Selasa</button>
                    <button class="px-6 py-2 text-sm font-medium text-gray-700 rounded-full">Rabu</button>
                    <button class="px-6 py-2 text-sm font-medium text-gray-700 rounded-full">Kamis</button>
                    <button class="px-6 py-2 text-sm font-medium text-gray-700 rounded-full">Jumat</button>
                    <button class="px-6 py-2 text-sm font-medium text-gray-700 rounded-full">Sabtu</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Jadwal Card -->
                <div class="bg-brand-primary text-white p-6 rounded-lg shadow-lg">
                    <h3 class="font-bold text-lg mb-2">Poli Umum</h3>
                    <p class="text-sm mb-4">dr. Anisa Pujianti</p>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        08:00 - 12:00 WIB
                    </div>
                </div>
                <div class="bg-brand-primary text-white p-6 rounded-lg shadow-lg">
                    <h3 class="font-bold text-lg mb-2">Poli Ibu dan Anak</h3>
                    <p class="text-sm mb-4">dr. Budi Santoso</p>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        12:00 - 15:00 WIB
                    </div>
                </div>
                 <div class="bg-brand-primary text-white p-6 rounded-lg shadow-lg">
                    <h3 class="font-bold text-lg mb-2">Poli Gigi</h3>
                    <p class="text-sm mb-4">dr. Amelia Lestari</p>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        13:00 - 17:00 WIB
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-16 md:py-20 bg-brand-background">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
                <div class="flex-1">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop"
                        alt="Tentang Klinik Sehat" class="w-full rounded-2xl shadow-xl">
                </div>
                <div class="flex-1">
                    <h2 class="text-center lg:text-left text-3xl md:text-4xl font-bold mb-6 text-text-dark">Tentang
                        Kami</h2>
                    <p class="text-text-grey leading-loose text-justify">Klinik Sehat lahir dari sebuah gagasan untuk
                        memodernisasi dan menyederhanakan akses layanan kesehatan primer di Indonesia. Kami percaya
                        bahwa setiap orang berhak mendapatkan perawatan kesehatan berkualitas tanpa harus mengorbankan
                        waktu dan kenyamanan. Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik, di
                        mana setiap pasien dapat mengelola kesehatannya dengan kendali penuh di ujung jari mereka.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Guide Section -->
    <section id="panduan" class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-12 text-text-dark">Panduan Penggunaan Website</h2>
            <div class="text-center">
                 <h3 class="text-2xl font-semibold mb-4 text-text-dark">Cara Menggunakan Website Klinik Sehat</h3>
                 <p class="text-text-grey max-w-2xl mx-auto">Kami telah menyusun panduan langkah-demi-langkah yang mudah diikuti untuk memastikan Anda dapat memanfaatkan semua fitur website kami secara maksimal. Mulai dari pendaftaran pasien baru, membuat janji temu, hingga mengakses rekam medis digital Anda.</p>
                 <a href="#" class="mt-8 inline-block text-center py-3 px-8 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border-2 border-brand-primary bg-brand-primary text-white hover:bg-transparent hover:text-brand-primary">Lihat Panduan Lengkap</a>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section id="artikel" class="py-16 md:py-20 bg-brand-background">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-12 text-text-dark">Artikel Kesehatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Article Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://plus.unsplash.com/premium_photo-1661769750558-4e1b5c47a505?q=80&w=2070&auto=format&fit=crop" alt="Pentingnya Pemeriksaan Rutin" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Pentingnya Pemeriksaan Rutin</h3>
                        <p class="text-sm text-text-grey mb-4">Penjelasan mengenai manfaat melakukan pemeriksaan kesehatan secara berkala untuk deteksi dini penyakit.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
                <!-- Article Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=2120&auto=format&fit=crop" alt="Tips Menjaga Kesehatan Fisik dan Mental" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Tips Menjaga Kesehatan Fisik dan Mental</h3>
                        <p class="text-sm text-text-grey mb-4">Panduan untuk menjaga keseimbangan kesehatan fisik dan mental dalam kehidupan sehari-hari.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
                <!-- Article Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1540420773420-2850a26b0f51?q=80&w=2070&auto=format&fit=crop" alt="Makanan Sehat" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-text-dark">Makanan Sehat</h3>
                        <p class="text-sm text-text-grey mb-4">Pentingnya makanan dan diet yang seimbang untuk menjaga fungsi tubuh dan mendukung kesehatan mental.</p>
                        <a href="#" class="font-semibold text-brand-primary hover:underline text-sm">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-brand-primary text-brand-text pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 text-center md:text-left">
            <div>
                <h4 class="text-lg font-semibold mb-4">Lokasi</h4>
                <p class="text-sm opacity-80 leading-relaxed">Jl. Ir. H. Soekarno No.345, <br>Tangerang, Indonesia.
                </p>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Navigasi</h4>
                <ul class="list-none space-y-2 text-sm">
                    <li><a href="#beranda" class="opacity-80 hover:opacity-100 transition-opacity">Beranda</a></li>
                    <li><a href="#layanan" class="opacity-80 hover:opacity-100 transition-opacity">Layanan Kami</a></li>
                    <li><a href="#tentang" class="opacity-80 hover:opacity-100 transition-opacity">Tentang</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Hubungi Kami</h4>
                <ul class="list-none space-y-2 text-sm opacity-80">
                    <li>kliniksehat@email.com</li>
                    <li>(021) 123-4567</li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#" class="opacity-80 hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="opacity-80 hover:opacity-100 transition-opacity">
                         <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.08 2.525c.636-.247 1.363-.416 2.427-.465C9.53 2.013 9.884 2 12.315 2zM12 7a5 5 0 100 10 5 5 0 000-10zm0-2a7 7 0 110 14 7 7 0 010-14zm6.406-2.186a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center mt-10 pt-5 border-t border-white/20 text-xs opacity-70">
            <p>&copy; {{ date('Y') }} Klinik Sehat. All Rights Reserved.</p>
        </div>
    </div>
</footer>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const burgerMenu = document.getElementById('burger-menu');
        const mobileNav = document.getElementById('mobile-nav');
        const navLinks = document.querySelectorAll('.nav-link-mobile');
        const mainNavLinks = document.querySelectorAll('nav a[href^="#"], #mobile-nav a[href^="#"]');

        burgerMenu.addEventListener('click', () => {
            mobileNav.classList.toggle('-right-full');
            mobileNav.classList.toggle('right-0');
            document.body.classList.toggle('overflow-hidden');
        });

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.add('-right-full');
                mobileNav.classList.remove('right-0');
                document.body.classList.remove('overflow-hidden');
            });
        });

        // Smooth scrolling for all anchor links
        mainNavLinks.forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
