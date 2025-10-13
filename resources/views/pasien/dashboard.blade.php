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

                    {{-- PENAMBAHAN ID AGAR BISA DIMANIPULASI JAVASCRIPT --}}
                    <div id="antrean-card-berobat" class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-4 text-center transition-all duration-500 {{ $pulseAnimation }}">
                        <p class="text-sm font-medium {{ $textColor }} mb-2">Poli {{ $antreanBerobat->poli->name }}</p>
                        <p class="text-6xl font-extrabold text-[#24306E]">{{ $antreanBerobat->queue_number }}</p>
                        <p id="status-text-berobat" class="text-lg {{ $textColor }} font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block">{{ $statusText }}</p>
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
                        {{-- PENAMBAHAN ID AGAR BISA DIMANIPULASI JAVASCRIPT --}}
                        <div id="action-button-container" class="pt-4 border-t">
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
                        {{-- Form untuk diri sendiri dan keluarga --}}
                        {{-- ... (Konten form tidak diubah) ... --}}
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

        // ======================================================================
        // == LOGIKA BARU UNTUK QR SCANNER CHECK-IN MENGGUNAKAN AJAX (FETCH) ==
        // ======================================================================
        const checkInBtn = document.getElementById('checkInBtn');
        const qrScannerModal = document.getElementById('qrScannerModal');
        const closeScannerBtn = document.getElementById('closeScannerBtn');
        const qrResultEl = document.getElementById('qr-result');

        if (checkInBtn) {
            const html5QrCode = new Html5Qrcode("qr-reader");

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                // Hentikan pemindaian setelah berhasil
                html5QrCode.stop().catch(err => console.error("Gagal menghentikan scanner.", err));
                
                // Tampilkan loading
                qrResultEl.textContent = 'QR Code terdeteksi! Memproses check-in...';
                Swal.fire({
                    title: 'Memproses Check-In',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // **PERUBAHAN UTAMA: Kirim data ke server menggunakan Fetch API**
                // Catatan: decodedText HARUSNYA hanya berisi UUID
                fetch(`{{ url('/pasien/check-in') }}/${decodedText}`, {
                    method: 'GET', // Method disesuaikan dengan route Anda
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Penting untuk keamanan
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Handle error HTTP seperti 404 atau 500
                        throw new Error('Server merespon dengan error!');
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.close(); // Tutup loading Swal
                    qrScannerModal.classList.add('hidden'); // Tutup modal scanner

                    if (data.success) {
                        // Tampilkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Check-In Berhasil!',
                            text: data.message,
                        });
                        
                        // **PERBARUI TAMPILAN SECARA DINAMIS TANPA RELOAD**
                        // 1. Ubah teks status
                        document.getElementById('status-text-berobat').textContent = 'Hadir (Siap Dipanggil)';
                        // 2. Ubah warna kartu
                        const antreanCard = document.getElementById('antrean-card-berobat');
                        antreanCard.classList.remove('bg-blue-100', 'border-blue-300', 'text-blue-800');
                        antreanCard.classList.add('bg-indigo-100', 'border-indigo-300', 'text-indigo-800');
                        // 3. Ganti tombol check-in dengan pesan konfirmasi
                        const actionContainer = document.getElementById('action-button-container');
                        actionContainer.innerHTML = `
                            <div class="w-full bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-lg text-center text-base">
                                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Anda Sudah Melakukan Check-In
                            </div>`;

                    } else {
                        // Tampilkan notifikasi error dari server
                        Swal.fire({
                            icon: 'error',
                            title: 'Check-In Gagal',
                            text: data.message || 'Terjadi kesalahan saat check-in.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Proses Gagal',
                        text: 'Tidak dapat memproses permintaan. Pastikan QR code benar dan coba lagi.',
                    });
                });
            };

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            checkInBtn.addEventListener('click', () => {
                qrScannerModal.classList.remove('hidden');
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
