@extends('layouts.dokter_layout')

@section('title', 'Detail Riwayat Pasien - ' . $patient->full_name)

@push('styles')
<style>
    .visit-list-item { cursor: pointer; transition: background-color 0.2s ease-in-out; }
    .visit-list-item:hover, .visit-list-item.active { background-color: #ECFDF5; /* emerald-50 */ border-left-color: #10B981; /* emerald-500 */}
    .detail-section h4 { font-weight: 600; color: #4b5563; /* gray-600 */ margin-bottom: 0.5rem; font-size: 0.875rem; /* text-sm */ text-transform: uppercase; letter-spacing: 0.05em;}
    .detail-section p, .detail-section li { color: #1f2937; /* gray-800 */ margin-bottom: 0.5rem;}
    .detail-section ul { list-style: disc; margin-left: 1.5rem; }
    .prescription-detail { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.75rem; margin-bottom: 0.5rem;}
</style>
@endpush

@section('content')
<div x-data="{ selectedVisitId: {{ $riwayatKunjungan->first()?->id ?? 'null' }} }">

    {{-- Header Informasi Pasien --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-4 mb-4">
            <div>
                 <h2 class="text-2xl font-bold text-gray-800">{{ $patient->full_name }}</h2>
                 <p class="text-gray-500">NIK: {{ $patient->nik ?? '-' }} | Usia: {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age . ' thn' : '-' }}</p>
            </div>
             <a href="{{ route('dokter.riwayat-pasien.index') }}" class="mt-4 md:mt-0 inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-sm">&larr; Kembali ke Daftar/Pencarian</a>
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
            <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-gray-50">Riwayat Kunjungan</h3>
            <div class="max-h-[60vh] overflow-y-auto">
                @forelse ($riwayatKunjungan as $kunjungan)
                    <div
                        class="visit-list-item border-b border-l-4 border-transparent p-4"
                        :class="{ 'active': selectedVisitId == {{ $kunjungan->id }} }"
                        @click="selectedVisitId = {{ $kunjungan->id }}"
                    >
                        <p class="font-semibold text-gray-900">{{ $kunjungan->finish_time ? $kunjungan->finish_time->translatedFormat('l, d F Y') : 'Tanggal tidak tersedia' }}</p>
                        {{-- Menampilkan dokter yang menangani saat itu --}}
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
                            <h4>Keluhan Utama (Subjektif)</h4>
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
                                    @if($record->blood_pressure)<div><p><strong>Tekanan Darah:</strong> {{ $record->blood_pressure }} mmHg</p></div>@endif
                                    @if($record->heart_rate)<div><p><strong>Nadi:</strong> {{ $record->heart_rate }} x/mnt</p></div>@endif
                                    @if($record->temperature)<div><p><strong>Suhu:</strong> {{ $record->temperature }} °C</p></div>@endif
                                    @if($record->respiratory_rate)<div><p><strong>Pernapasan:</strong> {{ $record->respiratory_rate }} x/mnt</p></div>@endif
                                    @if($record->oxygen_saturation)<div><p><strong>Saturasi O2:</strong> {{ $record->oxygen_saturation }} %</p></div>@endif
                                 </div>
                            </div>
                            @endif

                            {{-- Temuan Fisik --}}
                             @if ($record->physical_examination_notes)
                             <div class="detail-section border-t pt-4">
                                 <h4>Temuan Pemeriksaan Fisik (Objektif)</h4>
                                 <p class="whitespace-pre-wrap">{{ $record->physical_examination_notes }}</p>
                             </div>
                             @endif

                             {{-- Diagnosis --}}
                             @if ($record->diagnosisTags->isNotEmpty())
                             <div class="detail-section border-t pt-4">
                                 <h4>Diagnosis (Asesmen)</h4>
                                 <ul>
                                     @foreach ($record->diagnosisTags as $tag)
                                         <li>{{ $tag->tag_name }}</li>
                                     @endforeach
                                 </ul>
                             </div>
                             @endif
                             
                             {{-- Rencana Penatalaksanaan --}}
                             @if ($record->doctor_notes)
                             <div class="detail-section border-t pt-4">
                                 <h4>Rencana Penatalaksanaan (Plan)</h4>
                                 <p class="whitespace-pre-wrap">{{ $record->doctor_notes }}</p>
                             </div>
                             @endif

                             {{-- [PENAMBAHAN BARU] Detail Resep Obat --}}
                             @if ($record->prescription && $record->prescription->prescriptionDetails->isNotEmpty())
                             <div class="detail-section border-t pt-4">
                                 <h4>Resep Obat</h4>
                                 <div class="space-y-2">
                                     @foreach ($record->prescription->prescriptionDetails as $detail)
                                         <div class="prescription-detail text-sm">
                                             <p class="font-semibold">{{ $detail->medicine->name ?? 'Obat tidak ditemukan' }} (Jumlah: {{ $detail->quantity }})</p>
                                             <p class="text-gray-600">Dosis: {{ $detail->dosage }}</p>
                                         </div>
                                     @endforeach
                                 </div>
                             </div>
                             @else
                              <div class="detail-section border-t pt-4">
                                 <h4>Resep Obat</h4>
                                 <p class="text-gray-500 italic">Tidak ada resep obat pada kunjungan ini.</p>
                             </div>
                             @endif

                        @else
                            {{-- Tampilkan pesan jika tidak ada rekam medis --}}
                            <div class="border-t pt-6 text-center text-gray-500">
                                <p>Detail pemeriksaan untuk kunjungan ini tidak ditemukan.</p>
                            </div>
                        @endif

                    </div>
                @endforeach
            @else
                 {{-- Tampilan jika $riwayatKunjungan kosong sama sekali --}}
                <div x-show="selectedVisitId == null" class="text-center text-gray-500 py-12">
                     <p>Pilih tanggal kunjungan di sebelah kiri untuk melihat detail.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tidak perlu script tambahan khusus untuk halaman ini, karena AlpineJS sudah menangani show/hide --}}
@endpush
