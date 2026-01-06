@extends('layouts.guest')

@section('title', 'Verifikasi Email Anda')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">
    <div class="w-full max-w-lg bg-white shadow-xl rounded-2xl overflow-hidden p-8 text-center">
        
        {{-- Ikon Email --}}
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
            <svg class="h-8 w-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-text-dark mb-2">Verifikasi Email Diperlukan</h2>
        
        <p class="text-gray-600 mb-6">
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda.
        </p>
        
        <p class="text-sm text-gray-500 mb-6">
            Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya lagi.
        </p>

        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 text-sm text-left">
                Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
            </div>
        @endif

        <div class="flex flex-col gap-3">
            {{-- Tombol Kirim Ulang --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Logout / Keluar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection