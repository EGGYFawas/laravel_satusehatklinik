@extends('layouts.guest')

@section('title', 'Klinik Sehat - Landing Page')

@section('content')
    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-[1000] shadow-md transition-all duration-300 ease-in-out">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex justify-between items-center">
                <a href="/" class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-brand-primary no-underline">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                    Klinik Sehat
                </a>
                <ul class="hidden lg:flex gap-10 list-none">
                    <li><a href="#beranda" class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Beranda</a></li>
                    <li><a href="#layanan" class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Layanan Kami</a></li>
                    <li><a href="#dokter" class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Dokter</a></li>
                    <li><a href="#tentang" class="text-text-grey font-medium no-underline relative transition-colors duration-300 ease-in-out hover:text-brand-primary after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-[-5px] after:left-1/2 after:bg-brand-primary after:transition-all after:duration-300 after:ease-in-out after:-translate-x-1/2 hover:after:w-full">Tentang Kami</a></li>
                </ul>
                <div class="hidden lg:flex gap-4">
                    <a href="{{ route('login') }}" class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text hover:opacity-90 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300 hover:bg-gray-50 hover:border-brand-primary">Daftar</a>
                </div>
                {{-- Tampilkan ini jika pengguna SUDAH LOGIN --}}
                @auth
                    <div class="flex items-center gap-x-4">
                        <a href="{{ route('dashboard') }}" class="py-2 px-6 bg-brand-primary text-brand-text ...">
                            Dashboard 
                        </a>
                        
                        {{-- Form untuk logout --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="py-2 px-6 border border-gray-300 ...">
                                Keluar
                            </button>
                        </form>
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
    <div class="fixed top-0 h-screen w-[70%] bg-white shadow-lg z-[1005] transition-transform duration-400 ease-in-out flex flex-col items-center justify-center pt-20 -right-full" id="mobile-nav">
        <ul class="list-none w-full text-center">
            <li class="my-5"><a href="#beranda" class="nav-link-mobile">Beranda</a></li>
            <li class="my-5"><a href="#layanan" class="nav-link-mobile">Layanan Kami</a></li>
            <li class="my-5"><a href="#dokter" class="nav-link-mobile">Dokter</a></li>
            <li class="my-5"><a href="#tentang" class="nav-link-mobile">Tentang Kami</a></li>
        </ul>
        <div class="mt-10 flex flex-col gap-4 w-4/5">
            <a href="{{ route('login') }}" class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-brand-primary text-brand-text">Masuk</a>
            <a href="{{ route('register') }}" class="inline-block text-center py-2.5 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border bg-white text-brand-primary border-gray-300">Daftar</a>
        </div>
    </div>

    <main class="overflow-x-hidden">
        <!-- Hero Section -->
        <section id="beranda" class="relative min-h-[90vh] bg-cover bg-center flex items-center text-white" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-brand-primary/60"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-[1]">
                <div class="max-w-xl text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-[56px] leading-tight font-bold mb-4">Kami Hadir untuk Anda.</h1>
                    <p class="text-base md:text-lg mb-8 max-w-md mx-auto md:mx-0">Hidup sehat adalah fondasi utama untuk mencapai kebahagiaan dan kualitas hidup yang lebih baik.</p>
                    <a href="{{ route('register') }}" class="inline-block text-center py-3 px-6 rounded-lg no-underline font-semibold transition-all duration-300 ease-in-out border border-transparent bg-white text-brand-primary hover:bg-gray-200 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-none text-lg">Buat Antrean Berobat</a>
                </div>
            </div>
        </section>

        {{-- Sections lainnya akan menggunakan background utama dari body --}}
        <section id="layanan" class="py-16 md:py-20">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-center text-3xl md:text-4xl font-bold mb-4 text-text-dark">Mengapa Memilih Kami ?</h2>
                <p class="text-center text-base md:text-lg text-text-grey max-w-2xl mx-auto mb-12">Kami merevolusi cara Anda berobat. Lupakan antrean panjang dan ketidakpastian.</p>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                    <div class="p-8 md:p-10 rounded-2xl transition-all duration-300 ease-in-out flex flex-col items-center text-center bg-brand-primary text-brand-text shadow-lg hover:-translate-y-2 hover:shadow-2xl">
                        <h3 class="text-xl font-semibold mb-3">Pendaftaran 100% Online</h3>
                        <p class="opacity-80 text-sm leading-relaxed">Daftar dan pantau antrean secara real-time dari ponsel Anda.</p>
                    </div>
                    <div class="p-8 md:p-10 rounded-2xl transition-all duration-300 ease-in-out flex flex-col items-center text-center bg-white text-text-dark shadow-lg hover:-translate-y-2 hover:shadow-2xl">
                        <h3 class="text-xl font-semibold mb-3">Waktu Fleksibel</h3>
                        <p class="text-text-grey text-sm leading-relaxed">Pilih jadwal dokter yang paling sesuai dengan kesibukan Anda.</p>
                    </div>
                    <div class="p-8 md:p-10 rounded-2xl transition-all duration-300 ease-in-out flex flex-col items-center text-center bg-brand-primary text-brand-text shadow-lg hover:-translate-y-2 hover:shadow-2xl">
                        <h3 class="text-xl font-semibold mb-3">Rekam Medis Digital</h3>
                        <p class="opacity-80 text-sm leading-relaxed">Akses riwayat kesehatan Anda kapan pun dan di mana pun.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- About Section -->
        <section id="tentang" class="py-16 md:py-20 bg-white">
             <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
                    <div class="flex-1">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop" alt="Tentang Klinik Sehat" class="w-full rounded-2xl shadow-xl">
                    </div>
                    <div class="flex-1">
                        <h2 class="text-center lg:text-left text-3xl md:text-4xl font-bold mb-6 text-text-dark">Tentang Klinik Sehat</h2>
                        <p class="text-text-grey leading-loose">Visi kami adalah menjadi pionir dalam digitalisasi layanan klinik di Indonesia, di mana setiap pasien dapat mengelola kesehatannya dengan kendali penuh di ujung jari mereka.</p>
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
                    <h4 class="text-lg font-semibold mb-4">Klinik Sehat</h4>
                    <p class="text-sm opacity-80 leading-relaxed">Jl. Kesehatan No. 123, Jakarta, Indonesia.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Navigasi</h4>
                    <ul class="list-none space-y-2 text-sm">
                        <li><a href="#beranda" class="opacity-80 hover:opacity-100 transition-opacity">Beranda</a></li>
                        <li><a href="#layanan" class="opacity-80 hover:opacity-100 transition-opacity">Layanan</a></li>
                        <li><a href="#tentang" class="opacity-80 hover:opacity-100 transition-opacity">Tentang</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Hubungi Kami</h4>
                     <ul class="list-none space-y-2 text-sm opacity-80">
                         <li>kontak@kliniksehat.com</li>
                         <li>(021) 123-4567</li>
                     </ul>
                </div>
                 <div>
                    <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                    <ul class="list-none space-y-2 text-sm">
                        <li><a href="#" class="opacity-80 hover:opacity-100 transition-opacity">Facebook</a></li>
                        <li><a href="#" class="opacity-80 hover:opacity-100 transition-opacity">Instagram</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-10 pt-5 border-t border-white/20 text-xs opacity-70">
                <p>&copy; {{ date('Y') }} Klinik Sehat. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        const burgerMenu = document.getElementById('burger-menu');
        const mobileNav = document.getElementById('mobile-nav');
        const navLinks = document.querySelectorAll('.nav-link-mobile');

        burgerMenu.addEventListener('click', () => {
            mobileNav.classList.toggle('-right-full');
            mobileNav.classList.toggle('right-0');
        });

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.add('-right-full');
                mobileNav.classList.remove('right-0');
            });
        });
    </script>
@endsection

