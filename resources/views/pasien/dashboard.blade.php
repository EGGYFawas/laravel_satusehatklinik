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
        
        @php
            $hasActiveProcess = ($antreanBerobat && !in_array($antreanBerobat->status, ['SELESAI', 'BATAL'])) || 
                                ($antreanApotek && !in_array($antreanApotek->status, ['DITERIMA_PASIEN', 'BATAL']));
        @endphp

        @if(!$hasActiveProcess)
            <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-6 text-center mb-8">
                <img src="{{ asset('assets/img/ambil_antrean.png') }}" alt="Antrean Online" class="w-32 h-32 mx-auto mb-4">
                <h3 class="text-xl font-bold text-gray-800">Antrean Online</h3>
                <p class="text-gray-500 mb-6">Daftar antrean berobat menjadi lebih mudah.</p>
                <button id="ambilAntrianBtn" class="bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-md">Ambil Antrian</button>
            </div>
        @endif

        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- ====================================================== -->
            <!-- == KARTU ANTRIAN BEROBAT == -->
            <!-- ====================================================== -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Berobat</h3>
                
                @if($antreanBerobat)
                    @php
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
                                        } elseif ($antreanBerobat->status == 'HADIR') { $estimasi = "Menunggu antrean"; }
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
                        
                        {{-- [DIKEMBALIKAN] Logika untuk menampilkan tombol check-in dan status lainnya --}}
                        <div id="action-button-container" class="pt-4 border-t">
                            @if($antreanBerobat->status == 'MENUNGGU')
                                <button id="checkInBtn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md text-base">Saya Sudah Tiba, Lakukan Check-In</button>
                            @elseif($antreanBerobat->status == 'HADIR')
                                <div class="w-full bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-lg text-center text-base">Anda Sudah Melakukan Check-In</div>
                            @elseif($antreanBerobat->status == 'DIPANGGIL')
                                <div class="w-full bg-yellow-400 text-yellow-900 font-bold py-3 px-6 rounded-lg text-center text-lg animate-pulse">SEGERA MASUK KE RUANG PEMERIKSAAN</div>
                            @endif
                        </div>
                    </div>
                @elseif($riwayatBerobatTerakhir)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                        <p class="font-semibold text-gray-700">Kunjungan Terakhir Anda</p>
                        
                        {{-- [PERBAIKAN] Menambahkan @if untuk mencegah error pada data lama --}}
                        @if($riwayatBerobatTerakhir->finish_time)
                            <p class="text-2xl font-bold text-gray-800 mt-2">
                                {{ $riwayatBerobatTerakhir->finish_time->translatedFormat('l, d F Y') }}
                            </p>
                            <div class="mt-4 text-left space-y-2 text-sm">
                                <p><span class="font-semibold w-24 inline-block">Selesai Pukul</span>: {{ $riwayatBerobatTerakhir->finish_time->format('H:i') }} WIB</p>
                                <p><span class="font-semibold w-24 inline-block">Poli</span>: {{ $riwayatBerobatTerakhir->poli->name }}</p>
                                <p><span class="font-semibold w-24 inline-block">Dokter</span>: {{ $riwayatBerobatTerakhir->doctor->user->full_name ?? 'N/A' }}</p>
                            </div>
                        @else
                            {{-- Tampilan alternatif jika finish_time kosong --}}
                            <p class="text-lg text-gray-600 mt-2">
                                Data waktu kunjungan tidak lengkap.
                            </p>
                             <div class="mt-4 text-left space-y-2 text-sm">
                                <p><span class="font-semibold w-24 inline-block">Poli</span>: {{ $riwayatBerobatTerakhir->poli->name }}</p>
                                <p><span class="font-semibold w-24 inline-block">Dokter</span>: {{ $riwayatBerobatTerakhir->doctor->user->full_name ?? 'N/A' }}</p>
                            </div>
                        @endif
                        <a href="{{ route('pasien.riwayat.show', $riwayatBerobatTerakhir->patient_id) }}" class="mt-4 inline-block bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg text-sm">Lihat Detail Riwayat</a>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <p>Belum ada antrean berobat atau riwayat kunjungan.</p>
                    </div>
                @endif
            </div>
            
            <!-- ====================================================== -->
            <!-- == KARTU ANTRIAN APOTEK == -->
            <!-- ====================================================== -->
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

                    <div class="border {{ $borderColorApotek }} {{ $bgColorApotek }} rounded-lg p-4 text-center transition-all duration-500 {{ $pulseAnimationApotek }}">
                        <p class="text-sm font-medium {{ $textColorApotek }} mb-2">Resep Obat</p>
                        <p class="text-6xl font-extrabold text-[#24306E]">{{ $antreanApotek->pharmacy_queue_number }}</p>
                        <p class="text-lg {{ $textColorApotek }} font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block">{{ $statusTextApotek }}</p>
                    </div>

                    <div class="mt-6 space-y-4">
                        @if(in_array($antreanApotek->status, ['DALAM_ANTREAN', 'SEDANG_DIRACIK']))
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span class="font-semibold text-gray-700">Antrean Diproses:</span>
                                <span class="text-lg font-bold text-gray-900">{{ $antreanApotekBerjalan->pharmacy_queue_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span class="font-semibold text-gray-700">Estimasi Selesai:</span>
                                <span class="text-lg font-bold text-gray-900">
                                     @php
                                         $estimasiApotek = '-';
                                         if ($antreanApotek->status === 'SEDANG_DIRACIK') { $estimasiApotek = "Segera"; } 
                                         elseif ($antreanApotek->status === 'DALAM_ANTREAN') {
                                             $waktuTunggu = ($jumlahAntreanApotekSebelumnya) * 10;
                                             $estimasiApotek = $waktuTunggu > 0 ? "sekitar {$waktuTunggu} menit" : "Segera";
                                         }
                                     @endphp
                                     {{ $estimasiApotek }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="pt-4 border-t">
                             @if($antreanApotek->status == 'SIAP_DIAMBIL')
                                 <div class="w-full bg-green-500 text-white font-bold py-3 px-6 rounded-lg text-center text-lg animate-pulse">SEGERA MENUJU LOKET APOTEK</div>
                             @elseif($antreanApotek->status == 'DISERAHKAN')
                                 <form action="{{ route('pasien.antrean.apotek.konfirmasi', $antreanApotek->id) }}" method="POST" id="konfirmasiObatForm">
                                     @csrf
                                     <button type="button" id="konfirmasiObatBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-md text-base">Konfirmasi Obat Sudah Diterima</button>
                                 </form>
                             @elseif($antreanApotek->status == 'DITERIMA_PASIEN')
                                 <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-md">
                                     <div class="flex">
                                         <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                         <div>
                                             <p class="font-bold">Proses Selesai</p>
                                             <p class="text-sm">Terima kasih telah menyelesaikan seluruh proses berobat. Semoga lekas sembuh!</p>
                                         </div>
                                     </div>
                                 </div>
                             @endif
                        </div>
                    </div>
                @elseif($antreanBerobat && $antreanBerobat->status == 'SELESAI')
                    <div class="text-center text-gray-500 py-8"><p>Tidak ada resep obat untuk kunjungan ini.</p></div>
                @else
                    <div class="text-center text-gray-500 py-8"><p>Nomor antrean apotek akan muncul di sini setelah pemeriksaan selesai.</p></div>
                @endif
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
{{-- @if(!$hasActiveProcess) --}}
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
                    
                    {{-- [MODIFIKASI] Bagian Switch Dinonaktifkan --}}
                    {{-- <div class="flex items-center justify-center mb-6">
                        <label class="text-sm font-medium text-gray-900">Daftarkan Diri Sendiri</label>
                        
                        <!-- Tombol Switch dimatikan (Disabled) -->
                        <button type="button" disabled 
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-not-allowed rounded-full border-2 border-transparent bg-gray-200 transition-colors duration-200 ease-in-out focus:outline-none mx-3 opacity-60" 
                                role="switch">
                            <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                        
                        <label class="text-sm font-medium text-gray-400 decoration-slate-400">Daftarkan Anggota Keluarga (Nonaktif)</label>
                        
                        <!-- Force value is_family menjadi false/0 -->
                        <input type="hidden" name="is_family" value="0">
                    </div>
                    [END MODIFIKASI] --}}

                    <div class="border-t border-gray-200 pt-6">
                        {{-- Karena isFamily selalu false, bagian ini akan selalu muncul --}}
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

                        {{-- Bagian Form Anggota Keluarga (Tidak akan pernah muncul karena isFamily false) --}}
                        <div x-show="isFamily" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4 border-b border-gray-200 pb-4">
                            <!-- Konten form keluarga disembunyikan -->
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <h4 class="md:col-span-2 text-lg font-semibold text-gray-700 mb-2 pt-4 border-t border-gray-200">Detail Pendaftaran</h4>
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
                <button type="submit" form="antrianForm" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">Daftar</button>
            </div>
       </div>
    </div>
    {{-- @endif --}}
    <div id="qrScannerModal" class="hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 text-center relative">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Pindai QR Code Check-In</h3>
            <p class="text-gray-600 mb-4 text-sm">Arahkan kamera ke QR Code yang tersedia di meja pendaftaran.</p>
            <div id="qr-reader" class="w-full border rounded-lg overflow-hidden"></div>
            <button id="closeScannerBtn" class="mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg">Tutup</button>
        </div>
    </div>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ambilAntrianBtn = document.getElementById('ambilAntrianBtn');
        if (ambilAntrianBtn) {
             const antrianModal = document.getElementById('antrianModal');
             const cancelModalBtn = document.getElementById('cancelModalBtn');
             const antrianForm = document.getElementById('antrianForm');
             const poliSelect = document.getElementById('poli');
             const doctorSelect = document.getElementById('doctor');
             ambilAntrianBtn.addEventListener('click', () => antrianModal.classList.remove('hidden'));
             cancelModalBtn.addEventListener('click', () => antrianModal.classList.add('hidden'));
             antrianModal.addEventListener('click', (e) => { if (e.target.id === 'antrianModal') antrianModal.classList.add('hidden'); });
             
             antrianForm.addEventListener('submit', function(e) {
                 e.preventDefault();
                 Swal.fire({ title: 'Apakah data sudah benar?', icon: 'question', showCancelButton: true, confirmButtonColor: '#28a745', cancelButtonColor: '#d33', confirmButtonText: 'Ya, daftarkan!', cancelButtonText: 'Periksa Lagi'
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
                     }).catch(error => console.error('Fetch Error:', error));
                 }
             });
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
                        Swal.fire({ icon: 'success', title: 'Check-In Berhasil!', text: data.message });
                        
                        document.getElementById('status-text-berobat').textContent = 'Hadir (Siap Dipanggil)';
                        const antreanCard = document.getElementById('antrean-card-berobat');
                        antreanCard.className = 'border border-indigo-300 bg-indigo-100 rounded-lg p-4 text-center transition-all duration-500';
                        antreanCard.querySelector('.text-sm').className = 'text-sm font-medium text-indigo-800 mb-2';
                        antreanCard.querySelector('.text-lg').className = 'text-lg text-indigo-800 font-semibold mt-4 bg-white/50 rounded-full px-4 py-1 inline-block';
                        document.getElementById('action-button-container').innerHTML = `<div class="w-full bg-gray-200 text-gray-600 font-bold py-3 px-6 rounded-lg text-center text-base">Anda Sudah Melakukan Check-In</div>`;

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

