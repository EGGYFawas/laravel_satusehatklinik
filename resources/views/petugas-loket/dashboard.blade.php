@extends('layouts.petugas_loket_layout')

@section('title', 'Dashboard Apotek')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
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
        <p class="text-slate-600">Kelola antrean resep obat pasien secara real-time.</p>
    </div>

    {{-- [MODIFIKASI UTAMA] Kanban Board diubah menjadi 4 kolom --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Kolom 1: Dalam Antrean (Menggantikan 'Menunggu Racik') -->
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
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }}) - {{ $detail->dosage }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Aksi untuk mengubah status ke SEDANG_DIRACIK --}}
                        <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="mt-4 form-submit-confirm">
                            @csrf
                            @method('PATCH')
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

        <!-- Kolom 2: Sedang Disiapkan (Menggantikan 'Sedang Diracik') -->
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
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }}) - {{ $detail->dosage }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Aksi untuk mengubah status ke SIAP_DIAMBIL --}}
                        <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="mt-4 form-submit-confirm">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="SIAP_DIAMBIL">
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md transition-colors">
                                Selesai & Siap Diambil
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Tidak ada resep yang sedang disiapkan.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom 3: Siap Diambil -->
        <div class="bg-white/80 rounded-xl shadow-md flex flex-col">
            <h3 class="text-lg font-bold text-green-800 border-b-2 border-green-200 p-4 bg-green-100 rounded-t-xl">
                Siap Diambil ({{ $siapDiambil->count() }})
            </h3>
            <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: 70vh;">
                @forelse ($siapDiambil as $queue)
                    <div class="bg-white rounded-lg shadow-lg p-4 border-l-4 border-green-500">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-2xl text-gray-800">{{ $queue->pharmacy_queue_number }}</span>
                            <span class="text-xs font-semibold text-gray-500">{{ $queue->clinicQueue->poli->name }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-3">{{ $queue->clinicQueue->patient->full_name }}</p>
                        
                        <div class="text-xs text-gray-600 border-t pt-2">
                            <p class="font-semibold mb-1">Resep Obat:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($queue->prescription->prescriptionDetails as $detail)
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }}) - {{ $detail->dosage }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Aksi untuk mengubah status ke DISERAHKAN --}}
                        <form action="{{ route('petugas-loket.antrean-apotek.updateStatus', $queue->id) }}" method="POST" class="mt-4 form-submit-confirm">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="DISERAHKAN">
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md transition-colors">
                                Serahkan ke Pasien
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Tidak ada obat yang siap diambil.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom 4: Riwayat Hari Ini (Kolom Baru) -->
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
                        <p class="text-sm font-semibold text-gray-700 mb-3">{{ $queue->clinicQueue->patient->full_name }}</p>
                        
                        <div class="text-xs text-gray-600 border-t pt-2">
                             <p class="font-semibold mb-1">Resep Obat:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($queue->prescription->prescriptionDetails as $detail)
                                    <li>{{ $detail->medicine->name }} ({{ $detail->quantity }})</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-10">Belum ada riwayat hari ini.</p>
                @endforelse
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.form-submit-confirm');
        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                
                let confirmationText = 'Apakah Anda yakin?';
                const buttonText = e.target.querySelector('button[type="submit"]').textContent.trim();
                confirmationText = `Anda yakin ingin melanjutkan proses "${buttonText}"?`;

                Swal.fire({
                    title: 'Konfirmasi Tindakan',
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
