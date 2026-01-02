@extends('layouts.pasien_layout')

@section('title', 'Tagihan Pasien')

@section('content')
    <!-- Header Khusus Halaman Tagihan -->
    {{-- Menggunakan rounded dan margin bottom agar terlihat rapi di dalam layout --}}
    <div class="bg-teal-600 p-4 text-white shadow-md rounded-xl mb-6 sticky top-0 z-30">
        <div class="flex items-center">
            <!-- Tombol Back ke Dashboard -->
            <a href="{{ route('pasien.dashboard') }}" class="mr-4 text-white hover:text-teal-200 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-lg font-bold">Rincian Tagihan</h1>
        </div>
    </div>

    <div class="w-full max-w-lg mx-auto pb-20">
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @forelse($bills as $bill)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6 overflow-hidden">
                <!-- Header Card -->
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Tanggal Resep</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $bill->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        @if($bill->payment_status == 'paid')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                LUNAS
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                MENUNGGU
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Body Card (Rincian Obat) -->
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-2 font-semibold uppercase">Rincian Obat</p>
                    <ul class="space-y-2 mb-4">
                        @foreach($bill->details as $detail)
                            <li class="flex justify-between text-sm text-gray-700 border-b border-dashed border-gray-100 pb-1">
                                <span>{{ $detail->medicine->name }} <span class="text-xs text-gray-500">(x{{ $detail->quantity }})</span></span>
                                <span class="font-medium">Rp {{ number_format($detail->medicine->price * $detail->quantity) }}</span>
                            </li>
                        @endforeach
                        <li class="flex justify-between text-sm text-teal-700 font-semibold pt-1">
                            <span>Jasa Layanan</span>
                            <span>Rp 15,000</span>
                        </li>
                    </ul>

                    <div class="flex justify-between items-center border-t border-gray-100 pt-3 mb-4">
                        <span class="text-gray-600 font-bold">Total Tagihan</span>
                        <span class="text-xl font-extrabold text-teal-600">Rp {{ number_format($bill->total_price) }}</span>
                    </div>

                    <!-- ... Di bagian tombol aksi ... -->
<div class="mt-4 space-y-2">
    @if($bill->payment_status == 'pending')
                            <!-- Tombol Bayar -->
                            <a href="{{ route('pasien.billing.pay', $bill->id) }}" 
                            class="block w-full text-center bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition">
                                <i class="fas fa-qrcode mr-2"></i> Bayar Sekarang (QRIS)
                            </a>
                            
                            <!-- [BARU] Tombol Cek Status (Refresh) -->
                            <a href="{{ route('pasien.billing.check', $bill->id) }}" 
                            class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition">
                                <i class="fas fa-sync-alt mr-2"></i> Saya Sudah Bayar (Refresh Status)
                            </a>
                        @else
                            <!-- Tombol Download -->
                            <a href="{{ route('invoice.download', $bill->id) }}" 
                            class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded-lg shadow transition">
                                <i class="fas fa-file-pdf mr-2"></i> Download Struk PDF
                            </a>
                        @endif
                    </div>
                    <!-- ... -->
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 bg-white rounded-xl shadow-sm border border-dashed border-gray-200">
                <i class="fas fa-receipt text-5xl mb-3 text-gray-300"></i>
                <p>Belum ada tagihan obat.</p>
            </div>
        @endforelse
    </div>
@endsection