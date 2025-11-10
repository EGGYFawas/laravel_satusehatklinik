@extends('layouts.guest')

@section('title', 'Masuk ke Akun Anda')

@section('content')
{{-- Kontainer utama yang membungkus seluruh halaman --}}
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">

    {{-- Kartu login utama --}}
    <div class="flex flex-col md:flex-row w-full max-w-4xl bg-white shadow-2xl rounded-2xl overflow-hidden">

        <!-- Bagian Branding (Kiri) -->
        <div class="w-full md:w-[45%] bg-brand-primary/10 text-center p-8 flex flex-col justify-center items-center order-last md:order-first">
            {{-- Ganti src ini jika path gambar Anda berbeda --}}
            <img src="{{ asset('assets/img/logo_login.png') }}" alt="Ilustrasi Medis" class="max-w-[250px] mb-6">
            
            <p class="text-lg font-medium text-text-dark mb-6">“Berobat lebih mudah tanpa antri”</p>
            
            <div class="flex items-center gap-4 w-full justify-center">
                {{-- Tombol untuk membuat akun baru --}}
                <a href="{{ route('register') }}" 
                   class="w-1/2 bg-blue-400 text-black font-semibold py-3 px-6 rounded-full hover:bg-gray-500 transition-colors duration-300 text-center">
                   Buat Akun
                </a>
                {{-- Tombol untuk men-submit form login di sebelah kanan --}}
                <button type="submit" form="loginForm" 
                        class="w-1/2 bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                    Masuk
                </button>
            </div>
        </div>

        <!-- Bagian Form (Kanan) -->
        <div class="w-full md:w-[55%] p-8 flex flex-col justify-center">
            <h1 class="text-2xl font-bold text-text-dark mb-2">Selamat Datang Kembali</h1>
            <p class="text-sm text-text-grey mb-6">Masuk untuk melanjutkan ke akun Anda.</p>
            
            {{-- Menampilkan pesan sukses setelah registrasi --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4 text-sm" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Menampilkan error validasi dari Laravel --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4 text-sm" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-text-dark mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Masukkan email Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-text-dark mb-1">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Masukkan password Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                </div>

                <div class="flex items-center justify-between pt-2">
                     <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300 text-brand-primary focus:ring-brand-primary">
                        <label for="remember" class="text-sm text-text-grey">Ingat Saya</label>
                    </div>
                    <a href="#" class="text-sm text-brand-primary hover:underline">Lupa Password?</a>
                </div>

                 <div class="pt-2">
                    {{-- Tombol submit ini tersembunyi, karena kita menggunakan tombol di bagian branding --}}
                    {{-- Namun ini diperlukan agar form bisa di-submit dengan menekan Enter --}}
                    <button type="submit" class="w-full bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg md:hidden">
                        Masuk
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection

