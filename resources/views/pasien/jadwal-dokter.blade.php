@extends('layouts.pasien_layout')

@section('title', 'Jadwal Dokter')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- CDN Alpine.js (diperlukan untuk toggle di modal) --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Styling tambahan jika diperlukan */
        .schedule-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        /* Style untuk input disabled agar terlihat seperti teks biasa */
        .disabled-input {
            background-color: #f3f4f6; /* bg-gray-100 */
            border: 1px solid #d1d5db; /* border-gray-300 */
            color: #4b5563; /* text-gray-600 */
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
{{-- ====================================================== --}}
{{-- == REVISI: Pindahkan Kalkulasi ke Sini == --}}
{{-- ====================================================== --}}
@php
    $patientData = Auth::user()->patient ?? null; 
    $hasActiveQueueToday = false;
    if ($patientData) {
        $tz = 'Asia/Jakarta';
        $startOfDay = \Carbon\Carbon::now($tz)->startOfDay();
        $endOfDay = \Carbon\Carbon::now($tz)->endOfDay();
        // Cek antrean Klinik yang aktif
        $hasActiveClinicQueue = \App\Models\ClinicQueue::where('patient_id', $patientData->id)
                             ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                             ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                             ->exists();
        // Cek antrean Apotek yang aktif (belum diterima pasien)
        $hasActivePharmacyQueue = \App\Models\PharmacyQueue::whereHas('clinicQueue', function ($query) use ($patientData, $startOfDay, $endOfDay) {
                                $query->where('patient_id', $patientData->id)
                                      ->whereBetween('registration_time', [$startOfDay, $endOfDay]);
                             })
                             ->whereNotIn('status', ['DITERIMA_PASIEN', 'BATAL'])
                             ->exists();
                             
        $hasActiveQueueToday = $hasActiveClinicQueue || $hasActivePharmacyQueue;
    }
@endphp
{{-- ====================================================== --}}

<div class="w-full max-w-6xl mx-auto">
    {{-- Container Judul Utama --}}
    <div class="bg-white rounded-xl shadow-lg p-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center">Jadwal Praktik Dokter</h2>
    </div>

    {{-- Card Jadwal Hari Ini --}}
    <div class="bg-gradient-to-r from-blue-950 to-blue-300 rounded-xl shadow-lg p-6 mb-8 border border-cyan-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">Jadwal Hari Ini</h3>
                <p class="text-white">{{ $now->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="mt-2 sm:mt-0 text-lg font-semibold text-gray-700 bg-white/50 px-3 py-1 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                {{ $now->format('H:i') }} WIB
            </div>
        </div>

        @if($todaySchedules->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($todaySchedules as $schedule)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex flex-col justify-between schedule-card">
                        <div>
                            <p class="font-semibold text-base text-gray-800">{{ $schedule->doctor->user->full_name ?? 'N/A' }}</p>
                            <p class="text-xs font-medium text-emerald-700">{{ $schedule->doctor->poli->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </p>
                        </div>
                        {{-- ====================================================== --}}
                        {{-- == REVISI: TOMBOL HANYA MUNCUL JIKA TIDAK ADA ANTRIAN AKTIF == --}}
                        {{-- ====================================================== --}}
                        @if(!$hasActiveQueueToday)
                            <button 
                                class="mt-3 w-full bg-[#24306E] hover:bg-[#1a224d] text-white text-xs font-bold py-2 px-3 rounded-lg transition duration-300 shadow-md open-schedule-queue-modal-btn"
                                data-schedule-id="{{ $schedule->id }}"
                                data-doctor-id="{{ $schedule->doctor->id }}"
                                data-poli-id="{{ $schedule->doctor->poli->id }}"
                                data-doctor-name="{{ $schedule->doctor->user->full_name ?? 'N/A' }}"
                                data-poli-name="{{ $schedule->doctor->poli->name ?? 'N/A' }}">
                                Buat Antrean
                            </button>
                        @else
                            {{-- Tampilkan tombol disabled atau pesan jika sudah ada antrean --}}
                             <button 
                                class="mt-3 w-full bg-gray-400 text-white text-xs font-bold py-2 px-3 rounded-lg cursor-not-allowed" disabled>
                                Anda Memiliki Antrean Aktif
                            </button>
                        @endif
                        {{-- ====================================================== --}}
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 italic py-4">Tidak ada dokter yang praktik hari ini.</p>
        @endif
    </div>

    {{-- Tampilan Jadwal Mingguan (Tidak Diubah) --}}
    <div class="space-y-8">
        @foreach ($daysOrder as $day)
            @if(isset($groupedSchedules[$day]) && $groupedSchedules[$day]->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <h3 class="text-xl font-bold text-gray-100 p-4 bg-[#24306E]">
                        {{ $day }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach ($groupedSchedules[$day] as $schedule)
                            <div class="schedule-card border border-gray-200 rounded-lg p-4 bg-gray-50 flex flex-col justify-between" data-poli-id="{{ $schedule->doctor->poli->id }}">
                                <div>
                                    <p class="font-bold text-lg text-[#1a224d]">{{ $schedule->doctor->user->full_name ?? 'Nama Dokter Tidak Tersedia' }}</p>
                                    <p class="text-sm font-semibold text-emerald-700">{{ $schedule->doctor->poli->name ?? 'Poli Tidak Tersedia' }}</p>
                                    <p class="text-gray-600 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                    </p>
                                </div>
                                {{-- Tombol di jadwal mingguan TIDAK ditambahkan sesuai permintaan awal --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection

@push('modals')
{{-- ====================================================== --}}
{{-- == MODAL ANTRIAN BARU (DICOPY DARI DASHBOARD) == --}}
{{-- ====================================================== --}}
{{-- Hanya tampilkan modal jika pasien BELUM punya antrean aktif --}}
{{-- Variabel $hasActiveQueueToday diambil dari @php block di @section('content') --}}
@if(!$hasActiveQueueToday) 
<div id="antrianModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
    <div id="modalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[95vh] transform transition-all" 
         x-data="{ isFamily: false, customRelationship: false, nikInput: '' }">
        <div class="text-center p-6 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-2xl font-bold text-gray-800">Formulir Antrean Baru</h3>
        </div>
        <div class="overflow-y-auto p-8 flex-grow">
            {{-- Menggunakan data pasien dari Auth langsung --}}
            @if($patientData) 
            <form id="antrianForm" action="{{ route('pasien.antrean.store') }}" method="POST">
                @csrf
                {{-- Toggle Diri Sendiri / Keluarga --}}
                {{-- <div class="flex items-center justify-center mb-6">
                    <label class="text-sm font-medium text-gray-900">Daftarkan Diri Sendiri</label>
                    <button type="button" @click="isFamily = !isFamily" :class="isFamily ? 'bg-indigo-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 mx-3" role="switch">
                        <span :class="isFamily ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                    <label class="text-sm font-medium text-gray-900">Daftarkan Anggota Keluarga</label>
                    <input type="hidden" name="is_family" x-bind:value="isFamily">
                </div> --}}
                <div class="border-t border-gray-200 pt-6">
                    {{-- Form Data Diri Sendiri --}}
                    <div x-show="!isFamily" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2">Data Pasien</h4>
                          <div>
                            <label for="self_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" id="self_nama" class="w-full p-2 disabled-input rounded-md" value="{{ $patientData->full_name ?? Auth::user()->full_name }}" readonly>
                            <input type="hidden" name="patient_id" value="{{ $patientData->id }}">
                          </div>
                          <div>
                            <label for="self_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <input type="text" id="self_nik" class="w-full p-2 disabled-input rounded-md" value="{{ $patientData->nik ?? 'NIK tidak ditemukan' }}" readonly>
                          </div>
                    </div>
                    {{-- Form Data Anggota Keluarga
                    <div x-show="isFamily" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4 border-b border-gray-200 pb-4">
                        <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2">Data Anggota Keluarga</h4>
                        <div>
                            <label for="new_patient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Pasien <span class="text-red-500">*</span></label>
                            <input type="text" name="new_patient_name" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily" @input="event.target.value = event.target.value.toUpperCase()">
                        </div>
                        <div>
                            <label for="new_patient_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="new_patient_nik" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily" maxlength="16" x-model="nikInput" @input="nikInput = nikInput.replace(/\D/g, '')">
                            <p x-show="isFamily && nikInput.length > 0 && nikInput.length !== 16" class="text-xs text-red-600 mt-1">NIK harus terdiri dari 16 digit angka.</p>
                        </div>
                        <div>
                            <label for="new_patient_dob" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="new_patient_dob" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily">
                        </div>
                        <div>
                            <label for="new_patient_gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="new_patient_gender" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily">
                                <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="patient_relationship" class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga <span class="text-red-500">*</span></label>
                            <select name="patient_relationship" @change="customRelationship = ($event.target.value === 'Lainnya')" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily">
                                <option value="" disabled selected>-- Pilih Hubungan --</option>
                                <option value="Anak">Anak</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Pasangan">Pasangan</option>
                                <option value="Saudara Kandung">Saudara Kandung</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div x-show="customRelationship" x-transition class="md:col-span-2">
                            <label for="patient_relationship_custom" class="block text-sm font-medium text-gray-700 mb-1">Sebutkan Hubungan Lainnya</label>
                            <input type="text" name="patient_relationship_custom" class="w-full p-2 border border-gray-300 rounded-md" :required="customRelationship">
                        </div>
                    </div> --}}
                    {{-- Detail Pendaftaran --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2 pt-4" :class="isFamily ? '' : 'border-t border-gray-200'">Detail Pendaftaran</h4>
                        {{-- ====================================================== --}}
                        {{-- == INPUT POLI & DOKTER (MODIFIKASI UNTUK JADWAL DOKTER) == --}}
                        {{-- ====================================================== --}}
                        <div>
                            <label for="poli_display" class="block text-sm font-medium text-gray-700 mb-1">Poli <span class="text-red-500">*</span></label>
                            {{-- Input teks yang disabled untuk menampilkan nama poli --}}
                            <input type="text" id="poli_display" class="w-full p-2 disabled-input rounded-md" readonly>
                            {{-- Input hidden untuk mengirim ID poli --}}
                            <input type="hidden" id="poli_id" name="poli_id" required>
                        </div>
                        <div>
                            <label for="doctor_display" class="block text-sm font-medium text-gray-700 mb-1">Dokter <span class="text-red-500">*</span></label>
                            {{-- Input teks yang disabled untuk menampilkan nama dokter --}}
                            <input type="text" id="doctor_display" class="w-full p-2 disabled-input rounded-md" readonly>
                            {{-- Input hidden untuk mengirim ID dokter --}}
                            <input type="hidden" id="doctor_id" name="doctor_id" required>
                        </div>
                        {{-- ====================================================== --}}
                        <div class="md:col-span-2">
                            <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan <span class="text-red-500">*</span></label>
                            <textarea name="chief_complaint" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tuliskan keluhan utama Anda..." required></textarea>
                        </div>
                        {{-- Hidden input untuk tanggal pendaftaran (hari ini) --}}
                        <input type="hidden" name="registration_date" value="{{ date('Y-m-d') }}"> 
                        {{-- Hidden input untuk schedule_id (diisi oleh JS) --}}
                        <input type="hidden" id="schedule_id" name="schedule_id" required> 
                    </div>
                </div>
            </form>
            @else
                <div class="text-center p-8"><p class="text-red-600 font-semibold">Data profil pasien tidak ditemukan.</p><p class="text-gray-600 mt-2">Harap lengkapi profil Anda terlebih dahulu untuk dapat mendaftar antrean.</p></div>
            @endif
        </div>
        <div class="flex justify-center items-center gap-4 p-6 border-t border-gray-200 flex-shrink-0">
            <button type="button" id="cancelModalBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Batal</button>
            <button type="submit" form="antrianForm" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">Daftar</button>
        </div>
   </div>
</div>
@endif 
@endpush

@push('scripts')
{{-- Script original dari Dashboard untuk modal & poli/dokter (akan dimodifikasi) --}}
{{-- Script untuk QR Code tidak dicopy karena tidak relevan di halaman jadwal --}}
{{-- Script untuk konfirmasi obat tidak dicopy karena tidak relevan di halaman jadwal --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const antrianModal = document.getElementById('antrianModal');
    
    // Pastikan modal ada sebelum menambahkan event listener
    if (antrianModal) {
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const antrianForm = document.getElementById('antrianForm');

        // Fungsi untuk menutup modal
        const closeModal = () => antrianModal.classList.add('hidden');
        
        // Fungsi untuk membuka modal (akan dipanggil oleh tombol baru)
        const openModal = () => antrianModal.classList.remove('hidden');

        // Event listener tombol Batal
        cancelModalBtn.addEventListener('click', closeModal);
        
        // Event listener klik di luar area modal untuk menutup
        antrianModal.addEventListener('click', (e) => { 
            if (e.target.id === 'antrianModal') closeModal(); 
        });
        
        // Event listener konfirmasi submit form
        antrianForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submit langsung
            // Cek NIK jika mendaftarkan keluarga
            const isFamilyInput = antrianForm.querySelector('[name="is_family"]');
            const isFamily = isFamilyInput ? isFamilyInput.value === 'true' : false; // Handle null case
            const nikInput = antrianForm.querySelector('[name="new_patient_nik"]');
            
            if (isFamily && nikInput && nikInput.value.length !== 16) {
                 Swal.fire({ icon: 'error', title: 'Oops...', text: 'NIK anggota keluarga harus terdiri dari 16 digit angka.' });
                 return; // Hentikan proses jika NIK tidak valid
            }

            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan semua data yang Anda masukkan sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Hijau
                cancelButtonColor: '#d33', // Merah
                confirmButtonText: 'Ya, daftarkan!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebelum submit
                    Swal.fire({ title: 'Memproses Pendaftaran...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    antrianForm.submit(); // Lanjutkan submit
                }
            });
        });
    } // End if (antrianModal)

    // ======================================================
    // == LOGIKA BARU UNTUK TOMBOL "BUAT ANTRIAN" DI CARD ==
    // ======================================================
    const scheduleButtons = document.querySelectorAll('.open-schedule-queue-modal-btn');
    const modalPoliDisplay = document.getElementById('poli_display');
    const modalPoliId = document.getElementById('poli_id');
    const modalDoctorDisplay = document.getElementById('doctor_display');
    const modalDoctorId = document.getElementById('doctor_id');
    const modalScheduleId = document.getElementById('schedule_id'); // Hidden input untuk schedule_id

    scheduleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Pastikan modal ada sebelum melanjutkan
            const antrianModal = document.getElementById('antrianModal');
            if (!antrianModal) {
                console.error("Modal #antrianModal tidak ditemukan.");
                return; 
            }

            // 1. Baca data dari tombol yang diklik
            const scheduleId = this.dataset.scheduleId;
            const poliId = this.dataset.poliId;
            const poliName = this.dataset.poliName;
            const doctorId = this.dataset.doctorId;
            const doctorName = this.dataset.doctorName;

            // 2. Isi form di dalam modal
            if (modalPoliDisplay && modalPoliId) {
                modalPoliDisplay.value = poliName;
                modalPoliId.value = poliId;
            } else {
                 console.error("Input Poli di modal tidak ditemukan.");
            }
            if (modalDoctorDisplay && modalDoctorId) {
                modalDoctorDisplay.value = doctorName;
                modalDoctorId.value = doctorId;
            } else {
                 console.error("Input Dokter di modal tidak ditemukan.");
            }
            if (modalScheduleId) {
                modalScheduleId.value = scheduleId; // Isi hidden input schedule_id
            } else {
                console.error("Input Schedule ID di modal tidak ditemukan.");
            }

            // Reset toggle keluarga ke "Diri Sendiri" setiap kali modal dibuka dari card
            const modalContent = document.getElementById('modalContent');
            if (modalContent && typeof Alpine !== 'undefined' && Alpine.$data(modalContent)) {
                 try {
                      Alpine.$data(modalContent).isFamily = false;
                      Alpine.$data(modalContent).customRelationship = false; // Reset juga custom relationship
                      Alpine.$data(modalContent).nikInput = ''; // Reset NIK input
                 } catch (e) {
                      console.error("Gagal reset state Alpine:", e);
                 }
            } else if (modalContent && !Alpine.$data(modalContent)) {
                 console.warn("Alpine.js belum terinisialisasi pada modalContent.");
                 // Mungkin perlu menunda sedikit atau memastikan Alpine dimuat sebelum script ini
            } else if (!modalContent) {
                 console.error("Elemen modalContent tidak ditemukan.");
            }
             
             // Reset textarea keluhan
             const keluhanTextarea = antrianModal.querySelector('[name="chief_complaint"]');
             if(keluhanTextarea) keluhanTextarea.value = '';


            // 3. Tampilkan modal
            antrianModal.classList.remove('hidden');
        });
    });
    // ======================================================

}); // End DOMContentLoaded
</script>
@endpush

