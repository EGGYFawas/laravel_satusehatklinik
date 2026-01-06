@extends('layouts.petugas_loket_layout')

@section('title', 'Dashboard Apotek')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md" role="alert"><p>{{ session('success') }}</p></div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Antrean Apotek</h1>
        <p class="text-slate-600">Kelola antrean resep dan pembayaran obat pasien.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Kolom 1: Dalam Antrean -->
        <div class="bg-white/80 rounded-xl shadow-md flex flex-col">
            <h3 class="text-lg font-bold text-slate-800 border-b-2 border-slate-200 p-4 bg-slate-100 rounded-t-xl">
                Dalam Antrean ({{ $dalamAntrean->count() }})
            </h3>
            <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: 70vh;">
                @forelse ($dalamAntrean as $queue)
                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 border-slate-500">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-2xl text-gray-800">{{ $queue->pharmacy_queue_number }}</span>
                            <span class="text-xs font-semibold text-gray-500">{{ $queue->clinicQueue->poli->name }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-3">{{ $queue->clinicQueue->patient->full_name }}</p>
                        
                        <div class="text-xs text-gray-600 border-t pt-2">
                            <p class="font-semibold mb-1">Resep Obat:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($queue->prescription->prescriptionDetails as $detail)
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }})</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="mt-4 form-submit-confirm">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="SEDANG_DIRACIK">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md transition-colors">
                                Proses Resep
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Tidak ada antrean.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom 2: Sedang Disiapkan -->
        <div class="bg-white/80 rounded-xl shadow-md flex flex-col">
            <h3 class="text-lg font-bold text-yellow-800 border-b-2 border-yellow-200 p-4 bg-yellow-100 rounded-t-xl">
                Sedang Disiapkan ({{ $sedangDiracik->count() }})
            </h3>
            <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: 70vh;">
                @forelse ($sedangDiracik as $queue)
                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 border-yellow-500">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-2xl text-gray-800">{{ $queue->pharmacy_queue_number }}</span>
                            <span class="text-xs font-semibold text-gray-500">{{ $queue->clinicQueue->poli->name }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-3">{{ $queue->clinicQueue->patient->full_name }}</p>
                        
                        <div class="text-xs text-gray-600 border-t pt-2">
                            <p class="font-semibold mb-1">Resep Obat:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($queue->prescription->prescriptionDetails as $detail)
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }})</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="mt-4 form-submit-confirm">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="SIAP_DIAMBIL">
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md transition-colors">
                                Selesai & Siap Diambil
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Kosong.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom 3: Siap Diambil (MODIFIKASI UTAMA DI SINI) -->
        <div class="bg-white/80 rounded-xl shadow-md flex flex-col">
            <h3 class="text-lg font-bold text-green-800 border-b-2 border-green-200 p-4 bg-green-100 rounded-t-xl">
                Siap Diambil ({{ $siapDiambil->count() }})
            </h3>
            <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: 70vh;">
                @forelse ($siapDiambil as $queue)
                    @php
                        $isPaid = $queue->prescription->payment_status == 'paid';
                        $totalPrice = $queue->prescription->total_price;
                    @endphp

                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 {{ $isPaid ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-2xl text-gray-800">{{ $queue->pharmacy_queue_number }}</span>
                            @if($isPaid)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">LUNAS</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full animate-pulse">BELUM BAYAR</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">{{ $queue->clinicQueue->patient->full_name }}</p>
                        
                        <!-- Info Tagihan -->
                        <div class="bg-gray-50 p-2 rounded border border-gray-200 mb-3">
                            <p class="text-xs text-gray-500">Total Tagihan:</p>
                            <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</p>
                        </div>

                        <!-- LOGIKA TOMBOL -->
                        @if(!$isPaid)
                            {{-- MODIFIKASI: Tombol membuka Modal, bukan submit langsung --}}
                            <button onclick="openPaymentModal('{{ $queue->id }}', '{{ $queue->clinicQueue->patient->full_name }}', '{{ $totalPrice }}')" 
                                class="w-full mb-2 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-md text-sm transition-colors flex items-center justify-center">
                                <i class="fas fa-money-bill-wave mr-2"></i> Terima Tunai (Cash)
                            </button>
                            
                            <p class="text-xs text-center text-gray-400 mt-1">
                                Atau tunggu pasien bayar via QRIS (Otomatis Lunas)
                            </p>
                        @else
                            {{-- Jika Sudah Bayar: Tombol Struk & Serahkan --}}
                            <div class="flex gap-2 mb-2">
                                <a href="{{ route('invoice.download', $queue->prescription->id) }}" target="_blank" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded-md text-xs text-center flex items-center justify-center">
                                    <i class="fas fa-print mr-1"></i> Struk
                                </a>
                            </div>

                            <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="form-submit-confirm">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="DISERAHKAN">
                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                                    Serahkan Obat
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Tidak ada obat siap diambil.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom 4: Riwayat Hari Ini -->
        <div class="bg-white/80 rounded-xl shadow-md flex flex-col">
            <h3 class="text-lg font-bold text-purple-800 border-b-2 border-purple-200 p-4 bg-purple-100 rounded-t-xl">
                Riwayat Hari Ini ({{ $telahDiserahkan->count() }})
            </h3>
            <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: 70vh;">
                @forelse ($telahDiserahkan as $queue)
                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 {{ $queue->status == 'DITERIMA_PASIEN' ? 'border-purple-500' : 'border-gray-400' }}">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-2xl text-gray-800">{{ $queue->pharmacy_queue_number }}</span>
                            @if($queue->status == 'DITERIMA_PASIEN')
                                <span class="text-xs font-bold text-purple-600 bg-purple-200 px-2 py-1 rounded-full">Selesai</span>
                            @else
                                <span class="text-xs font-semibold text-gray-600 bg-gray-200 px-2 py-1 rounded-full">Diserahkan</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">{{ $queue->clinicQueue->patient->full_name }}</p>
                        <p class="text-xs text-green-600 font-bold mb-3">LUNAS - Rp {{ number_format($queue->prescription->total_price ?? 0, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- MODAL PEMBAYARAN TUNAI (HITUNG KEMBALIAN) --}}
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closePaymentModal()"></div>
        
        <!-- Modal Content -->
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md z-10 p-6 relative transform transition-all scale-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Kasir Pembayaran</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="mb-5 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <p class="text-sm text-gray-600">Pasien: <span id="modalPatientName" class="font-bold text-gray-800">...</span></p>
                <div class="flex justify-between items-end mt-2">
                    <span class="text-sm text-gray-600">Total Tagihan:</span>
                    <span class="text-2xl font-bold text-blue-700">Rp <span id="modalTotalDisplay">0</span></span>
                </div>
            </div>

            <form id="paymentForm" action="" method="POST">
                @csrf
                
                <!-- Input Uang Diterima -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Diterima (Cash)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                        <input type="number" id="inputAmountPaid" name="amount_paid" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                            placeholder="0" required min="0">
                    </div>
                </div>

                <!-- Display Kembalian -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kembalian</label>
                    <input type="text" id="displayChange" 
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-bold text-lg" 
                        value="Rp 0" readonly>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Batal</button>
                    <button type="submit" id="btnProcessPay" disabled
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Bayar & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // --- 1. SCRIPT LOGIKA MODAL PEMBAYARAN ---
    let currentTotal = 0;

    function openPaymentModal(id, name, total) {
        // Set Data ke Modal
        document.getElementById('modalPatientName').innerText = name;
        document.getElementById('modalTotalDisplay').innerText = new Intl.NumberFormat('id-ID').format(total);
        currentTotal = parseFloat(total);

        // Set Action Form
        let url = "{{ route('petugas-loket.bayar-tunai', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('paymentForm').action = url;

        // Reset Input
        document.getElementById('inputAmountPaid').value = '';
        document.getElementById('displayChange').value = 'Rp 0';
        document.getElementById('displayChange').classList.remove('text-red-600', 'text-green-600');
        document.getElementById('btnProcessPay').disabled = true;

        // Buka Modal
        document.getElementById('paymentModal').classList.remove('hidden');
        // Focus ke input
        setTimeout(() => document.getElementById('inputAmountPaid').focus(), 100);
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    // Event Listener Hitung Kembalian Otomatis
    document.getElementById('inputAmountPaid').addEventListener('input', function() {
        let paid = parseFloat(this.value);
        
        if (isNaN(paid)) {
            document.getElementById('displayChange').value = 'Rp 0';
            document.getElementById('btnProcessPay').disabled = true;
            return;
        }

        let change = paid - currentTotal;
        let formattedChange = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(change);
        
        const changeInput = document.getElementById('displayChange');
        const btnPay = document.getElementById('btnProcessPay');

        if (change < 0) {
            changeInput.value = 'Uang Kurang!';
            changeInput.classList.add('text-red-600');
            changeInput.classList.remove('text-green-600');
            btnPay.disabled = true;
        } else {
            changeInput.value = formattedChange;
            changeInput.classList.remove('text-red-600');
            changeInput.classList.add('text-green-600');
            btnPay.disabled = false;
        }
    });

    // --- 2. SCRIPT SWEETALERT (YANG SUDAH ADA) ---
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.form-submit-confirm');
        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                
                let confirmationText = 'Pastikan data sudah benar.';
                const buttonText = e.target.querySelector('button[type="submit"]').textContent.trim();
                
                if(buttonText.includes('Tunai')) {
                    confirmationText = 'Konfirmasi terima pembayaran Tunai dari pasien?';
                } else if(buttonText.includes('Serahkan')) {
                    confirmationText = 'Pastikan obat sudah sesuai dan diserahkan ke pasien yang benar.';
                } else {
                    confirmationText = `Lanjutkan proses "${buttonText}"?`;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmationText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush