@extends('layouts.dokter_layout')

@section('title', 'Dashboard Dokter')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- CDN untuk Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single { height: 42px; border-color: #d1d5db; border-radius: 0.375rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; padding-left: 0.75rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
        .select2-dropdown { border-color: #d1d5db; border-radius: 0.375rem; }
        .modal-backdrop { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 40; }
        .modal-content { z-index: 50; }
        /* Style untuk input tekanan darah */
        .blood-pressure-input { display: flex; align-items: center; }
        .blood-pressure-input input { text-align: center; }
        .blood-pressure-input span { margin: 0 0.5rem; font-size: 1.5rem; color: #9ca3af; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
@endpush

@section('content')
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Pemeriksaan & Detail Pasien --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-4">Area Pemeriksaan Pasien</h2>
                
                @php $pasienAktif = $pasienSedangDipanggil ?? $pasienBerikutnya; @endphp

                @if($pasienAktif)
                    @php
                        $patient = $pasienAktif->patient;
                        $isNewHistory = empty($patient->blood_type) && empty($patient->known_allergies) && empty($patient->chronic_diseases);
                    @endphp
                    <div x-data="{ isNewHistory: {{ $isNewHistory ? 'true' : 'false' }} }">
                        <!-- Informasi Pasien -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm text-emerald-700">{{ $pasienSedangDipanggil ? 'Pasien Saat Ini:' : 'Pasien Berikutnya (Siap Dipanggil):' }}</p>
                                    <p class="text-xl font-bold text-emerald-900">{{ $patient->full_name }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} thn)</p>
                                    <p class="text-sm text-gray-600">No. Antrean: <span class="font-semibold">{{ $pasienAktif->queue_number }}</span></p>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <p class="text-sm text-emerald-700">Keluhan Utama:</p>
                                    <p class="font-semibold text-emerald-900 max-w-xs">{{ $pasienAktif->chief_complaint }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tampilkan form HANYA jika pasien sedang dipanggil --}}
                        @if($pasienSedangDipanggil)
                            <!-- Form Pemeriksaan -->
                            <form action="{{ route('dokter.antrean.simpanPemeriksaan', $pasienSedangDipanggil->id) }}" method="POST" id="formPemeriksaan">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                
                                @if($isNewHistory)
                                <div class="mb-6 border border-yellow-300 bg-yellow-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold text-yellow-800 mb-3">Pasien Baru Terdeteksi</h3>
                                    <p class="text-sm text-yellow-700 mb-4">Harap lengkapi data riwayat kesehatan pasien berikut ini (opsional).</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                                            <select name="blood_type" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                                                <option value="">Pilih...</option> <option value="A">A</option> <option value="B">B</option>
                                                <option value="AB">AB</option> <option value="O">O</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="known_allergies" class="block text-sm font-medium text-gray-700">Alergi yang Diketahui</label>
                                            <input type="text" name="known_allergies" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Paracetamol, Udang">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label for="chronic_diseases" class="block text-sm font-medium text-gray-700">Penyakit Kronis</label>
                                            <input type="text" name="chronic_diseases" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Hipertensi, Diabetes">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Seksi Pemeriksaan Fisik -->
                                <div class="space-y-6 border-t pt-6">
                                    {{-- Tanda Vital --}}
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Tanda Vital</h3>
                                        
                                        <!-- Baris 1: Input Wajib -->
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-x-6 gap-y-4 items-end">
                                            <div class="md:col-span-2">
                                                <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700">Tekanan Darah (mmHg) <span class="text-red-500">*</span></label>
                                                <div class="blood-pressure-input mt-1">
                                                    <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Sistolik" required>
                                                    <span>/</span>
                                                    <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Diastolik" required>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="heart_rate" class="block text-sm font-medium text-gray-700">Nadi (x/mnt) <span class="text-red-500">*</span></label>
                                                <input type="number" name="heart_rate" id="heart_rate" class="mt-1 w-full p-2 border border-gray-300 rounded-md" required>
                                            </div>
                                            <div>
                                                <label for="temperature" class="block text-sm font-medium text-gray-700">Suhu (°C) <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.1" name="temperature" id="temperature" class="mt-1 w-full p-2 border border-gray-300 rounded-md" required>
                                            </div>
                                        </div>
                                        
                                        <!-- Baris 2: Input Opsional -->
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-x-6 gap-y-4 mt-4 items-end">
                                            <div>
                                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Pernapasan (x/mnt)</label>
                                                <input type="number" name="respiratory_rate" id="respiratory_rate" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                                            </div>
                                            <div>
                                                <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700">Saturasi O₂ (%)</label>
                                                <input type="number" name="oxygen_saturation" id="oxygen_saturation" class="mt-1 w-full p-2 border border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Temuan Fisik Lainnya --}}
                                    <div>
                                        <label for="physical_examination_notes" class="block text-sm font-medium text-gray-700 mb-1">Temuan Pemeriksaan Fisik (Objektif)</label>
                                        <textarea name="physical_examination_notes" id="physical_examination_notes" rows="4" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Konjungtiva anemis (-), sklera ikterik (-), murmur (-), wheezing (-/-), nyeri tekan abdomen (-)"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Seksi Asesmen & Rencana -->
                                <div class="space-y-6 border-t pt-6 mt-6">
                                    
                                    <!-- Diagnosis Utama (ICD 10) -->
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                        <label class="block text-lg font-semibold text-blue-800 mb-2">Diagnosis Utama (ICD-10) <span class="text-red-500">*</span></label>
                                        
                                        <div class="flex gap-2">
                                            <div class="flex-grow">
                                                <input type="hidden" name="icd10_code" id="icd10_code" required>
                                                <input type="hidden" name="icd10_name" id="icd10_name" required>
                                                <input type="text" id="icd10_display" class="w-full p-3 border border-blue-300 rounded-md bg-white text-gray-700 cursor-not-allowed font-medium" placeholder="Belum ada diagnosis dipilih... (Klik tombol Cari)" readonly>
                                            </div>
                                            <button type="button" id="openIcd10ModalBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition flex items-center shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                Cari ICD-10
                                            </button>
                                        </div>
                                        <p class="text-xs text-blue-600 mt-1">Diagnosis utama wajib menggunakan standar ICD-10. Data diambil langsung dari database.</p>
                                    </div>

                                    <!-- Diagnosis Tambahan (Tags) -->
                                    <div>
                                        <label for="diagnosis_tags" class="block text-lg font-semibold text-gray-700 mb-2">Diagnosis Tambahan / Catatan (Tags)</label>
                                        <select name="diagnosis_tags[]" id="diagnosis_tags" class="w-full" multiple="multiple">
                                            @if($diagnosisTags) @foreach($diagnosisTags as $tag) <option value="{{ $tag->tag_name }}">{{ $tag->tag_name }}</option> @endforeach @endif
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Gunakan ini untuk diagnosis sekunder atau catatan tambahan (bebas ketik).</p>
                                    </div>

                                    <!-- [BARU] TINDAKAN / PEMERIKSAAN TAMBAHAN -->
                                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-200 shadow-sm relative overflow-hidden">
                                        <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-100 rounded-bl-full opacity-50"></div>
                                        
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex items-center">
                                                <div class="bg-indigo-600 text-white p-2 rounded-lg mr-3 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-indigo-900">Tindakan / Pemeriksaan Tambahan</h3>
                                                    <p class="text-xs text-indigo-700">Input tindakan medis (cek lab sederhana, nebulizer, dll) untuk ditagihkan ke pasien.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="actions-container" class="space-y-3">
                                            <!-- Row Tindakan akan di-generate via JS di sini -->
                                        </div>

                                        <button type="button" id="btn-add-action" class="mt-4 w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-indigo-50 border-indigo-300 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Tambah Tindakan
                                        </button>
                                    </div>
                                    <!-- END [BARU] TINDAKAN -->

                                    <div>
                                        <label for="doctor_notes" class="block text-lg font-semibold text-gray-700 mb-2">Rencana Penatalaksanaan (Plan) <span class="text-red-500">*</span></label>
                                        <textarea name="doctor_notes" id="doctor_notes" rows="4" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tuliskan rencana, edukasi, atau catatan lain untuk pasien..." required></textarea>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Resep Obat</h3>
                                        <button type="button" id="showObatModalBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" /></svg>
                                                Tambah & Pilih Obat
                                        </button>
                                        <div id="resep-obat-list" class="mt-4 space-y-2"></div>
                                    </div>
                                </div>
                                
                                <div id="hidden-medicine-inputs"></div>
                                
                                <div class="mt-8 border-t pt-6 flex justify-end">
                                    <button type="submit" form="formPemeriksaan" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-300">Selesaikan Pemeriksaan & Simpan</button>
                                </div>
                            </form>
                        @endif
                    </div>

                @else
                    <div class="text-center text-gray-500 py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <h3 class="text-xl font-semibold">Tidak ada pasien yang sedang diperiksa.</h3>
                        <p class="mt-2">Panggil pasien berikutnya dari daftar di samping.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Antrean --}}
        <div>
            @if($pasienBerikutnya && !$pasienSedangDipanggil)
            <div class="bg-white rounded-xl shadow-lg mb-8 border-2 border-blue-500">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-blue-50 text-blue-800">Panggil Pasien Berikutnya</h3>
                <div class="p-6 text-center">
                    <p class="font-extrabold text-6xl text-blue-600">{{ $pasienBerikutnya->queue_number }}</p>
                    <p class="font-semibold text-xl text-gray-800 mt-2">{{ $pasienBerikutnya->patient->user->full_name ?? $pasienBerikutnya->patient->full_name }}</p>
                    <p class="text-sm text-gray-500">Check-in pukul: {{ $pasienBerikutnya->check_in_time ? $pasienBerikutnya->check_in_time->format('H:i') : '-' }} WIB</p>
                    
                    <form action="{{ route('dokter.antrean.panggil', $pasienBerikutnya->id) }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg text-lg animate-pulse">
                            Panggil Sekarang
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-green-50 text-green-800">Antrean Hadir Berikutnya</h3>
                <div class="p-4 max-h-64 overflow-y-auto">
                    @forelse($antreanMenunggu as $index => $antrean)
                        <div class="flex items-center justify-between p-3 rounded-md {{ $loop->first ? 'bg-blue-50 border border-blue-200' : 'border-b' }}">
                            <div>
                                <p class="font-bold text-xl text-gray-800">{{ $antrean->queue_number }}</p>
                                <p class="text-sm text-gray-600">{{ $antrean->patient->user->full_name ?? $antrean->patient->full_name }}</p>
                            </div>
                            <span class="text-xs text-gray-400">Menunggu giliran</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Tidak ada pasien lain yang sedang menunggu.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-gray-50">Antrean Menunggu (Belum Check-In)</h3>
                <div class="p-4 max-h-64 overflow-y-auto">
                    @forelse($antreanMenunggu as $antrean)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <p class="font-bold text-xl text-gray-500">{{ $antrean->queue_number }}</p>
                                <p class="text-sm text-gray-500">{{ $antrean->patient->user->full_name ?? $antrean->patient->full_name }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Belum Check-in</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Tidak ada pasien dalam antrean.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4">Riwayat Selesai Hari Ini</h3>
                <div class="p-4 max-h-80 overflow-y-auto">
                     @forelse($antreanSelesai as $antrean)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <p class="font-semibold text-gray-700">{{ $antrean->queue_number }} - {{ $antrean->patient->user->full_name ?? $antrean->patient->full_name }}</p>
                                <p class="text-xs text-gray-500">Selesai pada: {{ $antrean->finish_time ? $antrean->finish_time->format('H:i') : '-' }} WIB</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $antrean->status == 'SELESAI' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucwords(strtolower($antrean->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Belum ada pasien yang selesai diperiksa.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($pasienAktif)
    <!-- MODAL OBAT -->
    <div id="obatModal" class="fixed inset-0 flex items-center justify-center hidden">
        <div class="modal-backdrop" id="modalBackdrop"></div>
        <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-3xl m-4 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="text-2xl font-bold text-gray-800">Formulir Resep Obat</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <div id="medicine-rows-container" class="space-y-4"></div>
                <button type="button" id="add-medicine-row-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md text-sm w-full border border-gray-300">
                    + Tambah Baris Obat
                </button>
            </div>
            <div class="flex justify-end items-center p-5 border-t bg-gray-50 rounded-b-xl">
                <button type="button" id="cancelModalBtn" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg mr-3">Batal</button>
                <button type="button" id="save-resep-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg">Simpan Resep</button>
            </div>
        </div>
    </div>

    <!-- MODAL ICD 10 (AJAX) -->
    <div id="icd10Modal" class="fixed inset-0 flex items-center justify-center hidden z-50">
        <div class="fixed inset-0 bg-black bg-opacity-50" id="icd10ModalBackdrop"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl m-4 flex flex-col max-h-[85vh] z-50 relative">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="text-xl font-bold text-gray-800">Cari Diagnosis ICD-10</h3>
                <button id="closeIcd10ModalBtn" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-5">
                <!-- Search Bar -->
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" id="icd10SearchInput" 
                           data-search-url="{{ route('dokter.icd10.search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Ketik kode (Contoh: A00) atau nama penyakit..." autocomplete="off">
                </div>

                <!-- Loading Indicator -->
                <div id="icd10Loading" class="hidden text-center py-4 text-blue-500">
                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-xs mt-1">Mencari di database...</span>
                </div>

                <!-- List Container -->
                <div class="overflow-y-auto h-80 border border-gray-200 rounded-lg custom-scrollbar bg-gray-50" id="icd10ListContainer">
                     <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium">Mulai ketik untuk mencari diagnosis...</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center p-5 border-t bg-gray-50 rounded-b-xl">
                 <button type="button" id="cancelIcd10ModalBtn" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg">Batal</button>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const formPemeriksaan = document.getElementById('formPemeriksaan');

        if(formPemeriksaan) {
            $('#diagnosis_tags').select2({
                tags: true,
                placeholder: "Tulis catatan tambahan (opsional)",
                tokenSeparators: [',']
            });

            // --- [BARU] LOGIKA TINDAKAN / PEMERIKSAAN TAMBAHAN ---
            const actionsData = @json($availableActions ?? []);
            const actionsContainer = $('#actions-container');
            const btnAddAction = $('#btn-add-action');
            let actionRowCounter = 0;

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            };

            const addActionRow = () => {
                const rowIndex = actionRowCounter++;
                let optionsHtml = '<option value="">-- Pilih Tindakan --</option>';
                
                if (Array.isArray(actionsData)) {
                    actionsData.forEach(act => {
                        optionsHtml += `<option value="${act.id}" data-price="${act.price}">${act.name}</option>`;
                    });
                }

                const rowHtml = `
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start bg-white p-3 rounded-md border border-indigo-100 shadow-sm action-row animate-fade-in-down" id="action-row-${rowIndex}">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jenis Tindakan</label>
                            <select name="actions[${rowIndex}][id]" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 action-select" required>
                                ${optionsHtml}
                            </select>
                            <p class="text-xs text-indigo-600 mt-1 font-bold price-display">Harga: -</p>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Hasil / Keterangan (Opsional)</label>
                            <input type="text" name="actions[${rowIndex}][result]" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cth: 200 mg/dL, 5 Jahitan, Normal">
                        </div>
                        <div class="md:col-span-1 flex justify-end mt-6">
                            <button type="button" class="text-red-500 hover:text-red-700 p-1 remove-action-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                actionsContainer.append(rowHtml);
            };

            btnAddAction.on('click', addActionRow);

            actionsContainer.on('click', '.remove-action-btn', function() {
                $(this).closest('.action-row').remove();
            });

            actionsContainer.on('change', '.action-select', function() {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                const priceDisplay = $(this).siblings('.price-display');
                if (price) {
                    priceDisplay.text(`Harga: ${formatRupiah(price)}`);
                } else {
                    priceDisplay.text('Harga: -');
                }
            });
            // --- END LOGIKA TINDAKAN ---
            
            // --- LOGIKA MODAL OBAT ---
            const medicinesData = @json($medicines ?? []);
            
            let medicineRowCounter = 0;
            const modal = $('#obatModal');
            const showModalBtn = $('#showObatModalBtn');
            const closeModalBtn = $('#closeModalBtn');
            const cancelModalBtn = $('#cancelModalBtn');
            const modalBackdrop = $('#modalBackdrop');
            const addMedicineRowBtn = $('#add-medicine-row-btn');
            const medicineRowsContainer = $('#medicine-rows-container');
            const saveResepBtn = $('#save-resep-btn');
            const openModal = () => modal.removeClass('hidden');
            const closeModal = () => modal.addClass('hidden');
            
            const addMedicineRow = () => {
                medicineRowCounter++;
                let medOptionsHtml = '<option value="">Pilih Obat</option>';
                
                if (Array.isArray(medicinesData)) {
                    medicinesData.forEach(med => {
                        medOptionsHtml += `<option value="${med.id}" data-stock="${med.stock}">${med.name} (Stok: ${med.stock})</option>`;
                    });
                }
                
                const dosageOptions = [
                    "1x1 sehari sesudah makan", "2x1 sehari sesudah makan", "3x1 sehari sesudah makan",
                    "1x1 sehari sebelum makan", "2x1 sehari sebelum makan", "3x1 sehari sebelum makan",
                    "Jika perlu", "Oleskan", "Teteskan", "Lainnya..."
                ];
                let dosageOptionsHtml = dosageOptions.map(opt => `<option value="${opt}">${opt}</option>`).join('');
                
                const newRowHtml = `
                    <div class="grid grid-cols-12 gap-3 p-3 bg-gray-50 rounded-lg border medicine-row" data-row-id="${medicineRowCounter}">
                        <div class="col-span-12 sm:col-span-4"><label class="text-sm font-medium text-gray-700">Nama Obat</label><select class="w-full p-2 mt-1 border border-gray-300 rounded-md medicine-select" required>${medOptionsHtml}</select></div>
                        <div class="col-span-6 sm:col-span-2"><label class="text-sm font-medium text-gray-700">Jumlah</label><input type="number" class="w-full p-2 mt-1 border border-gray-300 rounded-md quantity-input" min="1" required></div>
                        <div class="col-span-6 sm:col-span-4"><label class="text-sm font-medium text-gray-700">Dosis / Aturan Pakai</label><select class="w-full p-2 mt-1 border border-gray-300 rounded-md dosage-select">${dosageOptionsHtml}</select><input type="text" class="w-full p-2 mt-1 border border-gray-300 rounded-md dosage-text-input hidden" placeholder="Tulis dosis kustom..." ></div>
                        <div class="col-span-12 sm:col-span-2 flex items-end"><button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded-md w-full mt-1 sm:mt-0 remove-row-btn">Hapus</button></div>
                    </div>
                `;
                medicineRowsContainer.append(newRowHtml);
                $(`.medicine-row[data-row-id="${medicineRowCounter}"] .medicine-select`).select2({
                    placeholder: "Pilih Obat", dropdownParent: modal, width: '100%'
                });
            };
            
            showModalBtn.on('click', () => {
                if(medicineRowsContainer.is(':empty')){ addMedicineRow(); }
                openModal();
            });
            [closeModalBtn, cancelModalBtn, modalBackdrop].forEach(el => el.on('click', closeModal));
            addMedicineRowBtn.on('click', addMedicineRow);
            medicineRowsContainer.on('click', '.remove-row-btn', function() { $(this).closest('.medicine-row').remove(); });
            
            medicineRowsContainer.on('change', '.medicine-select', function() {
                const stock = $(this).find('option:selected').data('stock');
                const quantityInput = $(this).closest('.medicine-row').find('.quantity-input');
                if (stock) {
                    quantityInput.attr('max', stock);
                    if (parseInt(quantityInput.val()) > stock) { quantityInput.val(stock); }
                } else {
                    quantityInput.removeAttr('max');
                }
            });
            
            medicineRowsContainer.on('change', '.dosage-select', function() {
                const textInput = $(this).siblings('.dosage-text-input');
                if ($(this).val() === 'Lainnya...') {
                    textInput.removeClass('hidden'); textInput.prop('required', true);
                } else {
                    textInput.addClass('hidden'); textInput.prop('required', false);
                }
            });
            
            saveResepBtn.on('click', function() {
                let isValid = true, firstError = null;
                medicineRowsContainer.find('.medicine-row').each(function() {
                    if (!$(this).find('.medicine-select').val()) isValid = false;
                    if (!$(this).find('.quantity-input').val()) isValid = false;
                    const dosageSelect = $(this).find('.dosage-select');
                    if (dosageSelect.val() === 'Lainnya...' && !$(this).find('.dosage-text-input').val()) {
                        isValid = false;
                        if (!firstError) firstError = 'Harap isi dosis kustom jika memilih "Lainnya...".';
                    }
                });
                
                if (!isValid) {
                    Swal.fire('Data Tidak Lengkap', firstError || 'Harap isi semua kolom untuk setiap baris obat.', 'error');
                    return;
                }
                
                Swal.fire({
                    title: 'Konfirmasi Resep', text: "Anda yakin ingin menyimpan resep ini? Data yang lama akan diganti.",
                    icon: 'question', showCancelButton: true, confirmButtonColor: '#10B981',
                    cancelButtonColor: '#d33', confirmButtonText: 'Ya, Simpan!', cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#hidden-medicine-inputs, #resep-obat-list').empty();
                        medicineRowsContainer.find('.medicine-row').each(function(index) {
                            const row = $(this), medId = row.find('.medicine-select').val();
                            const medName = row.find('option:selected').text(), quantity = row.find('.quantity-input').val();
                            let dosage = row.find('.dosage-select').val();
                            if (dosage === 'Lainnya...') { dosage = row.find('.dosage-text-input').val(); }
                            $('#hidden-medicine-inputs').append(`<input type="hidden" name="medicines[${index}][id]" value="${medId}"><input type="hidden" name="medicines[${index}][quantity]" value="${quantity}"><input type="hidden" name="medicines[${index}][dosage]" value="${dosage}">`);
                            $('#resep-obat-list').append(`<div class="flex justify-between items-center p-3 bg-emerald-50 border border-emerald-200 rounded-md"><div><p class="font-semibold text-emerald-800">${medName.replace(/\s\(Stok: \d+\)$/, '')}</p><p class="text-sm text-gray-600">Jumlah: ${quantity} | Dosis: ${dosage}</p></div></div>`);
                        });
                        closeModal();
                        Swal.fire('Berhasil!', 'Resep telah ditambahkan ke pemeriksaan.', 'success');
                    }
                });
            });

            // --- LOGIKA MODAL ICD 10 DENGAN AJAX ---
            const icd10Modal = $('#icd10Modal');
            const openIcd10Btn = $('#openIcd10ModalBtn');
            const closeIcd10Btn = $('#closeIcd10ModalBtn');
            const cancelIcd10Btn = $('#cancelIcd10ModalBtn');
            const icd10Backdrop = $('#icd10ModalBackdrop');
            const icd10ListContainer = $('#icd10ListContainer');
            const icd10SearchInput = $('#icd10SearchInput');
            const icd10Loading = $('#icd10Loading');
            
            const icd10CodeInput = $('#icd10_code');
            const icd10NameInput = $('#icd10_name');
            const icd10DisplayInput = $('#icd10_display');

            const toggleIcd10Modal = (show) => {
                if(show) {
                    icd10Modal.removeClass('hidden');
                    icd10SearchInput.focus();
                } else {
                    icd10Modal.addClass('hidden');
                }
            };

            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            const fetchIcd10 = debounce(function(query) {
                if (query.length < 2) {
                    icd10ListContainer.html('<div class="flex flex-col items-center justify-center h-40 text-gray-400"><svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><p class="text-sm">Ketik minimal 2 karakter...</p></div>');
                    return;
                }

                const url = icd10SearchInput.data('search-url') + '?q=' + encodeURIComponent(query);
                
                icd10Loading.removeClass('hidden'); 
                icd10ListContainer.addClass('opacity-50');

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        icd10ListContainer.empty();
                        if(data.length === 0) {
                            icd10ListContainer.append(`
                                <div class="flex flex-col items-center justify-center h-40 text-gray-500">
                                    <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-medium">Tidak ditemukan</p>
                                    <p class="text-xs text-gray-400">Coba kata kunci lain atau kode ICD spesifik.</p>
                                </div>
                            `);
                        } else {
                            let html = '<div class="flex flex-col">';
                            data.forEach(item => {
                                const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                                const highlightedName = item.name.replace(regex, '<span class="bg-yellow-200 font-semibold">$1</span>');
                                const highlightedCode = item.code.replace(regex, '<span class="bg-yellow-200 text-blue-800">$1</span>');

                                html += `
                                    <div class="group flex items-center justify-between p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-all duration-200 icd10-item" 
                                        data-code="${item.code}" 
                                        data-name="${item.name}">
                                        
                                        <div class="flex items-start gap-3 overflow-hidden">
                                            <div class="flex-shrink-0 bg-blue-100 text-blue-700 font-mono font-bold px-2 py-1 rounded text-sm border border-blue-200 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-colors">
                                                ${highlightedCode}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-gray-800 text-sm font-medium leading-snug group-hover:text-blue-900">
                                                    ${highlightedName}
                                                </span>
                                                <span class="text-[10px] text-gray-400 group-hover:text-blue-400">ICD-10 Standard</span>
                                            </div>
                                        </div>

                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 ml-2">
                                            <span class="text-xs bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm font-medium">
                                                Pilih
                                            </span>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            icd10ListContainer.append(html);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        icd10ListContainer.html('<p class="text-center text-red-500 py-4">Gagal mengambil data.</p>');
                    })
                    .finally(() => {
                        icd10Loading.addClass('hidden'); 
                        icd10ListContainer.removeClass('opacity-50');
                    });
            }, 400);

            openIcd10Btn.on('click', () => toggleIcd10Modal(true));
            [closeIcd10Btn, cancelIcd10Btn, icd10Backdrop].forEach(el => el.on('click', () => toggleIcd10Modal(false)));

            icd10SearchInput.on('input', function() {
                fetchIcd10($(this).val());
            });

            icd10ListContainer.on('click', '.icd10-item', function() {
                const code = $(this).data('code');
                const name = $(this).data('name');
                icd10CodeInput.val(code);
                icd10NameInput.val(name);
                icd10DisplayInput.val(`${code} - ${name}`);
                toggleIcd10Modal(false);
            });

            formPemeriksaan.addEventListener('submit', function(e) {
                e.preventDefault();
                const systolic = document.getElementById('blood_pressure_systolic').value;
                const diastolic = document.getElementById('blood_pressure_diastolic').value;
                const heartRate = document.getElementById('heart_rate').value;
                const temperature = document.getElementById('temperature').value;
                const doctorNotes = document.getElementById('doctor_notes').value;
                const icd10Code = $('#icd10_code').val();

                if (!systolic || !diastolic || !heartRate || !temperature || !doctorNotes.trim() || !icd10Code) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Pemeriksaan Tidak Lengkap',
                        text: 'Harap lengkapi Tanda Vital, Diagnosis Utama (ICD-10), dan Rencana Penatalaksanaan.',
                    });
                    return; 
                }

                Swal.fire({
                    title: 'Konfirmasi Penyimpanan',
                    text: "Anda yakin ingin menyelesaikan pemeriksaan dan menyimpan data ini? Tindakan ini tidak dapat diubah.",
                    icon: 'warning', 
                    showCancelButton: true, 
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#d33', 
                    confirmButtonText: 'Ya, Simpan!', 
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formPemeriksaan.submit(); 
                    }
                });
            });
        }
    });
    </script>
@endpush