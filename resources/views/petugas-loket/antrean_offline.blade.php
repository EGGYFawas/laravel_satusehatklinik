@extends('layouts.petugas_loket_layout')

@section('title', 'Antrean Pasien Offline')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="space-y-8">

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md" role="alert"><p>{{ session('success') }}</p></div>
    @endif
    @if (session('error') || $errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul>
                @if(session('error'))
                    <li>{{ session('error') }}</li>
                @endif
                @foreach ($errors->all() as $error)
                    <li class="list-disc ml-4">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bagian Pendaftaran Pasien Offline --}}
    <div x-data="{ open: true, nikInput: '' }" class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300">
        <div @click="open = !open" class="p-4 bg-gray-50 border-b cursor-pointer flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Formulir Pendaftaran Pasien Walk-in
            </h2>
            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
        </div>
        <div x-show="open" x-collapse>
            <form id="antreanOfflineForm" action="{{ route('petugas-loket.antrean-offline.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 border-b border-gray-200 pb-6">
                    <h4 class="md:col-span-2 text-lg font-semibold text-gray-700">Data Pasien</h4>
                    <div>
                        <label for="new_patient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Pasien <span class="text-red-500">*</span></label>
                        <input type="text" name="new_patient_name" value="{{ old('new_patient_name') }}" class="w-full p-2 border border-gray-300 rounded-md" required @input="event.target.value = event.target.value.toUpperCase()">
                    </div>
                    <div>
                        <label for="new_patient_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" name="new_patient_nik" value="{{ old('new_patient_nik') }}" class="w-full p-2 border border-gray-300 rounded-md" required maxlength="16" x-model="nikInput" @input="nikInput = nikInput.replace(/\D/g, '')">
                        <p x-show="nikInput.length > 0 && nikInput.length !== 16" class="text-xs text-red-600 mt-1">NIK harus terdiri dari 16 digit angka.</p>
                    </div>
                    <div>
                        <label for="new_patient_dob" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="new_patient_dob" value="{{ old('new_patient_dob') }}" class="w-full p-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="new_patient_gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="new_patient_gender" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" @if(old('new_patient_gender') == 'Laki-laki') selected @endif>Laki-laki</option>
                            <option value="Perempuan" @if(old('new_patient_gender') == 'Perempuan') selected @endif>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                     <h4 class="md:col-span-2 text-lg font-semibold text-gray-700">Detail Pendaftaran</h4>
                    <div>
                        <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Pilih Poli <span class="text-red-500">*</span></label>
                        <select id="poli" name="poli_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                            <option value="" disabled selected>-- Silahkan Pilih Poli --</option>
                            @foreach($polis as $poli)
                                <option value="{{ $poli->id }}" @if(old('poli_id') == $poli->id) selected @endif>{{ $poli->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="doctor" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter <span class="text-red-500">*</span></label>
                        <select id="doctor" name="doctor_id" class="w-full p-2 border border-gray-300 rounded-md" required disabled>
                            <option value="">-- Pilih Poli Terlebih Dahulu --</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan <span class="text-red-500">*</span></label>
                        {{-- [PERBAIKAN] Menambahkan id="keluhan" agar cocok dengan label 'for' --}}
                        <textarea id="keluhan" name="chief_complaint" rows="3" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan keluhan utama pasien..." required>{{ old('chief_complaint') }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Daftarkan Antrean
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bagian Tampilan Antrean --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Antrean Berobat -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 space-y-4 border-t-4 border-blue-500">
            <h3 class="text-2xl font-bold text-gray-800 text-center">Antrean Berobat</h3>
            <div class="text-center">
                <p class="text-gray-500">Nomor Antrean Dipanggil</p>
                <p class="text-6xl font-extrabold text-blue-600">{{ $antreanBerobatBerjalan->queue_number ?? '-' }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500">Total Pasien Hari Ini</p>
                <p class="text-3xl font-bold text-gray-700">{{ $totalAntreanBerobat }} Pasien</p>
            </div>
            <hr>
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Daftar Pasien Aktif:</h4>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    @forelse ($daftarAntreanBerobat as $antrean)
                    @php
                        $bgColor = ''; $statusText = '';
                        switch($antrean->status) {
                            case 'MENUNGGU':
                                $bgColor = 'bg-yellow-50';
                                $statusText = 'Menunggu Check-in';
                                break;
                            case 'HADIR':
                                $bgColor = 'bg-green-50';
                                $statusText = 'Sudah Hadir';
                                break;
                        }
                    @endphp
                    <div class="flex items-center justify-between {{ $bgColor }} p-3 rounded-lg shadow-sm">
                        <div>
                            <p class="font-bold text-gray-800">{{ $antrean->queue_number }} - {{ $antrean->patient->user->full_name ?? $antrean->patient->full_name }}</p>
                            <p class="text-sm text-gray-500">Poli: {{ $antrean->poli->name }} | <span class="font-semibold">{{ $statusText }}</span></p>
                        </div>
                        
                        {{-- [MODIFIKASI] Logika Aksi Langsung Disatukan di Sini --}}
                        <div>
                            @if ($antrean->status == 'MENUNGGU')
                                <form action="{{ route('petugas-loket.antrean-offline.checkin', $antrean->id) }}" method="POST" class="checkin-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition checkin-btn">
                                        Check-in
                                    </button>
                                </form>
                            @elseif ($antrean->status == 'HADIR')
                                <div class="px-3 py-1 bg-green-200 text-green-800 text-sm font-bold rounded-full">
                                    Hadir
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-4">Tidak ada pasien dalam antrean aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Antrean Apotek -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 space-y-4 border-t-4 border-purple-500">
            <h3 class="text-2xl font-bold text-gray-800 text-center">Antrean Apotek</h3>
            <div class="text-center">
                <p class="text-gray-500">Nomor Resep Diproses</p>
                <p class="text-6xl font-extrabold text-purple-600">{{ $antreanApotekBerjalan->pharmacy_queue_number ?? '-' }}</p>
            </div>
             <div class="text-center">
                <p class="text-gray-500">Total Resep Hari Ini</p>
                <p class="text-3xl font-bold text-gray-700">{{ $daftarAntreanApotek->count() }} Resep</p>
            </div>
            <hr>
            <div>
                 <h4 class="font-semibold text-gray-700 mb-2">Daftar Antrean Resep:</h4>
                 <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    @forelse ($daftarAntreanApotek as $antrean)
                    @php
                        $statusTextApotek = ''; $textColorApotek = '';
                        switch ($antrean->status) {
                            case 'DALAM_ANTREAN': $statusTextApotek = 'Dalam Antrean'; $textColorApotek = 'text-cyan-800'; break;
                            case 'SEDANG_DIRACIK': $statusTextApotek = 'Obat Disiapkan'; $textColorApotek = 'text-orange-800'; break;
                            case 'SIAP_DIAMBIL': $statusTextApotek = 'Siap Diambil'; $textColorApotek = 'text-yellow-800 font-bold'; break;
                            case 'DISERAHKAN': $statusTextApotek = 'Siap Diserahkan'; $textColorApotek = 'text-purple-800 font-bold'; break;
                            default: $statusTextApotek = 'Selesai'; $textColorApotek = 'text-gray-500'; break;
                        }
                    @endphp
                    <div class="bg-gray-50 p-3 rounded-lg shadow-sm flex justify-between items-center">
                        <p class="font-bold text-gray-800">{{ $antrean->pharmacy_queue_number }} - {{ $antrean->patient_name }}</p>
                        <p class="text-sm font-semibold {{ $textColorApotek }}">{{ $statusTextApotek }}</p>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-4">Belum ada resep yang masuk antrean.</p>
                    @endforelse
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script tidak berubah, sudah benar --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const poliSelect = document.getElementById('poli');
    const doctorSelect = document.getElementById('doctor');
    const antreanForm = document.getElementById('antreanOfflineForm');
    const checkinForms = document.querySelectorAll('.checkin-form');

    poliSelect.addEventListener('change', function() {
        const poliId = this.value;
        doctorSelect.innerHTML = '<option value="">Memuat dokter...</option>';
        doctorSelect.disabled = true;

        if (poliId) {
            fetch(`{{ url('/petugas-loket/doctors-by-poli') }}/${poliId}`)
                .then(response => response.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="" disabled selected>-- Silahkan Pilih Dokter --</option>';
                    if (data.length > 0) {
                        data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = doctor.name;
                            doctorSelect.appendChild(option);
                        });
                        doctorSelect.disabled = false;
                    } else {
                        doctorSelect.innerHTML = '<option value="">-- Tidak ada dokter praktek --</option>';
                    }
                }).catch(error => {
                    console.error('Fetch Error:', error);
                    doctorSelect.innerHTML = '<option value="">-- Gagal memuat dokter --</option>';
                });
        }
    });

    if (antreanForm) {
        antreanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Pendaftaran',
                text: "Apakah data pasien dan pendaftaran sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, daftarkan!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    antreanForm.submit();
                }
            });
        });
    }

    if (checkinForms) {
        checkinForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Kehadiran',
                    text: "Anda akan melakukan check-in untuk pasien ini.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lakukan Check-in!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }
});
</script>
@endpush

