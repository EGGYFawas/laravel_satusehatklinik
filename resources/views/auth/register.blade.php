@extends('layouts.guest')

@section('title', 'Buat Akun Baru')

@section('content')
{{-- Kontainer utama yang membungkus seluruh halaman --}}
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">

    {{-- Kartu registrasi utama --}}
    <div class="flex flex-col md:flex-row w-full max-w-4xl bg-white shadow-2xl rounded-2xl overflow-hidden">

        <!-- Bagian Branding (Kiri) -->
        <div class="w-full md:w-[45%] bg-brand-primary/10 text-center p-8 flex flex-col justify-center items-center order-last md:order-first">
            {{-- Ganti src ini jika path gambar Anda berbeda --}}
            <img src="{{ asset('assets/img/logo_login.png') }}" alt="Ilustrasi Medis" class="max-w-[250px] mb-6">
            
            <p class="text-lg font-medium text-text-dark mb-6">“Berobat lebih mudah tanpa antri”</p>
            
            <div class="flex items-center gap-4 w-full justify-center">
                {{-- Tombol Kembali --}}
                <a href="{{ route('login') }}" 
                   class="w-1/2 bg-gray-400 text-white font-semibold py-3 px-6 rounded-full hover:bg-gray-500 transition-colors duration-300 text-center">
                   Kembali
                </a>
                {{-- Tombol untuk men-submit form di sebelah kanan --}}
                <button type="submit" form="registerForm" 
                        class="w-1/2 bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                    Daftar Akun
                </button>
            </div>
        </div>

        <!-- Bagian Form (Kanan) -->
        <div class="w-full md:w-[55%] p-8 overflow-y-auto" style="max-height: 90vh;">
            <h1 class="text-2xl font-bold text-text-dark mb-2">Buat Akun Baru</h1>
            <p class="text-sm text-text-grey mb-6">Silakan isi data diri Anda dengan benar.</p>
            
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

            <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-3">
                @csrf
                
                <div>
                    <label for="full_name" class="block text-sm font-medium text-text-dark mb-1">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="Masukkan Nama Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition uppercase">
                </div>

                <div>
                    <label for="nik" class="block text-sm font-medium text-text-dark mb-1">NIK</label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required placeholder="Masukkan 16 Digit NIK"
                           pattern="\d{16}" title="NIK harus terdiri dari 16 angka" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-text-dark mb-1">Jenis Kelamin</label>
                        <select id="gender" name="gender" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition bg-white">
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-text-dark mb-1">Tanggal Lahir</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-text-dark mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="contoh: email@gmail.com"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-text-dark mb-1">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Buat Password Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    <small class="text-xs text-text-grey mt-1">Minimal 6 karakter.</small>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text-dark mb-1">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi Password Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    <small id="passwordMatchMessage" class="mt-1 text-xs"></small>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="terms" name="terms" required class="h-4 w-4 rounded border-gray-300 text-brand-primary focus:ring-brand-primary">
                    <label for="terms" class="text-sm text-text-grey">
                        Saya setuju dengan <a href="#" class="text-brand-primary hover:underline">Syarat dan Ketentuan</a>.
                    </label>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const messageElement = document.getElementById('passwordMatchMessage');

    function validatePassword() {
        messageElement.classList.remove('text-green-600', 'text-red-600');
        if (confirmPasswordInput.value === '') { messageElement.textContent = ''; return; }
        if (passwordInput.value === confirmPasswordInput.value) {
            messageElement.textContent = 'Password cocok!';
            messageElement.classList.add('text-green-600');
        } else {
            messageElement.textContent = 'Password tidak cocok.';
            messageElement.classList.add('text-red-600');
        }
    }
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
});
</script>
@endsection

