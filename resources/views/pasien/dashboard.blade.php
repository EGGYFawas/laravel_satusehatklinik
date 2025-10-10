@extends('layouts.pasien_layout')

@section('title', 'Dashboard Pasien')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Animasi untuk kartu panggilan */
        .blinking-warning {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {
                background-color: #fef3c7; /* yellow-100 */
                border-color: #fcd34d; /* yellow-300 */
            }
        }
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

    {{-- Konten Utama --}}
    <div class="flex flex-col items-center w-full">
        
        {{-- Tombol Ambil Antrean (hanya jika belum ada antrean) --}}
        @if(!$antreanBerobat)
            <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-6 text-center mb-8">
                <img src="{{ asset('assets/img/ambil_antrean.png') }}" alt="Antrean Online" class="w-32 h-32 mx-auto mb-4">
                <h3 class="text-xl font-bold text-gray-800">Antrean Online</h3>
                <p class="text-gray-500 mb-6">Daftar antrean berobat menjadi lebih mudah.</p>
                <button id="ambilAntrianBtn" class="bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-md">Ambil Antrian</button>
            </div>
        @endif

        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- ====================================================== -->
            <!-- == KARTU ANTRIAN BEROBAT (SUDAH DIRENVOASI TOTAL) == -->
            <!-- ====================================================== -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Berobat</h3>
                @if($antreanBerobat)
                    @php
                        // Logika untuk warna, status, dan estimasi
                        $statusText = '';
                        $bgColor = '';
                        $textColor = '';
                        $borderColor = '';
                        $pulseAnimation = '';

                        switch ($antreanBerobat->status) {
                            case 'MENUNGGU':
                                $statusText = 'Menunggu Check-In';
                                $bgColor = 'bg-blue-100'; $textColor = 'text-blue-800'; $borderColor = 'border-blue-300';
                                break;
                            case 'HADIR':
                                $statusText = 'Hadir (Siap Dipanggil)';
                                $bgColor = 'bg-indigo-100'; $textColor = 'text-indigo-800'; $borderColor = 'border-indigo-300';
                                break;
                            case 'DIPANGGIL':
                                $statusText = 'Giliran Anda!';
                                $bgColor = 'bg-yellow-100'; $textColor = 'text-yellow-800'; $borderColor = 'border-yellow-300';
                                $pulseAnimation = 'blinking-warning'; // Animasi peringatan
                                break;
                            default:
                                $statusText = ucwords(strtolower($antreanBerobat->status));
                                $bgColor = 'bg-gray-100'; $textColor = 'text-gray-800'; $borderColor = 'border-gray-300';
                                break;
                        }
                    @endphp

                    <div class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-4 text-center transition-all duration-500 {{ $pulseAnimation }}">
                        <p class="text-sm font-medium {{ $textColor }} mb-2">Poli {{ $antreanBerobat->poli->name }}</p>
                        <p class="text-6xl font-extrabold text-[#24306E]">{{ $antreanBerobat->queue_number }}</p>
                        <p class="text-lg {{ $textColor }} font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block">{{ $statusText }}</p>
                    </div>

                    <div class="mt-6 space-y-4">
                        {{-- Info Antrean Berjalan & Estimasi --}}
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                            <span class="font-semibold text-gray-700">Antrean Saat Ini:</span>
                            <span class="text-lg font-bold text-gray-900">{{ $antreanBerjalan->queue_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                            <span class="font-semibold text-gray-700">Estimasi Dipanggil:</span>
                            @php
                                $estimasi = '-';
                                if ($antreanBerjalan && $antreanBerobat->status !== 'DIPANGGIL') {
                                    $nomorAntreanPasien = (int) substr($antreanBerobat->queue_number, -3);
                                    $nomorAntreanBerjalan = (int) substr($antreanBerjalan->queue_number, -3);
                                    $selisih = $nomorAntreanPasien - $nomorAntreanBerjalan -1;
                                    if ($selisih >= 0) {
                                        $waktuTunggu = $selisih * 15;
                                        $estimasi = "sekitar {$waktuTunggu} menit lagi";
                                    } else {
                                        $estimasi = "Segera";
                                    }
                                } elseif ($antreanBerobat->status === 'DIPANGGIL') {
                                    $estimasi = "Sekarang!";
                                }
                            @endphp
                            <span class="text-lg font-bold text-gray-900">{{ $estimasi }}</span>
                        </div>
                        
                        {{-- Tombol Aksi Kontekstual --}}
                        <div class="pt-4 border-t">
                            @if($antreanBerobat->status == 'MENUNGGU')
                                <button id="checkInBtn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md text-base">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                    Saya Sudah Tiba, Lakukan Check-In
                                </button>
                            @elseif($antreanBerobat->status == 'HADIR')
                                <div class="w-full bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-lg text-center text-base">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Anda Sudah Melakukan Check-In
                                </div>
                             @elseif($antreanBerobat->status == 'DIPANGGIL')
                                <div class="w-full bg-yellow-400 text-yellow-900 font-bold py-3 px-6 rounded-lg text-center text-lg animate-pulse">
                                    SEGERA MASUK KE RUANG PEMERIKSAAN
                                </div>
                            @endif
                        </div>

                    </div>

                @else
                    <div class="text-center text-gray-500 py-8">
                        <p>Belum ada antrean dibuat hari ini.</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Apotik</h3>
                <div class="text-center text-gray-500 py-8">
                    <p>Belum ada resep obat terbaru.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Artikel Kesehatan --}}
    <div class="mt-12 w-full max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Artikel Kesehatan Terbaru</h2>
        @if(isset($articles) && $articles->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-2 transition-transform duration-300">
                        <img src="https://placehold.co/600x400/ABDCD6/24306E?text=Klinik+Sehat" alt="Gambar Artikel: {{ $article->title }}" class="w-full h-48 object-cover">
                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="font-bold text-lg mb-2 text-gray-800">{{ $article->title }}</h3>
                            <p class="text-gray-600 text-sm flex-grow">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                            <a href="#" class="text-sm text-[#24306E] font-semibold mt-4 self-start hover:underline">Baca Selengkapnya &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 py-8 bg-white rounded-xl shadow-lg"><p>Belum ada artikel kesehatan yang diterbitkan.</p></div>
        @endif
    </div>
@endsection

@push('modals')
    {{-- Modal Ambil Antrian (tidak diubah) --}}
    @if(!$antreanBerobat)
        <div id="antrianModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
             <div id="modalContent" class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[95vh] transform transition-all" 
                 x-data="{ isFamily: false, customRelationship: false, nikInput: '' }">
                 <div class="text-center p-6 border-b border-gray-200 flex-shrink-0">
                     <h3 class="text-2xl font-bold text-gray-800">Formulir Antrean Baru</h3>
                 </div>
                 <div class="overflow-y-auto p-8 flex-grow">
                     @if($patient)
                     <form id="antrianForm" action="{{ route('pasien.antrean.store') }}" method="POST">
                         @csrf
                         <div class="flex items-center justify-center mb-6">
                             <label class="text-sm font-medium text-gray-900">Daftarkan Diri Sendiri</label>
                             <button type="button" @click="isFamily = !isFamily" :class="isFamily ? 'bg-indigo-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 mx-3" role="switch">
                                 <span :class="isFamily ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                             </button>
                             <label class="text-sm font-medium text-gray-900">Daftarkan Anggota Keluarga</label>
                             <input type="hidden" name="is_family" x-bind:value="isFamily">
                         </div>
                         <div class="border-t border-gray-200 pt-6">
                             <div x-show="!isFamily" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                                 <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2">Data Pasien</h4>
                                  <div>
                                     <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                     <input type="text" id="nama" class="w-full p-2 bg-gray-100 border border-gray-300 rounded-md" value="{{ $patient->full_name ?? $user->full_name }}" readonly>
                                 </div>
                                 <div>
                                     <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                     <input type="text" id="nik" class="w-full p-2 bg-gray-100 border border-gray-300 rounded-md" value="{{ $patient->nik ?? 'NIK tidak ditemukan' }}" readonly>
                                 </div>
                             </div>
                             <div x-show="isFamily" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4 border-b border-gray-200 pb-4">
                                 <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2">Data Anggota Keluarga</h4>
                                 <div>
                                     <label for="new_patient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (ALL CAPS)</label>
                                     <input type="text" name="new_patient_name" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily"
                                            @input="event.target.value = event.target.value.toUpperCase()">
                                 </div>
                                 <div>
                                     <label for="new_patient_nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit)</label>
                                     <input type="text" name="new_patient_nik" class="w-full p-2 border border-gray-300 rounded-md" 
                                            :required="isFamily" maxlength="16" x-model="nikInput" 
                                            @input="nikInput = nikInput.replace(/\D/g, '')">
                                     <p x-show="isFamily && nikInput.length > 0 && nikInput.length !== 16" 
                                        class="text-xs text-red-600 mt-1">
                                        NIK harus terdiri dari 16 digit angka.
                                     </p>
                                 </div>
                                 <div>
                                     <label for="new_patient_dob" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                     <input type="date" name="new_patient_dob" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily">
                                 </div>
                                 <div>
                                     <label for="new_patient_gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                     <select name="new_patient_gender" class="w-full p-2 border border-gray-300 rounded-md" :required="isFamily">
                                         <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                         <option value="Laki-laki">Laki-laki</option>
                                         <option value="Perempuan">Perempuan</option>
                                     </select>
                                 </div>
                                 <div class="md:col-span-2">
                                      <label for="patient_relationship" class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga</label>
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
                             </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                  <div x-show="!isFamily" class="md:col-span-2">
                                     <h4 class="text-lg font-semibold text-gray-700 mb-2 border-t border-gray-200 pt-4">Detail Pendaftaran</h4>
                                  </div>
                                  <div x-show="isFamily" class="md:col-span-2">
                                     <h4 class="text-lg font-semibold text-gray-700 mb-2">Detail Pendaftaran</h4>
                                  </div>
                                  <div>
                                     <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Pilih Poli</label>
                                     <select id="poli" name="poli_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                                         <option value="" disabled selected>-- Silahkan Pilih Poli --</option>
                                         @foreach($polis as $poli)
                                             <option value="{{ $poli->id }}">{{ $poli->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div>
                                     <label for="doctor" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter</label>
                                     <select id="doctor" name="doctor_id" class="w-full p-2 border border-gray-300 rounded-md" required disabled>
                                         <option value="">-- Pilih Poli Terlebih Dahulu --</option>
                                     </select>
                                 </div>
                                 <div class="md:col-span-2">
                                     <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                                     <textarea name="chief_complaint" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tuliskan keluhan utama Anda..." required></textarea>
                                 </div>
                                 <input type="hidden" name="registration_date" value="{{ date('Y-m-d') }}">
                             </div>
                         </div>
                     </form>
                     @else
                         <div class="text-center p-8"><p class="text-red-600 font-semibold">Data profil pasien tidak ditemukan.</p><p class="text-gray-600 mt-2">Harap lengkapi profil Anda terlebih dahulu untuk dapat mendaftar antrean.</p></div>
                     @endif
                 </div>
                 <div class="flex justify-center items-center gap-4 p-6 border-t border-gray-200 flex-shrink-0">
                     <button type="button" id="cancelModalBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Batal</button>
                     <button type="submit" form="antrianForm" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">Simpan</button>
                 </div>
             </div>
        </div>
    @endif
    
    <!-- Modal untuk QR Code Scanner -->
    <div id="qrScannerModal" class="hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Pindai QR Code Check-In</h3>
            <p class="text-gray-600 mb-4 text-sm">Arahkan kamera ke QR Code yang tersedia di meja pendaftaran untuk mengonfirmasi kehadiran Anda.</p>
            <div id="qr-reader" class="w-full border rounded-lg overflow-hidden"></div>
            <p id="qr-result" class="mt-4 text-sm font-semibold text-green-600"></p>
            <button id="closeScannerBtn" class="mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg">Tutup</button>
        </div>
    </div>
@endpush

@push('scripts')
    {{-- CDN untuk library QR Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Logika untuk Modal Ambil Antrean (hanya jika tombolnya ada)
        const ambilAntrianBtn = document.getElementById('ambilAntrianBtn');
        if(ambilAntrianBtn) {
            // ... (kode modal antrean yang lama tetap di sini, tidak diubah) ...
            const antrianModal = document.getElementById('antrianModal');
            const cancelModalBtn = document.getElementById('cancelModalBtn');
            const antrianForm = document.getElementById('antrianForm');
            const poliSelect = document.getElementById('poli');
            const doctorSelect = document.getElementById('doctor');
            function openModal() { antrianModal.classList.remove('hidden'); }
            function closeModal() { antrianModal.classList.add('hidden'); }
            ambilAntrianBtn.addEventListener('click', openModal);
            cancelModalBtn.addEventListener('click', () => {
                Swal.fire({ title: 'Yakin membatalkan?', text: "Data yang sudah Anda isi akan dihapus.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Ya, batalkan!', cancelButtonText: 'Tidak'
                }).then((result) => { if (result.isConfirmed) { closeModal(); } });
            });
            antrianModal.addEventListener('click', (e) => { if (e.target.id === 'antrianModal') { closeModal(); }});
            antrianForm.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({ title: 'Apakah data sudah benar?', text: "Pastikan semua data yang Anda masukkan sudah benar.", icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', cancelButtonColor: '#d33', confirmButtonText: 'Ya, simpan!', cancelButtonText: 'Periksa Lagi'
                }).then((result) => { if (result.isConfirmed) { antrianForm.submit(); } });
            });
            poliSelect.addEventListener('change', function() {
                const poliId = this.value;
                doctorSelect.innerHTML = '<option value="">Memuat dokter...</option>';
                doctorSelect.disabled = true;
                if (poliId) {
                    fetch(`{{ url('/pasien/doctors-by-poli') }}/${poliId}`)
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
                    }).catch(error => { console.error('Fetch Error:', error); });
                }
            });
        }

        // ======================================================
        // == LOGIKA BARU UNTUK QR SCANNER CHECK-IN ==
        // ======================================================
        const checkInBtn = document.getElementById('checkInBtn');
        const qrScannerModal = document.getElementById('qrScannerModal');
        const closeScannerBtn = document.getElementById('closeScannerBtn');
        const qrResultEl = document.getElementById('qr-result');

        if (checkInBtn) {
            const html5QrCode = new Html5Qrcode("qr-reader");

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                qrResultEl.textContent = 'QR Code terdeteksi! Memproses...';
                
                // Hentikan pemindaian setelah berhasil
                html5QrCode.stop().then(ignore => {
                    // Redirect ke URL yang ada di QR code
                    window.location.href = decodedText;
                }).catch(err => {
                    console.error("Gagal menghentikan scanner.", err);
                });
            };

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            checkInBtn.addEventListener('click', () => {
                qrScannerModal.classList.remove('hidden');
                // Mulai pemindaian kamera
                html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Membuka Kamera',
                            text: 'Pastikan Anda memberikan izin akses kamera pada browser.',
                        });
                        console.error("Tidak dapat memulai scanner", err);
                    });
            });

            closeScannerBtn.addEventListener('click', () => {
                // Hentikan pemindaian jika sedang berjalan sebelum menutup modal
                if (html5QrCode.isScanning) {
                     html5QrCode.stop().then(ignore => {
                        qrScannerModal.classList.add('hidden');
                     }).catch(err => console.error("Gagal menghentikan scanner.", err));
                } else {
                    qrScannerModal.classList.add('hidden');
                }
            });
        }
    });
    </script>
@endpush

