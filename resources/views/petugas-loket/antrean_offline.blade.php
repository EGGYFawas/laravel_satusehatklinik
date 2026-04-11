@extends('layouts.petugas_loket_layout')

@section('title', 'Antrean Pasien Offline')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .nik-status-indicator {
            position: absolute;
            right: 0.75rem; 
            top: 50%;
            transform: translateY(-50%);
            display: none; 
        }
        .nik-status-indicator.loading {
            display: inline-block;
            border: 3px solid #f3f3f3; 
            border-top: 3px solid #3498db; 
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        .nik-status-indicator.success {
            display: inline-block;
            color: #10B981; 
        }
         .nik-status-indicator.error {
            display: inline-block;
            color: #EF4444; 
        }
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
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
    <div x-data="{ open: true }" class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300">
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
                    
                    <div class="relative">
                        <label for="new_patient_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" id="new_patient_nik" name="new_patient_nik" value="{{ old('new_patient_nik') }}" class="w-full p-2 border border-gray-300 rounded-md" required maxlength="16" x-model="nikInput" @input="nikInput = nikInput.replace(/\D/g, '')">
                        <p x-show="nikInput.length > 0 && nikInput.length !== 16" class="text-xs text-red-600 mt-1">NIK harus terdiri dari 16 digit angka.</p>
                        
                        <div id="nik_status_indicator" class="nik-status-indicator"></div>
                        <p id="nik_message" class="text-xs text-blue-600 mt-1 hidden"></p>

                        <!-- [BARU] TOMBOL CEK BPJS & WADAH KARTU -->
                        <div class="mt-3">
                            <button type="button" id="btn_cek_bpjs" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors shadow-sm border border-green-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Cek Kepesertaan BPJS
                            </button>
                            
                            <!-- Kartu Info BPJS (Awalnya disembunyikan) -->
                            <div id="bpjs_info_card" class="hidden mt-3 bg-white p-4 rounded-lg border shadow-sm relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1.5" id="bpjs_status_bar"></div>
                                <div class="pl-2">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Status Kepesertaan JKN</p>
                                        <span id="bpjs_status_badge" class="px-2 py-0.5 rounded text-xs font-bold"></span>
                                    </div>
                                    <p id="bpjs_nama" class="font-bold text-gray-800 text-lg"></p>
                                    <p class="text-sm text-gray-600">No. Kartu: <span id="bpjs_no_kartu" class="font-medium"></span></p>
                                    <p class="text-xs text-gray-500 mt-2">Faskes Tk. 1: <span id="bpjs_faskes" class="font-medium text-gray-700"></span></p>
                                    <p class="text-xs text-gray-500">Jenis Peserta: <span id="bpjs_jenis" class="font-medium text-gray-700"></span></p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div>
                        <label for="new_patient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Pasien <span class="text-red-500">*</span></label>
                        <input type="text" id="new_patient_name" name="new_patient_name" value="{{ old('new_patient_name') }}" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" required @input="event.target.value = event.target.value.toUpperCase()">
                    </div>
                    <div>
                        <label for="new_patient_dob" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" id="new_patient_dob" name="new_patient_dob" value="{{ old('new_patient_dob') }}" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" required>
                    </div>
                    <div>
                        <label for="new_patient_gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="new_patient_gender" name="new_patient_gender" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" required>
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
                <h4 class="font-semibold text-gray-700 mb-2">Daftar Pasien Hari Ini:</h4>
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @forelse ($daftarAntreanBerobat as $antrean)
                    @php
                        $bgColor = ''; $statusText = '';
                        switch($antrean->status) {
                            case 'MENUNGGU': $bgColor = 'bg-yellow-50 border-l-4 border-yellow-400'; $statusText = 'Menunggu'; break;
                            case 'HADIR': $bgColor = 'bg-green-50 border-l-4 border-green-400'; $statusText = 'Hadir'; break;
                            case 'DIPANGGIL': $bgColor = 'bg-blue-50 border-l-4 border-blue-400'; $statusText = 'Dipanggil'; break;
                            case 'SELESAI': $bgColor = 'bg-gray-100 border-l-4 border-gray-400 text-gray-500'; $statusText = 'Selesai'; break;
                            case 'BATAL': $bgColor = 'bg-red-50 border-l-4 border-red-400 text-red-600'; $statusText = 'Batal'; break;
                        }
                    @endphp
                    <div class="flex items-center justify-between {{ $bgColor }} p-3 rounded-lg shadow-sm">
                        <div>
                            <p class="font-bold text-gray-800">{{ $antrean->queue_number }} - {{ $antrean->patient->user->full_name ?? $antrean->patient->full_name }}</p>
                            <p class="text-sm text-gray-500">Poli: {{ $antrean->poli->name }} | <span class="font-semibold">{{ $statusText }}</span></p>
                        </div>
                        
                        <div class="w-28 text-center">
                            @if ($antrean->status == 'MENUNGGU')
                                @if ($antrean->registered_by_user_id != null)
                                    <form action="{{ route('petugas-loket.antrean-offline.checkin', $antrean->id) }}" method="POST" class="checkin-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition checkin-btn w-full">
                                            Check-in
                                        </button>
                                    </form>
                                @else
                                    <div class="px-2 py-1 bg-yellow-200 text-yellow-800 text-xs font-bold rounded-full">
                                        Menunggu<br>Check-in QR
                                    </div>
                                @endif
                            @elseif ($antrean->status == 'HADIR')
                                <div class="px-3 py-1 bg-green-200 text-green-800 text-sm font-bold rounded-full">Hadir</div>
                            @elseif ($antrean->status == 'DIPANGGIL')
                                <div class="px-3 py-1 bg-blue-200 text-blue-800 text-sm font-bold rounded-full">Dipanggil</div>
                            @elseif ($antrean->status == 'SELESAI')
                                <div class="px-3 py-1 bg-gray-200 text-gray-800 text-sm font-bold rounded-full">Selesai</div>
                            @elseif ($antrean->status == 'BATAL')
                                <div class="px-3 py-1 bg-red-200 text-red-800 text-sm font-bold rounded-full">Batal</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 py-4">Tidak ada pasien dalam antrean hari ini.</p>
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
                 <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @forelse ($daftarAntreanApotek as $antrean)
                    @php
                        $statusTextApotek = ''; $bgColorApotek = '';
                        switch ($antrean->status) {
                            case 'DALAM_ANTREAN': $statusTextApotek = 'Dalam Antrean'; $bgColorApotek = 'bg-cyan-100'; break;
                            case 'SEDANG_DIRACIK': $statusTextApotek = 'Obat Disiapkan'; $bgColorApotek = 'bg-orange-100'; break;
                            case 'SIAP_DIAMBIL': $statusTextApotek = 'Siap Diambil'; $bgColorApotek = 'bg-yellow-100 font-semibold'; break;
                            case 'DISERAHKAN': $statusTextApotek = 'Diserahkan'; $bgColorApotek = 'bg-purple-100 font-semibold'; break;
                            case 'SELESAI': $statusTextApotek = 'Selesai'; $bgColorApotek = 'bg-gray-100 text-gray-500'; break;
                            default: $statusTextApotek = $antrean->status; $bgColorApotek = 'bg-gray-50'; break;
                        }
                    @endphp
                    <div class="{{ $bgColorApotek }} p-3 rounded-lg shadow-sm flex justify-between items-center">
                        <p class="font-bold text-gray-800">{{ $antrean->pharmacy_queue_number }} - {{ $antrean->patient_name }}</p>
                        <p class="text-sm font-semibold">{{ $statusTextApotek }}</p>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const poliSelect = document.getElementById('poli');
    const doctorSelect = document.getElementById('doctor');
    const antreanForm = document.getElementById('antreanOfflineForm');
    const checkinForms = document.querySelectorAll('.checkin-form');

    const nikInput = document.getElementById('new_patient_nik');
    const nameInput = document.getElementById('new_patient_name');
    const dobInput = document.getElementById('new_patient_dob');
    const genderSelect = document.getElementById('new_patient_gender');
    const nikStatusIndicator = document.getElementById('nik_status_indicator');
    const nikMessage = document.getElementById('nik_message');
    let fetchNikTimer; 

    // [BARU] LOGIKA CEK BPJS
    const btnCekBpjs = document.getElementById('btn_cek_bpjs');
    const bpjsInfoCard = document.getElementById('bpjs_info_card');

    function setPatientFormReadOnly(isReadOnly) {
        nameInput.readOnly = isReadOnly;
        dobInput.readOnly = isReadOnly;
        
        // [PERBAIKAN BUG GENDER]
        // Jangan pakai .disabled = true karena data tidak akan terkirim saat submit.
        // Gunakan CSS pointer-events untuk mengunci interaksi klik.
        if (isReadOnly) {
            genderSelect.style.pointerEvents = 'none';
            genderSelect.setAttribute('tabindex', '-1'); // Hilangkan dari urutan tombol Tab
        } else {
            genderSelect.style.pointerEvents = 'auto';
            genderSelect.removeAttribute('tabindex');
        }
        
        [nameInput, dobInput, genderSelect].forEach(el => {
            if (isReadOnly) {
                el.classList.add('bg-gray-200', 'text-gray-500');
                el.classList.remove('bg-gray-50');
            } else {
                el.classList.remove('bg-gray-200', 'text-gray-500');
                el.classList.add('bg-gray-50');
            }
        });
    }

    function resetPatientForm() {
        nameInput.value = '';
        dobInput.value = '';
        genderSelect.value = '';
        nikMessage.classList.add('hidden');
        nikMessage.textContent = '';
        bpjsInfoCard.classList.add('hidden'); // Sembunyikan kartu BPJS
        setPatientFormReadOnly(false); 
    }

    setPatientFormReadOnly(false);
    if (nameInput.value) { setPatientFormReadOnly(false); }

    nikInput.addEventListener('input', function() {
        clearTimeout(fetchNikTimer); 
        const nik = this.value;

        nikStatusIndicator.className = 'nik-status-indicator';
        nikMessage.classList.add('hidden');
        bpjsInfoCard.classList.add('hidden'); // Reset card BPJS tiap ngetik ulang

        if (nik.length !== 16) {
            if (nameInput.readOnly) resetPatientForm();
            return; 
        }

        nikStatusIndicator.className = 'nik-status-indicator loading';
        setPatientFormReadOnly(true); 

        fetchNikTimer = setTimeout(() => {
            // Ubah URL di bawah ini sesuai dengan Route Name Anda
            fetch(`{{ url('/petugas-loket/check-patient-nik') }}/${nik}`)
                .then(response => {
                    if (!response.ok) throw new Error('Respon server tidak baik.');
                    return response.json();
                })
                .then(data => {
                    if (data.found) {
                        nikStatusIndicator.className = 'nik-status-indicator success';
                        nikStatusIndicator.innerHTML = '&#10003;'; 
                        
                        nikMessage.textContent = 'Data pasien lama ditemukan.';
                        nikMessage.classList.remove('hidden', 'text-red-600');
                        nikMessage.classList.add('text-blue-600');

                        nameInput.value = data.full_name;
                        dobInput.value = data.date_of_birth;
                        genderSelect.value = data.gender;
                        
                        setPatientFormReadOnly(true); 
                    } else {
                        nikStatusIndicator.className = 'nik-status-indicator error';
                        nikStatusIndicator.innerHTML = '&#10005;'; 
                        
                        nikMessage.textContent = 'Pasien baru. Silakan isi form dan tekan "Cek Kepesertaan BPJS".';
                        nikMessage.classList.remove('hidden', 'text-blue-600');
                        nikMessage.classList.add('text-red-600');

                        resetPatientForm();
                        setPatientFormReadOnly(false); 
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    nikStatusIndicator.className = 'nik-status-indicator error';
                    nikStatusIndicator.innerHTML = '&#10005;'; 
                    nikMessage.textContent = 'Gagal memuat data lokal.';
                    nikMessage.classList.remove('hidden');
                    nikMessage.classList.add('text-red-600');
                    setPatientFormReadOnly(false); 
                });
        }, 500);
    });

    // [BARU] EVENT LISTENER CEK BPJS
    btnCekBpjs.addEventListener('click', function() {
        const nik = nikInput.value;
        if(nik.length !== 16) {
            Swal.fire('Oops!', 'Masukkan 16 digit NIK terlebih dahulu.', 'warning');
            return;
        }

        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Menghubungi BPJS...';
        this.disabled = true;

        fetch(`{{ url('/petugas-loket/check-bpjs') }}/${nik}`)
            .then(res => res.json())
            .then(data => {
                this.innerHTML = originalText;
                this.disabled = false;

                if(data.success) {
                    const peserta = data.data; // Response BPJS (Nama, status, faskes, dll)
                    
                    // Pastikan variabel response sesuai dengan API BPJS V-Claim v2 lo
                    const nama = peserta.nama || peserta.name || '-';
                    const noKartu = peserta.noKartu || peserta.no_kartu || '-';
                    const status = peserta.statusPeserta?.keterangan || peserta.status || 'Tidak Diketahui';
                    const faskes = peserta.provUmum?.nmProvider || peserta.faskes || '-';
                    const jenisPeserta = peserta.jenisPeserta?.keterangan || peserta.jenis_peserta || '-';

                    // Update UI Card
                    document.getElementById('bpjs_nama').textContent = nama;
                    document.getElementById('bpjs_no_kartu').textContent = noKartu;
                    document.getElementById('bpjs_faskes').textContent = faskes;
                    document.getElementById('bpjs_jenis').textContent = jenisPeserta;
                    
                    const badge = document.getElementById('bpjs_status_badge');
                    const bar = document.getElementById('bpjs_status_bar');
                    const card = document.getElementById('bpjs_info_card');
                    
                    badge.textContent = status;
                    
                    // Jika Status Aktif
                    if (status.toUpperCase().includes('AKTIF') && !status.toUpperCase().includes('TIDAK')) {
                        badge.className = 'px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700';
                        bar.className = 'absolute left-0 top-0 bottom-0 w-1.5 bg-green-500';
                        card.className = 'mt-3 bg-green-50/50 p-4 rounded-lg border border-green-200 shadow-sm relative overflow-hidden';
                    } else {
                        // Jika Status Tidak Aktif / Menunggak
                        badge.className = 'px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700';
                        bar.className = 'absolute left-0 top-0 bottom-0 w-1.5 bg-red-500';
                        card.className = 'mt-3 bg-red-50/50 p-4 rounded-lg border border-red-200 shadow-sm relative overflow-hidden';
                    }

                    bpjsInfoCard.classList.remove('hidden');

                    // Jika form belum terkunci (Pasien Baru), auto-fill nama dari BPJS
                    if(!nameInput.readOnly && nameInput.value === '') {
                        nameInput.value = nama;
                    }

                } else {
                    Swal.fire('Tidak Ditemukan', data.message || 'NIK tidak terdaftar sebagai peserta JKN/BPJS.', 'error');
                    bpjsInfoCard.classList.add('hidden');
                }
            })
            .catch(err => {
                this.innerHTML = originalText;
                this.disabled = false;
                Swal.fire('Error System', 'Terjadi kesalahan saat menghubungi server BPJS.', 'error');
                bpjsInfoCard.classList.add('hidden');
            });
    });

    // Logika Poli -> Dokter
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
                    doctorSelect.innerHTML = '<option value="">-- Gagal memuat dokter --</option>';
                });
        }
    });

    // Konfirmasi Pendaftaran
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
                    antreanForm.querySelector('button[type="submit"]').disabled = true;
                    antreanForm.querySelector('button[type="submit"]').textContent = 'Mendaftarkan...';
                    antreanForm.submit();
                }
            });
        });
    }

    // Konfirmasi Check-in
    if (checkinForms) {
        checkinForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const currentForm = this; 
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
                        currentForm.querySelector('button[type="submit"]').disabled = true;
                        currentForm.querySelector('button[type="submit"]').textContent = '...';
                        currentForm.submit();
                    }
                });
            });
        });
    }
});
</script>
@endpush