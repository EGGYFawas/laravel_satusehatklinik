@extends('layouts.pasien_layout')

@section('title', 'Dashboard Pasien')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Animasi untuk kartu panggilan */
<<<<<<< HEAD
        .blinking-call { animation: blinker-call 1.5s ease-in-out infinite; }
        @keyframes blinker-call {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); border-color: #34d399; }
            50% { box-shadow: 0 0 20px 10px rgba(16, 185, 129, 0.2); border-color: #059669; }
=======
        .blinking-warning {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {
                background-color: #fef3c7; /* yellow-100 */
                border-color: #fcd34d; /* yellow-300 */
            }
>>>>>>> parent of 110fbee (update magang baru)
        }
        /* Efek lubang tiket (Boarding Pass) */
        .ticket-hole-left { clip-path: circle(12px at left center); }
        .ticket-hole-right { clip-path: circle(12px at right center); }
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

        @php
            $hasActiveProcess = ($antreanBerobat && !in_array($antreanBerobat->status, ['SELESAI', 'BATAL'])) ||
                                ($antreanApotek && !in_array($antreanApotek->status, ['DITERIMA_PASIEN', 'BATAL']));
        @endphp

<<<<<<< HEAD
        <!-- WIDGET CEK BPJS PASIEN -->
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-sm border border-green-200 p-6 mb-8 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-green-50 rounded-full opacity-50 pointer-events-none"></div>
            
            <div class="relative z-10 flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-green-100 p-2 rounded-lg text-green-600"><i class="fas fa-id-card"></i></div>
                    <h3 class="text-xl font-bold text-gray-800">Cek Status JKN / BPJS Anda</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Pastikan kartu BPJS Anda dalam status <strong class="text-green-600">AKTIF</strong> untuk dapat mendaftar menggunakan jalur BPJS Kesehatan.</p>
                <button type="button" id="btn_patient_cek_bpjs" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                    <i class="fas fa-search mr-2"></i> Cek Status Sekarang
                </button>
            </div>

            <!-- Hasil Cek BPJS (Hidden by default) -->
            <div id="patient_bpjs_result" class="hidden w-full md:w-80 bg-gray-50 rounded-xl p-4 border border-gray-200 relative z-10">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-gray-500 uppercase">Hasil Pengecekan</span>
                    <span id="patient_bpjs_badge" class="text-[10px] font-bold px-2 py-1 rounded-md"></span>
                </div>
                <p id="patient_bpjs_nama" class="font-bold text-gray-800 mb-1">-</p>
                <p class="text-xs text-gray-500 mb-1">No. Kartu: <strong id="patient_bpjs_nokartu" class="text-gray-700">-</strong></p>
                <p class="text-xs text-gray-500">Jenis: <strong id="patient_bpjs_jenis" class="text-gray-700">-</strong></p>
            </div>
        </div>

        <!-- Card Daftar Antrean (Jika Tidak Ada Proses Aktif) -->
=======
        <!-- card daftar antrian -->
>>>>>>> parent of 110fbee (update magang baru)
        @if(!$hasActiveProcess)
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-8 text-center mb-8 border border-gray-100 transform transition duration-500 hover:scale-105">
                <img src="{{ asset('assets/img/ambil_antrean.png') }}" alt="Antrean Online" class="w-40 h-40 mx-auto mb-6 drop-shadow-md">
                <h3 class="text-2xl font-black text-[#24306E] mb-2">Ambil Antrean Online</h3>
                <p class="text-gray-500 mb-8 px-4">Daftar dari mana saja, pantau dari HP Anda. Tidak perlu lagi berdesakan di ruang tunggu.</p>
                <button id="ambilAntrianBtn" class="w-full bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg text-lg flex justify-center items-center gap-2">
                    <i class="fas fa-ticket-alt"></i> Ambil Tiket Sekarang
                </button>
            </div>
        @endif

        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8">
<<<<<<< HEAD
            
=======

>>>>>>> parent of 110fbee (update magang baru)
            <!-- ====================================================== -->
            <!-- == KARTU ANTRIAN BEROBAT (DIGITAL BOARDING PASS UI) == -->
            <!-- ====================================================== -->
            <div class="flex flex-col h-full">
                <h3 class="text-lg font-bold text-white bg-[#24306E] px-6 py-3 rounded-t-2xl shadow-md w-max ml-4 relative top-2">
                    <i class="fas fa-stethoscope mr-2"></i> Kunjungan Dokter
                </h3>
                
                @php
                    // Cek apakah ada antrean yang BENAR-BENAR aktif (belum selesai/batal)
                    $isAntreanAktif = $antreanBerobat && in_array($antreanBerobat->status, ['MENUNGGU', 'HADIR', 'DIPANGGIL']);
                    
                    // Siapkan data riwayat (Prioritas 1: Antrean hari ini yang sudah selesai, Prioritas 2: Riwayat lama)
                    $dataRiwayat = null;
                    if ($antreanBerobat && $antreanBerobat->status == 'SELESAI') {
                        $dataRiwayat = $antreanBerobat;
                    } elseif ($riwayatBerobatTerakhir) {
                        $dataRiwayat = $riwayatBerobatTerakhir;
                    }
                @endphp

                @if($isAntreanAktif)
                    @php
<<<<<<< HEAD
                        // Logika Estimasi Waktu & Psikologi Warna (HANYA UNTUK YANG AKTIF)
                        $estimasi = '-';
                        $waktuTunggu = 0;
                        $selisih = 0;
                        
                        $ticketTheme = 'from-blue-500 to-indigo-600'; 
                        $bgCard = 'bg-blue-50/50';
                        $textHighlight = 'text-blue-700';
                        $pulseAnim = '';

                        if (in_array($antreanBerobat->status, ['MENUNGGU', 'HADIR'])) {
                            if ($antreanBerjalan) {
                                $noPasien = (int) preg_replace('/[^0-9]/', '', $antreanBerobat->queue_number);
                                $noJalan = (int) preg_replace('/[^0-9]/', '', $antreanBerjalan->queue_number);
                                $selisih = $noPasien - $noJalan;
                                
                                if ($selisih > 0) {
                                    $waktuTunggu = ($selisih - 1) * 20; 
                                    $estimasi = $waktuTunggu > 0 ? "± {$waktuTunggu} Menit" : "Giliran Berikutnya!";
                                    if ($selisih <= 3) {
                                        $ticketTheme = 'from-amber-400 to-orange-500';
                                        $bgCard = 'bg-orange-50/80';
                                        $textHighlight = 'text-orange-700';
                                    }
                                } else {
                                    $estimasi = "Segera";
                                }
                            } else {
                                $estimasi = "Poli Belum Mulai";
                            }
                        } elseif ($antreanBerobat->status == 'DIPANGGIL') {
                            $estimasi = "SEKARANG!";
                            $ticketTheme = 'from-emerald-500 to-teal-600';
                            $bgCard = 'bg-emerald-50/80';
                            $textHighlight = 'text-emerald-700';
                            $pulseAnim = 'blinking-call border-2 border-emerald-400';
                        }
                    @endphp

                    <div class="bg-white rounded-2xl shadow-xl flex-grow flex flex-col relative overflow-hidden {{ $pulseAnim }}">
                        <div class="h-3 w-full bg-gradient-to-r {{ $ticketTheme }}"></div>
                        
                        <div class="p-8 {{ $bgCard }} text-center flex flex-col items-center justify-center relative">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Tiket Antrean Anda</span>
                            <h2 class="text-7xl font-black text-gray-800 tracking-tighter drop-shadow-sm mb-2">
                                {{ $antreanBerobat->queue_number }}
                            </h2>
                            <p class="text-md font-semibold text-gray-700 mb-4">Poli {{ $antreanBerobat->poli->name }}</p>
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold tracking-wider border shadow-sm {{ $antreanBerobat->payment_method == 'BPJS' ? 'border-green-400 bg-green-100 text-green-700' : 'border-blue-400 bg-blue-100 text-blue-700' }}">
                                JALUR: {{ strtoupper($antreanBerobat->payment_method ?? 'UMUM') }}
                            </span>
                        </div>

                        <div class="relative flex items-center justify-between bg-white h-8">
                            <div class="w-4 h-8 bg-gray-100 rounded-r-full border-y border-r border-gray-200"></div>
                            <div class="flex-1 border-t-2 border-dashed border-gray-300 mx-2"></div>
                            <div class="w-4 h-8 bg-gray-100 rounded-l-full border-y border-l border-gray-200"></div>
                        </div>
=======
                        $statusText = ''; $bgColor = ''; $textColor = ''; $borderColor = ''; $pulseAnimation = '';
                        switch ($antreanBerobat->status) {
                            case 'MENUNGGU':
                                $statusText = 'Menunggu Check-In'; $bgColor = 'bg-blue-100'; $textColor = 'text-blue-800'; $borderColor = 'border-blue-300'; break;
                            case 'HADIR':
                                $statusText = 'Hadir (Siap Dipanggil)'; $bgColor = 'bg-indigo-100'; $textColor = 'text-indigo-800'; $borderColor = 'border-indigo-300'; break;
                            case 'DIPANGGIL':
                                $statusText = 'Giliran Anda!'; $bgColor = 'bg-yellow-100'; $textColor = 'text-yellow-800'; $borderColor = 'border-yellow-300'; $pulseAnimation = 'blinking-warning'; break;
                            case 'SELESAI':
                                $statusText = 'Pemeriksaan Selesai'; $bgColor = 'bg-green-100'; $textColor = 'text-green-800'; $borderColor = 'border-green-300'; break;
                            default:
                                $statusText = ucwords(strtolower($antreanBerobat->status)); $bgColor = 'bg-gray-100'; $textColor = 'text-gray-800'; $borderColor = 'border-gray-300'; break;
                        }
                    @endphp

                    <div id="antrean-card-berobat" class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-4 text-center transition-all duration-500 {{ $pulseAnimation }}">
                        <p class="text-sm font-medium {{ $textColor }} mb-2">Poli {{ $antreanBerobat->poli->name }}</p>
                        <p class="text-6xl font-extrabold text-[#24306E]">{{ $antreanBerobat->queue_number }}</p>
                        <p id="status-text-berobat" class="text-lg {{ $textColor }} font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block">{{ $statusText }}</p>
                    </div>

                    <div class="mt-6 space-y-4">
                        @if(in_array($antreanBerobat->status, ['MENUNGGU', 'HADIR', 'DIPANGGIL']))
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span class="font-semibold text-gray-700">Antrean Saat Ini:</span>
                                <span class="text-lg font-bold text-gray-900">{{ $antreanBerjalan->queue_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span class="font-semibold text-gray-700">Estimasi Dipanggil:</span>
                                <span class="text-lg font-bold text-gray-900">
                                    @php
                                        $estimasi = '-';
                                        if ($antreanBerjalan) {
                                            $nomorAntreanPasien = (int) substr($antreanBerobat->queue_number, -3);
                                            $nomorAntreanBerjalan = (int) substr($antreanBerjalan->queue_number, -3);
                                            $selisih = $nomorAntreanPasien - $nomorAntreanBerjalan;
                                            if ($selisih > 0) {
                                                $waktuTunggu = ($selisih - 1) * 15;
                                                $estimasi = "sekitar {$waktuTunggu} menit";
                                            } else { $estimasi = "Segera"; }
                                        } elseif ($antreanBerobat->status == 'HADIR') { $estimasi = "Menunggu Dipanggil ke Ruangan"; }
                                    @endphp
                                    {{ $estimasi }}
                                </span>
                            </div>
                        @elseif($antreanBerobat->status == 'SELESAI')
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span class="font-semibold text-gray-700">Selesai Pada:</span>
                                <span class="text-lg font-bold text-gray-900">
                                    @if($antreanBerobat->finish_time)
                                        {{ $antreanBerobat->finish_time->format('H:i') }} WIB
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            @if($antreanApotek && $antreanApotek->status != 'DITERIMA_PASIEN')
                            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md">
                                <p class="font-bold">Pemeriksaan telah selesai.</p>
                                <p class="text-sm">Silakan lanjutkan ke proses antrean apotek dan selesaikan hingga obat diterima. Terima kasih.</p>
                            </div>
                            @endif
                        @endif
>>>>>>> parent of 110fbee (update magang baru)

                        <div class="p-6 bg-white grid grid-cols-2 gap-4 divide-x divide-gray-200 flex-grow">
                            <div class="text-center flex flex-col justify-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Sedang Dilayani</span>
                                <p class="text-3xl font-black text-gray-800">{{ $antreanBerjalan->queue_number ?? '-' }}</p>
                            </div>
                            <div class="text-center flex flex-col justify-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Estimasi Panggilan</span>
                                <p class="text-xl font-bold {{ $textHighlight }} leading-tight">{{ $estimasi }}</p>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 border-t border-gray-100">
                            @if($antreanBerobat->status == 'MENUNGGU')
                                <button id="checkInBtn" class="w-full bg-[#24306E] hover:bg-blue-800 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg transition flex justify-center items-center gap-2 text-sm uppercase tracking-wide">
                                    <i class="fas fa-qrcode text-lg"></i> Saya Sudah di Klinik (Check-In)
                                </button>
                            @elseif($antreanBerobat->status == 'HADIR')
                                <div class="w-full bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold py-3 px-6 rounded-xl text-center text-sm uppercase tracking-wide flex justify-center items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Terverifikasi (Menunggu)
                                </div>
                            @elseif($antreanBerobat->status == 'DIPANGGIL')
                                <div class="w-full bg-emerald-500 text-white font-black py-4 px-6 rounded-xl text-center text-lg uppercase tracking-widest shadow-lg animate-bounce flex justify-center items-center gap-2">
                                    <i class="fas fa-volume-up"></i> MASUK KE RUANGAN
                                </div>
                            @endif
                        </div>
                    </div>
<<<<<<< HEAD

                @elseif($dataRiwayat)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex-grow p-8 flex flex-col justify-center relative overflow-hidden group hover:border-blue-300 transition-colors">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>
                        
                        <div class="flex flex-col items-center text-center mb-6 relative z-10">
                            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-3xl mb-3 shadow-inner">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Pemeriksaan Selesai</h4>
                            <p class="text-sm text-gray-500 mt-1">Data riwayat kunjungan terakhir Anda</p>
                        </div>

                        <div class="w-full bg-gray-50 rounded-xl p-5 text-sm space-y-4 border border-gray-100 relative z-10">
                            <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                <div class="flex items-center gap-2 text-gray-500 font-medium">
                                    <i class="far fa-calendar-check w-4"></i> Tanggal
                                </div>
                                <span class="font-bold text-gray-800">
                                    {{ $dataRiwayat->finish_time ? $dataRiwayat->finish_time->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : ($dataRiwayat->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y')) }}
                                </span>
=======
                @elseif($riwayatBerobatTerakhir)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <p class="font-semibold text-gray-700">Kunjungan Terakhir Anda</p>

                        @if($riwayatBerobatTerakhir->finish_time)
                            <p class="text-2xl font-bold text-gray-800 mt-2">
                                {{ $riwayatBerobatTerakhir->finish_time->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                            </p>
                            <div class="mt-4 text-left space-y-2 text-sm">
                                <p>
                                    <span class="font-semibold w-24 inline-block">Selesai Pukul</span>:
                                    {{ $riwayatBerobatTerakhir->finish_time->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                                </p>
                                <p><span class="font-semibold w-24 inline-block">Poli</span>: {{ $riwayatBerobatTerakhir->poli->name }}</p>
                                <p><span class="font-semibold w-24 inline-block">Dokter</span>: {{ $riwayatBerobatTerakhir->doctor->user->full_name ?? 'N/A' }}</p>
                            </div>
                        @else
                            <p class="text-lg text-gray-600 mt-2">
                                Data waktu kunjungan tidak lengkap.
                            </p>
                             <div class="mt-4 text-left space-y-2 text-sm">
                                <p><span class="font-semibold w-24 inline-block">Poli</span>: {{ $riwayatBerobatTerakhir->poli->name }}</p>
                                <p><span class="font-semibold w-24 inline-block">Dokter</span>: {{ $riwayatBerobatTerakhir->doctor->user->full_name ?? 'N/A' }}</p>
>>>>>>> parent of 110fbee (update magang baru)
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                <div class="flex items-center gap-2 text-gray-500 font-medium">
                                    <i class="fas fa-user-md w-4"></i> Dokter
                                </div>
                                <span class="font-bold text-gray-800">{{ $dataRiwayat->doctor->user->full_name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2 text-gray-500 font-medium">
                                    <i class="fas fa-clinic-medical w-4"></i> Poli / Jalur
                                </div>
                                <span class="font-bold text-gray-800">{{ $dataRiwayat->poli->name ?? '-' }} <span class="text-gray-400 font-normal">({{ strtoupper($dataRiwayat->payment_method ?? 'UMUM') }})</span></span>
                            </div>
                        </div>

                        <a href="{{ route('pasien.riwayat.show', $dataRiwayat->patient_id) }}" class="mt-6 w-full block bg-blue-50 hover:bg-blue-100 text-[#24306E] border border-blue-200 font-bold py-3.5 rounded-xl transition text-center shadow-sm relative z-10">
                            <i class="fas fa-folder-open mr-2"></i> Lihat Rekam Medis
                        </a>
                    </div>
                @else
<<<<<<< HEAD
                    <div class="bg-white rounded-2xl shadow-xl flex-grow border border-gray-100 flex flex-col items-center justify-center p-8 text-center text-gray-400">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-bed text-3xl text-gray-300"></i>
                        </div>
                        <h4 class="text-gray-600 font-bold mb-1">Belum Ada Kunjungan</h4>
                        <p class="text-sm">Anda belum memiliki antrean aktif maupun riwayat berobat sebelumnya.</p>
=======
                    <div class="text-center text-gray-500 py-8">
                        <p>Belum ada antrean berobat atau riwayat kunjungan.</p>
>>>>>>> parent of 110fbee (update magang baru)
                    </div>
                @endif
            </div>

            <!-- ====================================================== -->
            <!-- == KARTU ANTRIAN APOTEK (DISESUAIKAN DENGAN TEMA) == -->
            <!-- ====================================================== -->
<<<<<<< HEAD
            <div class="flex flex-col h-full">
                <h3 class="text-lg font-bold text-white bg-purple-700 px-6 py-3 rounded-t-2xl shadow-md w-max ml-4 relative top-2">
                    <i class="fas fa-pills mr-2"></i> Pengambilan Obat
                </h3>
                
                <div class="bg-white rounded-2xl shadow-xl flex-grow border border-gray-100 flex flex-col overflow-hidden">
                    @if($antreanApotek)
                        @php
                            $statusTextApotek = ''; $bgColorApotek = ''; $textColorApotek = ''; $pulseAnimationApotek = '';
                            switch ($antreanApotek->status) {
                                case 'DALAM_ANTREAN': $statusTextApotek = 'Dalam Antrean Apotek'; $bgColorApotek = 'bg-cyan-50'; $textColorApotek = 'text-cyan-700'; break;
                                case 'SEDANG_DIRACIK': $statusTextApotek = 'Obat Sedang Disiapkan'; $bgColorApotek = 'bg-orange-50'; $textColorApotek = 'text-orange-700'; break;
                                case 'SIAP_DIAMBIL': $statusTextApotek = 'Obat Siap Diambil!'; $bgColorApotek = 'bg-yellow-50'; $textColorApotek = 'text-yellow-700'; $pulseAnimationApotek = 'blinking-warning border-2 border-yellow-400'; break;
                                case 'DISERAHKAN': $statusTextApotek = 'Menunggu Konfirmasi Anda'; $bgColorApotek = 'bg-purple-50'; $textColorApotek = 'text-purple-700'; break;
                                case 'DITERIMA_PASIEN': $statusTextApotek = 'Proses Selesai'; $bgColorApotek = 'bg-green-50'; $textColorApotek = 'text-green-700'; break;
                                case 'BATAL': $statusTextApotek = 'Dibatalkan'; $bgColorApotek = 'bg-red-50'; $textColorApotek = 'text-red-700'; break;
                                default: $statusTextApotek = 'Menunggu Proses'; $bgColorApotek = 'bg-gray-50'; $textColorApotek = 'text-gray-700'; break;
                            }
                        @endphp
=======
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Apotek</h3>
                @if($antreanApotek)
                    @php
                        $statusTextApotek = ''; $bgColorApotek = ''; $textColorApotek = ''; $borderColorApotek = ''; $pulseAnimationApotek = '';
                        switch ($antreanApotek->status) {
                            case 'DALAM_ANTREAN':
                                $statusTextApotek = 'Dalam Antrean Apotek'; $bgColorApotek = 'bg-cyan-100'; $textColorApotek = 'text-cyan-800'; $borderColorApotek = 'border-cyan-300'; break;
                            case 'SEDANG_DIRACIK':
                                $statusTextApotek = 'Obat Sedang Disiapkan'; $bgColorApotek = 'bg-orange-100'; $textColorApotek = 'text-orange-800'; $borderColorApotek = 'border-orange-300'; break;
                            case 'SIAP_DIAMBIL':
                                $statusTextApotek = 'Obat Siap Diambil!'; $bgColorApotek = 'bg-yellow-100'; $textColorApotek = 'text-yellow-800'; $borderColorApotek = 'border-yellow-300'; $pulseAnimationApotek = 'blinking-warning'; break;
                            case 'DISERAHKAN':
                                $statusTextApotek = 'Menunggu Konfirmasi Anda'; $bgColorApotek = 'bg-purple-100'; $textColorApotek = 'text-purple-800'; $borderColorApotek = 'border-purple-300'; break;
                            case 'DITERIMA_PASIEN':
                                $statusTextApotek = 'Proses Selesai'; $bgColorApotek = 'bg-green-100'; $textColorApotek = 'text-green-800'; $borderColorApotek = 'border-green-300'; break;
                            case 'BATAL':
                                $statusTextApotek = 'Dibatalkan'; $bgColorApotek = 'bg-red-100'; $textColorApotek = 'text-red-800'; $borderColorApotek = 'border-red-300'; break;
                            default:
                                $statusTextApotek = 'Menunggu Proses'; $bgColorApotek = 'bg-gray-100'; $textColorApotek = 'text-gray-800'; $borderColorApotek = 'border-gray-300'; break;
                        }
                    @endphp
>>>>>>> parent of 110fbee (update magang baru)

                        <div class="p-8 {{ $bgColorApotek }} text-center flex flex-col items-center justify-center border-b border-gray-100 {{ $pulseAnimationApotek }}">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Nomor Resep</span>
                            <p class="text-6xl font-extrabold text-purple-800 drop-shadow-sm">{{ $antreanApotek->pharmacy_queue_number }}</p>
                            <span class="inline-block mt-4 px-4 py-1.5 bg-white border border-gray-200 rounded-full text-sm font-bold {{ $textColorApotek }} shadow-sm">
                                {{ $statusTextApotek }}
                            </span>
                        </div>

                        <div class="p-6 flex-grow flex flex-col justify-center space-y-4">
                            @if(in_array($antreanApotek->status, ['DALAM_ANTREAN', 'SEDANG_DIRACIK']))
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                    <span class="text-sm font-semibold text-gray-500">Antrean Diproses</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $antreanApotekBerjalan->pharmacy_queue_number ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                    <span class="text-sm font-semibold text-gray-500">Estimasi Selesai</span>
                                    <span class="text-lg font-bold text-gray-800">
                                         @php
                                             $estimasiApotek = '-';
                                             if ($antreanApotek->status === 'SEDANG_DIRACIK') { $estimasiApotek = "Segera"; }
                                             elseif ($antreanApotek->status === 'DALAM_ANTREAN') {
                                                  $waktuTungguApotek = ($jumlahAntreanApotekSebelumnya) * 10;
                                                  $estimasiApotek = $waktuTungguApotek > 0 ? "± {$waktuTungguApotek} Menit" : "Segera";
                                             }
                                         @endphp
                                         {{ $estimasiApotek }}
                                    </span>
                                </div>
                            @endif

                            @if($antreanApotek->status == 'SIAP_DIAMBIL')
<<<<<<< HEAD
                                @if(isset($tagihanObat) && $tagihanObat->payment_status == 'pending')
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                        <p class="text-sm text-yellow-800 font-bold mb-1"><i class="fas fa-exclamation-circle mr-1"></i> Tagihan belum dibayar</p>
                                        <p class="text-xs text-yellow-700">Silakan lakukan pembayaran kasir agar obat dapat diserahkan.</p>
=======

                                {{-- FITUR BILLING/TAGIHAN --}}
                                @if(isset($tagihanObat) && $tagihanObat->payment_status == 'pending')
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 text-left">
                                        <div class="flex">
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700 font-bold">
                                                    Tagihan obat belum dibayar.
                                                </p>
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    Silakan lakukan pembayaran agar obat dapat diserahkan.
                                                </p>
                                            </div>
                                        </div>
>>>>>>> parent of 110fbee (update magang baru)
                                    </div>
                                    <a href="{{ route('pasien.billing.index') }}" class="mt-2 block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl text-center shadow-lg transition">
                                        <i class="fas fa-wallet mr-2"></i> Bayar Tagihan Sekarang
                                    </a>
                                @else
<<<<<<< HEAD
                                    <div class="w-full bg-green-500 text-white font-bold py-4 px-6 rounded-xl text-center text-sm uppercase tracking-wider animate-pulse shadow-md">
                                        MENUJU LOKET APOTEK
=======
                                    {{-- Jika sudah lunas atau tidak ada tagihan --}}
                                    <div class="w-full bg-green-500 text-white font-bold py-3 px-6 rounded-lg text-center text-lg animate-pulse">
                                        SEGERA MENUJU LOKET APOTEK
>>>>>>> parent of 110fbee (update magang baru)
                                        @if(isset($tagihanObat) && $tagihanObat->payment_status == 'paid')
                                            <span class="block text-xs font-normal mt-1 opacity-80">(STATUS: LUNAS)</span>
                                        @endif
                                    </div>
                                @endif

                            @elseif($antreanApotek->status == 'DISERAHKAN')
                                 <form action="{{ route('pasien.antrean.apotek.konfirmasi', $antreanApotek->id) }}" method="POST" id="konfirmasiObatForm" class="mt-auto">
                                     @csrf
                                     <button type="button" id="konfirmasiObatBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg text-sm uppercase tracking-wide transition">
                                         <i class="fas fa-box-open mr-2"></i> Konfirmasi Obat Diterima
                                     </button>
                                 </form>
                             @elseif($antreanApotek->status == 'DITERIMA_PASIEN')
                                 <div class="bg-green-50 border border-green-200 p-4 rounded-xl flex items-start gap-3 mt-auto">
                                     <i class="fas fa-check-circle text-green-500 text-2xl mt-1"></i>
                                     <div>
                                         <p class="font-bold text-green-800">Selesai</p>
                                         <p class="text-xs text-green-700 mt-1">Terima kasih. Jangan lupa diminum sesuai anjuran dokter. Semoga lekas sembuh!</p>
                                     </div>
                                 </div>
                             @endif
                        </div>
                    @elseif($antreanBerobat && $antreanBerobat->status == 'SELESAI')
                        <div class="flex flex-col justify-center items-center h-full p-8 text-center text-gray-400">
                            <i class="fas fa-prescription-bottle text-4xl mb-3 text-gray-200"></i>
                            <p>Tidak ada resep obat untuk kunjungan kali ini.</p>
                        </div>
                    @else
                        <div class="flex flex-col justify-center items-center h-full p-8 text-center text-gray-400">
                            <i class="fas fa-hourglass-half text-4xl mb-3 text-gray-200"></i>
                            <p>Nomor antrean apotek akan muncul di sini setelah pemeriksaan selesai.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Artikel Kesehatan --}}
    <div class="mt-12 w-full max-w-5xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm shadow-sm rounded-xl p-6 mb-8 border border-gray-100 relative z-10">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Artikel Kesehatan Terbaru</h2>
                    <p class="text-sm text-gray-500 mt-1">Informasi terkini untuk menunjang kesehatan Anda.</p>
                </div>
                <a href="{{ route('pasien.artikel.index') }}" class="text-sm text-[#24306E] hover:text-blue-800 font-semibold mb-1 flex items-center transition-colors">
                    Lihat Semua <span class="ml-1 text-lg leading-none">&rarr;</span>
                </a>
            </div>
        </div>
        @if(isset($articles) && $articles->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-2 transition-transform duration-300 border border-gray-100 group">
                        <a href="{{ route('pasien.artikel.show', $article->slug) }}" class="block overflow-hidden h-48">
                            <img src="{{ $article->image_url ? asset('storage/' . $article->image_url) : 'https://placehold.co/600x400/ABDCD6/24306E?text=Klinik+Sehat' }}"
                                 alt="Gambar Artikel: {{ $article->title }}"
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                        </a>

                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2">
                                <a href="{{ route('pasien.artikel.show', $article->slug) }}" class="hover:text-[#24306E] transition">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm flex-grow line-clamp-3">
                                {{ Str::limit(strip_tags($article->content), 100) }}
                            </p>
                            <a href="{{ route('pasien.artikel.show', $article->slug) }}" class="text-sm text-[#24306E] font-semibold mt-4 self-start hover:underline flex items-center">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 py-12 bg-white rounded-xl shadow-lg border border-dashed border-gray-200">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6M7 8h6"></path></svg>
                <p>Belum ada artikel kesehatan yang diterbitkan.</p>
            </div>
        @endif
    </div>
@endsection

@push('modals')
    <!-- Modal Formulir Antrean -->
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
                    <div class="border-t border-gray-200 pt-6">
                        <div x-show="!isFamily" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
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

                        <div x-show="isFamily" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4 border-b border-gray-200 pb-4">
                            <!-- Konten form keluarga disembunyikan -->
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2 pt-4 border-t border-gray-200">Detail Pendaftaran</h4>
<<<<<<< HEAD
                            
                             <!-- PILIHAN CARA BAYAR -->
                             <div class="md:col-span-2 bg-blue-50 border border-blue-200 p-4 rounded-lg mb-2">
                                <label for="payment_method" class="block text-sm font-bold text-blue-900 mb-2">Pilih Jalur Pendaftaran (Cara Bayar) <span class="text-red-500">*</span></label>
                                <select id="payment_method" name="payment_method" class="w-full p-2.5 border border-blue-300 rounded-md font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="Umum" selected>JALUR UMUM (Pribadi)</option>
                                    <option value="BPJS">JALUR BPJS KESEHATAN (JKN)</option>
                                </select>
                                <p class="text-xs text-blue-700 mt-2">*Jika memilih BPJS, pastikan Anda telah mengecek status kepesertaan aktif di dashboard.</p>
                             </div>

=======
>>>>>>> parent of 110fbee (update magang baru)
                             <div>
                                <label for="poli" class="block text-sm font-medium text-gray-700 mb-1">Pilih Poli <span class="text-red-500">*</span></label>
                                <select id="poli" name="poli_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                                    <option value="" disabled selected>-- Silahkan Pilih Poli --</option>
                                    @foreach($polis as $poli)
                                        <option value="{{ $poli->id }}">{{ $poli->name }}</option>
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
                                <textarea name="chief_complaint" rows="3" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Tuliskan keluhan utama Anda...(Cth: Batuk Berdahak, Demam Tinggi)" required></textarea>
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
                <button type="submit" form="antrianForm" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">Daftar</button>
            </div>
       </div>
    </div>
<<<<<<< HEAD

    <!-- Modal Scanner QR -->
=======
>>>>>>> parent of 110fbee (update magang baru)
    <div id="qrScannerModal" class="hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Pindai QR Code Check-In</h3>
            <p class="text-gray-600 mb-4 text-sm">Arahkan kamera ke QR Code yang tersedia di meja pendaftaran.</p>
            <div id="qr-reader" class="w-full border rounded-lg overflow-hidden"></div>
<<<<<<< HEAD
            <button id="closeScannerBtn" class="mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg w-full transition">Batal & Tutup</button>
=======
            <button id="closeScannerBtn" class="mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg">Tutup</button>
>>>>>>> parent of 110fbee (update magang baru)
        </div>
    </div>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ambilAntrianBtn = document.getElementById('ambilAntrianBtn');
<<<<<<< HEAD
        const btnSubmitAntrian = document.getElementById('btnSubmitAntrian'); 
        
        if (ambilAntrianBtn) {
            const antrianModal = document.getElementById('antrianModal');
            const cancelModalBtn = document.getElementById('cancelModalBtn');
            const antrianForm = document.getElementById('antrianForm');
            const poliSelect = document.getElementById('poli');
            const doctorSelect = document.getElementById('doctor');
            const paymentSelect = document.getElementById('payment_method');
=======
        if (ambilAntrianBtn) {
             const antrianModal = document.getElementById('antrianModal');
             const cancelModalBtn = document.getElementById('cancelModalBtn');
             const antrianForm = document.getElementById('antrianForm');
             const poliSelect = document.getElementById('poli');
             const doctorSelect = document.getElementById('doctor');
             ambilAntrianBtn.addEventListener('click', () => antrianModal.classList.remove('hidden'));
             cancelModalBtn.addEventListener('click', () => antrianModal.classList.add('hidden'));
             antrianModal.addEventListener('click', (e) => { if (e.target.id === 'antrianModal') antrianModal.classList.add('hidden'); });
>>>>>>> parent of 110fbee (update magang baru)

             antrianForm.addEventListener('submit', function(e) {
                 e.preventDefault();
                 Swal.fire({ title: 'Apakah data sudah benar?', icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', cancelButtonColor: '#d33', confirmButtonText: 'Ya, daftarkan!', cancelButtonText: 'Periksa Lagi'
                 }).then((result) => { if (result.isConfirmed) { antrianForm.submit(); } });
             });

<<<<<<< HEAD
            antrianForm.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({ 
                    title: 'Apakah data sudah benar?', 
                    icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', cancelButtonColor: '#d33', 
                    confirmButtonText: 'Ya, daftarkan!', cancelButtonText: 'Periksa Lagi'
                }).then((result) => { 
                    if (result.isConfirmed) { antrianForm.submit(); } 
                });
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
                    }).catch(error => console.error('Fetch Error:', error));
                }
            });

            if(paymentSelect && btnSubmitAntrian) {
                paymentSelect.addEventListener('change', function() {
                    if(this.value === 'BPJS') {
                        btnSubmitAntrian.disabled = true;
                        btnSubmitAntrian.classList.replace('bg-green-500', 'bg-gray-400');
                        
                        Swal.fire({
                            title: 'Memverifikasi BPJS...',
                            text: 'Mohon tunggu, sistem sedang menghubungi server BPJS.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`{{ route('pasien.check-bpjs') }}`)
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const status = data.data.statusPeserta?.keterangan || data.data.status || '';
                                    if(status.toUpperCase().includes('AKTIF') && !status.toUpperCase().includes('TIDAK')) {
                                        Swal.fire('Terverifikasi!', 'Status BPJS Anda AKTIF. Silakan lanjutkan pendaftaran.', 'success');
                                        btnSubmitAntrian.disabled = false;
                                        btnSubmitAntrian.classList.replace('bg-gray-400', 'bg-green-500');
                                    } else {
                                        Swal.fire('Mohon Maaf', 'Status BPJS Anda tidak aktif / menunggak. Pembayaran dialihkan ke jalur Umum.', 'warning');
                                        paymentSelect.value = 'Umum';
                                        btnSubmitAntrian.disabled = false;
                                        btnSubmitAntrian.classList.replace('bg-gray-400', 'bg-green-500');
                                    }
                                } else {
                                    Swal.fire('Tidak Ditemukan', 'NIK Anda belum terdaftar di BPJS. Pembayaran dialihkan ke jalur Umum.', 'error');
                                    paymentSelect.value = 'Umum';
                                    btnSubmitAntrian.disabled = false;
                                    btnSubmitAntrian.classList.replace('bg-gray-400', 'bg-green-500');
                                }
                            }).catch(err => {
                                Swal.fire('Error', 'Gagal memverifikasi BPJS. Coba lagi nanti.', 'error');
                                paymentSelect.value = 'Umum';
                                btnSubmitAntrian.disabled = false;
                                btnSubmitAntrian.classList.replace('bg-gray-400', 'bg-green-500');
                            });
                    } else {
                        btnSubmitAntrian.disabled = false;
                        btnSubmitAntrian.classList.replace('bg-gray-400', 'bg-green-500');
                    }
                });
            }
=======
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
                     }).catch(error => console.error('Fetch Error:', error));
                 }
             });
>>>>>>> parent of 110fbee (update magang baru)
        }

        const checkInBtn = document.getElementById('checkInBtn');
        const qrScannerModal = document.getElementById('qrScannerModal');
        const closeScannerBtn = document.getElementById('closeScannerBtn');

        if (checkInBtn) {
            const html5QrCode = new Html5Qrcode("qr-reader");

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                html5QrCode.stop().catch(err => console.error("Gagal stop scanner.", err));

                Swal.fire({ title: 'Memproses Check-In', text: 'Mohon tunggu...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch(`{{ url('/pasien/check-in') }}/${decodedText}`, {
                    method: 'GET',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    qrScannerModal.classList.add('hidden');

                    if (data.success) {
<<<<<<< HEAD
                        Swal.fire({ icon: 'success', title: 'Check-In Berhasil!', text: data.message }).then(() => {
                            window.location.reload(); // Refresh untuk update UI Estimasi
                        });
=======
                        Swal.fire({ icon: 'success', title: 'Check-In Berhasil!', text: data.message });

                        document.getElementById('status-text-berobat').textContent = 'Hadir (Siap Dipanggil)';
                        const antreanCard = document.getElementById('antrean-card-berobat');
                        antreanCard.className = 'border border-indigo-300 bg-indigo-100 rounded-lg p-4 text-center transition-all duration-500';
                        antreanCard.querySelector('.text-sm').className = 'text-sm font-medium text-indigo-800 mb-2';
                        antreanCard.querySelector('.text-lg').className = 'text-lg text-indigo-800 font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block';
                        document.getElementById('action-button-container').innerHTML = `<div class="w-full bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-lg text-center text-base">Anda Sudah Melakukan Check-In</div>`;

>>>>>>> parent of 110fbee (update magang baru)
                    } else {
                        Swal.fire({ icon: 'error', title: 'Check-In Gagal', text: data.message || 'Terjadi kesalahan.' });
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    Swal.fire({ icon: 'error', title: 'Proses Gagal', text: 'Tidak dapat memproses. Pastikan QR code benar.' });
                });
            };

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            checkInBtn.addEventListener('click', () => {
                qrScannerModal.classList.remove('hidden');
                html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                    .catch(err => Swal.fire({ icon: 'error', title: 'Gagal Membuka Kamera', text: 'Izinkan akses kamera pada browser.' }));
            });

            closeScannerBtn.addEventListener('click', () => {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => qrScannerModal.classList.add('hidden')).catch(err => console.error("Gagal stop scanner.", err));
                } else {
                    qrScannerModal.classList.add('hidden');
                }
            });
        }

        const konfirmasiObatBtn = document.getElementById('konfirmasiObatBtn');
        if (konfirmasiObatBtn){
            konfirmasiObatBtn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Penerimaan Obat',
                    text: "Apakah Anda yakin sudah menerima obat sesuai resep?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Sudah Saya Terima!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('konfirmasiObatForm').submit();
                    }
                });
            });
        }
    });
    </script>
@endpush