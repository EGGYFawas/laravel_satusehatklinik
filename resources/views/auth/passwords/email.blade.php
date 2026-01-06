@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl overflow-hidden p-8">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-text-dark">Reset Password</h2>
            <p class="text-sm text-text-grey mt-2">
                Masukkan email yang terdaftar. Kami akan mengirimkan link untuk mereset password Anda.
            </p>
        </div>

        {{-- Pesan Sukses jika link berhasil dikirim --}}
        @if (session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Pesan Error Validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-text-dark mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition"
                    placeholder="nama@email.com">
            </div>

            <button type="submit" 
                class="w-full bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                Kirim Link Reset Password
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-brand-primary hover:underline font-medium">
                    &larr; Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection