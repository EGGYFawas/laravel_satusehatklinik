@extends('layouts.pasien_layout')

@section('title', 'Detail Riwayat Kunjungan - ' . $patient->full_name)

@push('styles')
<style>
    .visit-list-item { cursor: pointer; transition: background-color 0.2s ease-in-out; }
    .visit-list-item:hover, .visit-list-item.active { background-color: #e0f2fe; /* light blue */ border-left: 4px solid #0ea5e9; }
    .detail-section h4 { font-weight: 600; color: #4b5563; /* gray-600 */ margin-bottom: 0.5rem; font-size: 0.875rem; /* text-sm */ text-transform: uppercase; letter-spacing: 0.05em;}
    .detail-section p, .detail-section li { color: #1f2937; /* gray-800 */ margin-bottom: 0.5rem;}
    .detail-section ul { list-style: disc; margin-left: 1.5rem; }
    .prescription-detail { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.75rem; margin-bottom: 0.5rem;}
</style>
@endpush

@section('content')
{{-- Gunakan ID kunjungan terbaru sebagai default, atau null jika tidak ada riwayat --}}
<div x-data="{ selectedVisitId: {{ $riwayatKunjungan->first()?->id ?? 'null' }} }">

    {{-- Header Informasi Pasien --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-4 mb-4">
            <div>
                 <h2 class="text-2xl font-bold text-gray-800">{{ $patient->full_name }}</h2>
                 <p class="text-gray-500">NIK: {{ $patient->nik ?? '-' }}</p>
            </div>
             <a href="{{ route('pasien.riwayat.index') }}" class="mt-4 md:mt-0 inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-sm">&larr; Kembali ke Daftar</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="font-semibold text-gray-600">Golongan Darah</p>
                <p class="text-gray-800">{{ $patient->blood_type ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Alergi Diketahui</p>
                <p class="text-gray-800">{{ $patient->known_allergies ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Penyakit Kronis</p>
                <p class="text-gray-800">{{ $patient->chronic_diseases ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Layout Dua Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Daftar Riwayat --}}
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg overflow-hidden">
            <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-gray-50">Daftar Kunjungan Selesai</h3>
            <div class="max-h-[60vh] overflow-y-auto">
                @forelse ($riwayatKunjungan as $kunjungan)
                    <div
                        class="visit-list-item border-b p-4 border-l-4 border-transparent"
                        :class="{ 'active': selectedVisitId == {{ $kunjungan->id }} }"
                        @click="selectedVisitId = {{ $kunjungan->id }}"
                    >
                        {{-- Pastikan finish_time tidak null sebelum diformat --}}
                        <p class="font-semibold text-gray-900">{{ $kunjungan->finish_time ? $kunjungan->finish_time->translatedFormat('l, d F Y') : 'Tanggal tidak tersedia' }}</p>
                        <p class="text-sm text-gray-600">Dokter: {{ $kunjungan->doctor?->user?->full_name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">Poli: {{ $kunjungan->poli?->name ?? 'N/A' }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 p-6">Belum ada riwayat kunjungan yang selesai.</p>
                @endforelse
            </div>
        </div>

        {{-- Kolom Kanan: Detail Rekam Medis --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 md:p-8">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-6">Detail Pemeriksaan</h3>

            @if($riwayatKunjungan->isNotEmpty())
                @foreach ($riwayatKunjungan as $kunjungan)
                    {{-- Tampilkan detail HANYA jika ID kunjungan cocok dengan selectedVisitId --}}
                    <div x-show="selectedVisitId == {{ $kunjungan->id }}" x-transition.opacity.duration.300ms class="space-y-5">

                        {{-- Informasi Dasar Kunjungan --}}
                        <div class="detail-section">
                             <h4>Informasi Kunjungan</h4>
                             <p><strong>Tanggal:</strong> {{ $kunjungan->finish_time ? $kunjungan->finish_time->translatedFormat('l, d F Y H:i') : '-' }} WIB</p>
                             <p><strong>Poli:</strong> {{ $kunjungan->poli?->name ?? 'N/A' }}</p>
                             <p><strong>Dokter Pemeriksa:</strong> {{ $kunjungan->doctor?->user?->full_name ?? 'N/A' }}</p>
                        </div>

                        {{-- Keluhan Utama --}}
                        <div class="detail-section border-t pt-4">
                            <h4>Keluhan Utama</h4>
                            <p>{{ $kunjungan->chief_complaint ?? '-' }}</p>
                        </div>

                        {{-- Cek apakah ada data rekam medis terkait --}}
                        @if ($kunjungan->medicalRecord)
                            @php $record = $kunjungan->medicalRecord; @endphp

                            {{-- Tanda Vital --}}
                            @if ($record->blood_pressure || $record->heart_rate || $record->temperature || $record->respiratory_rate || $record->oxygen_saturation)
                            <div class="detail-section border-t pt-4">
                                <h4>Tanda Vital</h4>
                                 <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-2 text-sm">
                                    @if($record->blood_pressure)
                                    <div><p><strong>Tekanan Darah:</strong> {{ $record->blood_pressure }} mmHg</p></div>
                                    @endif
                                    @if($record->heart_rate)
                                    <div><p><strong>Nadi:</strong> {{ $record->heart_rate }} x/mnt</p></div>
                                    @endif
                                    @if($record->temperature)
                                    <div><p><strong>Suhu:</strong> {{ $record->temperature }} °C</p></div>
                                    @endif
                                    @if($record->respiratory_rate)
                                    <div><p><strong>Pernapasan:</strong> {{ $record->respiratory_rate }} x/mnt</p></div>
                                    @endif
                                    @if($record->oxygen_saturation)
                                    <div><p><strong>Saturasi O2:</strong> {{ $record->oxygen_saturation }} %</p></div>
                                    @endif
                                 </div>
                            </div>
                            @endif

                            {{-- Diagnosis untuk Pasien (HANYA TAGS) --}}
                            <div class="detail-section border-t pt-4">
                                <h4>Diagnosis / Catatan Dokter</h4>
                                {{-- Kita hanya menampilkan Tags, ICD-10 di-skip --}}
                                @if ($record->diagnosisTags->isNotEmpty())
                                    <ul class="list-disc pl-5">
                                        @foreach ($record->diagnosisTags as $tag)
                                            <li class="font-medium text-gray-800">{{ $tag->tag_name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 italic">-</p>
                                @endif
                            </div>
                            
                            {{-- Rencana Penatalaksanaan --}}
                            @if ($record->doctor_notes)
                            <div class="detail-section border-t pt-4">
                                <h4>Catatan / Anjuran Dokter</h4>
                                <p class="whitespace-pre-wrap">{{ $record->doctor_notes }}</p>
                            </div>
                            @endif

                            {{-- Detail Resep Obat --}}
                            @if ($record->prescription && $record->prescription->prescriptionDetails->isNotEmpty())
                            <div class="detail-section border-t pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4>Resep Obat & Tagihan</h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($record->prescription->prescriptionDetails as $detail)
                                        <div class="prescription-detail text-sm">
                                            <p class="font-semibold">{{ $detail->medicine->name ?? 'Obat' }} (Jumlah: {{ $detail->quantity }})</p>
                                            <p class="text-gray-600">Aturan Pakai: {{ $detail->dosage }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- [NEW] Button Download Invoice --}}
                                @if($record->prescription->payment_status == 'paid')
                                    <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                                        <a href="{{ route('invoice.download', $record->prescription->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                                            <i class="fas fa-file-invoice-dollar mr-2"></i> Download Struk / Invoice
                                        </a>
                                        <p class="text-xs text-green-600 mt-2 flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i> Pembayaran Lunas
                                        </p>
                                    </div>
                                @elseif($record->prescription->payment_status == 'failed' || $record->prescription->payment_status == 'pending')
                                    <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                                         <a href="{{ route('pasien.billing.index') }}" 
                                            class="inline-flex items-center text-teal-600 hover:text-teal-800 text-sm font-semibold">
                                            Lihat Tagihan Pembayaran &rarr;
                                         </a>
                                    </div>
                                @endif
                            </div>
                            @endif

                        @else
                            {{-- Tampilkan pesan jika tidak ada rekam medis --}}
                            <div class="border-t pt-6 text-center text-gray-500">
                                <p>Detail pemeriksaan belum tersedia.</p>
                            </div>
                        @endif

                    </div>
                @endforeach
            @else
                 {{-- Tampilan jika $riwayatKunjungan kosong sama sekali --}}
                <div x-show="selectedVisitId == null" class="text-center text-gray-500 py-12">
                     <p>Pilih riwayat kunjungan di sebelah kiri untuk melihat detail.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Scripts --}}
@endpush