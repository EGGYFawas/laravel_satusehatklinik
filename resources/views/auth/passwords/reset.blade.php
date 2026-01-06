@extends('layouts.guest')

@section('title', 'Buat Password Baru')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl overflow-hidden p-8">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-text-dark">Password Baru</h2>
            <p class="text-sm text-text-grey mt-2">
                Silakan buat password baru untuk akun Anda.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Utama dengan autocomplete="off" --}}
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" autocomplete="off">
            @csrf

            {{-- Token Reset (Wajib disembunyikan) --}}
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-text-dark mb-1">Alamat Email</label>
                {{-- 
                    MODIFIKASI FINAL:
                    Menambahkan `request()->email` pada value. 
                    Ini akan mengambil email dari URL link reset secara paksa 
                    meskipun controller lupa mengirimnya.
                --}}
                <input type="email" id="email" name="email" 
                    value="{{ $email ?? old('email') ?? request()->email }}" 
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition bg-gray-100 text-gray-500 cursor-not-allowed"
                    placeholder="nama@email.com" 
                    readonly>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text-dark mb-1">Password Baru</label>
                {{-- autocomplete="new-password" mencegah browser mengisi password lama --}}
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition"
                    placeholder="Min. 6 karakter (Huruf & Angka)"
                    autocomplete="new-password">
                
                {{-- Text bantuan --}}
                <p class="text-xs text-text-grey mt-1">
                    Minimal 6 karakter, harus kombinasi huruf dan angka.
                </p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text-dark mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password">
                
                {{-- Pesan Validasi JS --}}
                <small id="passwordMatchMessage" class="mt-1 text-xs block min-h-[1rem]"></small>
            </div>

            <button type="submit" 
                class="w-full bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                Reset Password
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const messageElement = document.getElementById('passwordMatchMessage');
    
        function validatePassword() {
            // Hapus class warna sebelumnya
            messageElement.classList.remove('text-green-600', 'text-red-600');
            
            // Jika konfirmasi password kosong, bersihkan pesan
            if (confirmPasswordInput.value === '') { 
                messageElement.textContent = ''; 
                return; 
            }
            
            // Cek kesamaan
            if (passwordInput.value === confirmPasswordInput.value) {
                messageElement.textContent = 'Password cocok!';
                messageElement.classList.add('text-green-600');
            } else {
                messageElement.textContent = 'Password tidak cocok.';
                messageElement.classList.add('text-red-600');
            }
        }
    
        // Pasang event listener pada kedua input
        passwordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
    });
</script>
@endsection