@extends('layouts.pasien_layout')

@section('title', 'Dashboard Pasien')

@push('styles')
    {{-- Tambahkan link CDN SweetAlert2 di sini atau di layout utama --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    {{-- Notifikasi Sukses atau Error --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-md" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Konten Utama Dashboard --}}
    <div class="flex flex-col items-center w-full">
        <!-- Card Ambil Antrean -->
        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-6 text-center mb-8">
            <img src="{{ asset('assets/img/ambil_antrean.png') }}" alt="Antrean Online" class="w-32 h-32 mx-auto mb-4">
            <h3 class="text-xl font-bold text-gray-800">Antrean Online</h3>
            <p class="text-gray-500 mb-6">Daftar antrean berobat menjadi lebih mudah.</p>
            @if(!$antreanBerobat)
                <button id="ambilAntrianBtn" class="bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-md">Ambil Antrian</button>
            @else
                <button class="bg-gray-400 text-white font-bold py-3 px-8 rounded-lg cursor-not-allowed" disabled>Anda Sudah Punya Antrean</button>
            @endif
        </div>
        
        <!-- Card Antrean Berobat & Apotik -->
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Berobat</h3>
                @if($antreanBerobat)
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500 mb-2">Poli {{ $antreanBerobat->poli->name }}</p>
                    <p class="text-6xl font-extrabold text-[#24306E]">{{ $antreanBerobat->queue_number }}</p>
                    <p class="text-lg text-yellow-600 font-semibold mt-4 bg-yellow-100 rounded-full px-4 py-1 inline-block">Status: {{ ucwords(strtolower(str_replace('_', ' ', $antreanBerobat->status))) }}</p>
                </div>
                @else
                <div class="text-center text-gray-500 py-8"><p>Belum ada antrean dibuat hari ini.</p></div>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Nomor Antrean Apotik</h3>
                <div class="text-center text-gray-500 py-8"><p>Belum ada resep obat terbaru.</p></div>
                 {{-- Nanti diisi dengan data pharmacy_queues --}}
            </div>
        </div>
    </div>

    {{-- Artikel Kesehatan --}}
    {{-- ... (bagian artikel tidak diubah) ... --}}
@endsection

@push('modals')
    <!-- Modal Ambil Antrian -->
    <div id="antrianModal" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-gray-700 bg-opacity-50">
        {{-- Mengadopsi style dari kode Anda: lebar, padding, rounded, shadow --}}
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md" id="modal-content">
            {{-- Mengadopsi style header dari kode Anda --}}
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-700">Formulir Antrean</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            
            @if($patient)
            <form id="antrianForm" action="{{ route('pasien.antrean.store') }}" method="POST">
                @csrf
                {{-- Menggunakan div tanpa 'space-y' untuk kontrol margin per elemen --}}
                <div>
                    {{-- DATA DIRI PASIEN (OTOMATIS) --}}
                    {{-- Mengadopsi style label dan input dari kode Anda --}}
                    <label for="full_name" class="block text-gray-600 mb-2">Nama:</label>
                    <input type="text" id="full_name" name="full_name" class="w-full p-2 border border-gray-300 rounded mb-4 bg-gray-100" value="{{ $patient->full_name }}" readonly>
                    
                    <label for="nik" class="block text-gray-600 mb-2">NIK:</label>
                    <input type="text" id="nik" name="nik" class="w-full p-2 border border-gray-300 rounded mb-4 bg-gray-100" value="{{ $patient->nik }}" readonly>
                    
                    {{-- DATA PENDAFTARAN --}}
                    <label for="registration_date" class="block text-gray-600 mb-2">Tanggal Berobat:</label>
                    <input type="date" id="registration_date" name="registration_date" class="w-full p-2 border border-gray-300 rounded mb-4 bg-gray-100" value="{{ date('Y-m-d') }}" readonly>
                    
                    <label for="poli" class="block text-gray-600 mb-2">Pilih Poli:</label>
                    <select id="poli" name="poli_id" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                        <option value="" disabled selected>-- Silahkan Pilih Poli --</option>
                        @foreach($polis as $poli)
                        <option value="{{ $poli->id }}">{{ $poli->name }}</option>
                        @endforeach
                    </select>
                    
                    <label for="doctor" class="block text-gray-600 mb-2">Pilih Dokter:</label>
                    <select id="doctor" name="doctor_id" class="w-full p-2 border border-gray-300 rounded mb-4" required disabled>
                        <option value="">-- Pilih Poli Terlebih Dahulu --</option>
                    </select>
                    
                    <label for="chief_complaint" class="block text-gray-600 mb-2">Keluhan:</label>
                    <textarea id="chief_complaint" name="chief_complaint" rows="3" class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Tuliskan keluhan utama Anda..." required></textarea>
                    
                    {{-- Mengadopsi style tombol dan layout dari kode Anda --}}
                    <div class="flex justify-between mt-6">
                        <button type="button" id="cancelModalBtn" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Batal</button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
                    </div>
                </div>
            </form>
            @else
            <div class="text-center py-8">
                <p class="text-red-500">Profil pasien tidak ditemukan. Mohon lengkapi profil Anda terlebih dahulu.</p>
            </div>
            @endif
        </div>
    </div>
@endpush

@push('scripts')
{{-- Tidak ada perubahan pada script karena sudah menangani semua fungsionalitas yang diperlukan --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ambilAntrianBtn = document.getElementById('ambilAntrianBtn');
    const antrianModal = document.getElementById('antrianModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const antrianForm = document.getElementById('antrianForm');
    const poliSelect = document.getElementById('poli');
    const doctorSelect = document.getElementById('doctor');

    function openModal() { antrianModal.classList.remove('hidden'); }
    function closeModal() { 
        antrianModal.classList.add('hidden'); 
        if (antrianForm) {
            antrianForm.reset(); // Reset form
            doctorSelect.innerHTML = '<option value="">-- Pilih Poli Terlebih Dahulu --</option>';
            doctorSelect.disabled = true;
        }
    }

    if (ambilAntrianBtn) {
        ambilAntrianBtn.addEventListener('click', openModal);
    }
    closeModalBtn.addEventListener('click', closeModal);
    
    // Klik di luar modal akan menutup modal
    antrianModal.addEventListener('click', (e) => {
        if (e.target === antrianModal) {
            // Kita gunakan konfirmasi batal di tombolnya saja
            closeModal();
        }
    });

    // Event listener untuk tombol Batal dengan konfirmasi SweetAlert2
    cancelModalBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'Yakin membatalkan antrean?',
            text: "Data yang sudah Anda isi akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Merah untuk tombol konfirmasi batal
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                closeModal();
            }
        });
    });

    // Event listener untuk submit form dengan konfirmasi SweetAlert2
    if(antrianForm) {
        antrianForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah form submit secara otomatis
            
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan semua data yang Anda masukkan sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a', // Hijau untuk tombol konfirmasi simpan
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Periksa Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    antrianForm.submit(); // Lanjutkan submit form
                }
            });
        });
    }

    // Fetch dokter berdasarkan poli (fungsionalitas inti tetap sama)
    poliSelect.addEventListener('change', function() {
        const poliId = this.value;
        doctorSelect.innerHTML = '<option value="">Memuat dokter...</option>';
        doctorSelect.disabled = true;

        if (poliId) {
            fetch(`{{ url('/pasien/doctors-by-poli') }}/${poliId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        doctorSelect.innerHTML = '<option value="" disabled selected>-- Silahkan Pilih Dokter --</option>';
                        data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = doctor.name;
                            doctorSelect.appendChild(option);
                        });
                        doctorSelect.disabled = false;
                    } else {
                        doctorSelect.innerHTML = '<option value="">-- Tidak ada dokter praktek hari ini --</option>';
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    doctorSelect.innerHTML = '<option value="">Gagal memuat dokter</option>';
                });
        } else {
            doctorSelect.innerHTML = '<option value="">-- Pilih Poli Terlebih Dahulu --</option>';
        }
    });
});
</script>
@endpush

