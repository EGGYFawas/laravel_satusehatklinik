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
        /* Style untuk modal */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        .modal-content {
            z-index: 50;
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
                @if(session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li class="list-disc ml-4">{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Pemeriksaan & Detail Pasien --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-4">Area Pemeriksaan Pasien</h2>
                
                @if($pasienSedangDipanggil)
                    @php
                        $patient = $pasienSedangDipanggil->patient;
                        $isNewHistory = empty($patient->blood_type) && empty($patient->known_allergies) && empty($patient->chronic_diseases);
                    @endphp
                    <div>
                        <!-- Informasi Pasien -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-emerald-700">Pasien Saat Ini:</p>
                                    <p class="text-xl font-bold text-emerald-900">{{ $patient->full_name }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} thn)</p>
                                    <p class="text-sm text-gray-600">No. Antrean: <span class="font-semibold">{{ $pasienSedangDipanggil->queue_number }}</span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-emerald-700">Keluhan Utama:</p>
                                    <p class="font-semibold text-emerald-900">{{ $pasienSedangDipanggil->chief_complaint }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Pemeriksaan -->
                        <form action="{{ route('dokter.antrean.simpanPemeriksaan', $pasienSedangDipanggil->id) }}" method="POST" id="formPemeriksaan">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            
                            @if($isNewHistory)
                            <div class="mb-6 border border-yellow-300 bg-yellow-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-yellow-800 mb-3">Pasien Baru Terdeteksi</h3>
                                <p class="text-sm text-yellow-700 mb-4">Harap lengkapi data riwayat kesehatan pasien berikut ini.</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                                        <select name="blood_type" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                                            <option value="">Pilih...</option>
                                            <option value="A">A</option> <option value="B">B</option>
                                            <option value="AB">AB</option> <option value="O">O</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="known_allergies" class="block text-sm font-medium text-gray-700">Alergi yang Diketahui</label>
                                        <input type="text" name="known_allergies" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Paracetamol, Udang" required>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label for="chronic_diseases" class="block text-sm font-medium text-gray-700">Penyakit Kronis</label>
                                        <input type="text" name="chronic_diseases" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Hipertensi, Diabetes" required>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-6">
                                <label for="doctor_notes" class="block text-lg font-semibold text-gray-700 mb-2">Hasil Pemeriksaan & Catatan Dokter</label>
                                <textarea name="doctor_notes" rows="5" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tuliskan hasil pemeriksaan subjektif, objektif, dan asesmen di sini..." required></textarea>
                            </div>
                            <div class="mb-6">
                                <label for="diagnosis_tags" class="block text-lg font-semibold text-gray-700 mb-2">Diagnosis</label>
                                <select name="diagnosis_tags[]" id="diagnosis_tags" class="w-full" multiple="multiple">
                                    @if($diagnosisTags) @foreach($diagnosisTags as $tag) <option value="{{ $tag->tag_name }}">{{ $tag->tag_name }}</option> @endforeach @endif
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih diagnosis yang ada atau ketik untuk membuat diagnosis baru.</p>
                            </div>
                            
                            <!-- ========================================================= -->
                            <!-- == BAGIAN BARU: TOMBOL MODAL & LIST OBAT == -->
                            <!-- ========================================================= -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3">Resep Obat</h3>
                                <!-- Tombol untuk membuka modal -->
                                <button type="button" id="showObatModalBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" /></svg>
                                    Tambah & Pilih Obat
                                </button>
                                
                                <!-- Area untuk menampilkan list obat yang sudah dipilih -->
                                <div id="resep-obat-list" class="mt-4 space-y-2">
                                    {{-- List obat akan muncul di sini via JavaScript --}}
                                </div>
                            </div>
                            
                            <!-- Container tersembunyi untuk input data obat yang akan di-submit -->
                            <div id="hidden-medicine-inputs"></div>
                            
                            <!-- ========================================================= -->

                            <div class="mt-8 border-t pt-6 flex justify-end">
                                <button type="submit" form="formPemeriksaan" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-300">Selesaikan Pemeriksaan & Simpan</button>
                            </div>
                        </form>
                    </div>

                @else
                    <div class="text-center text-gray-500 py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <h3 class="text-xl font-semibold">Tidak ada pasien yang sedang diperiksa.</h3>
                        <p class="mt-2">Panggil pasien yang sudah check-in dari daftar di samping.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Antrean --}}
        <div>
            <!-- Antrean Hadir (Siap Dipanggil) -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-green-50 text-green-800">Antrean Hadir (Siap Dipanggil)</h3>
                <div class="p-4 max-h-64 overflow-y-auto">
                    @forelse($antreanHadir as $antrean)
                        <div class="flex items-center justify-between p-3 rounded-md border-b">
                            <div>
                                <p class="font-bold text-xl text-gray-800">{{ $antrean->queue_number }}</p>
                                <p class="text-sm text-gray-600">{{ $antrean->patient->full_name }}</p>
                            </div>
                            <div>
                                @if(!$pasienSedangDipanggil)
                                <form action="{{ route('dokter.antrean.panggil', $antrean->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Panggil</button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Belum ada pasien yang check-in.</p>
                    @endforelse
                </div>
            </div>

            <!-- Antrean Menunggu (Belum Check-In) -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4 bg-gray-50">Antrean Menunggu (Belum Check-In)</h3>
                <div class="p-4 max-h-64 overflow-y-auto">
                    @forelse($antreanMenunggu as $antrean)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <p class="font-bold text-xl text-gray-500">{{ $antrean->queue_number }}</p>
                                <p class="text-sm text-gray-500">{{ $antrean->patient->full_name }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Belum Check-in</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">Tidak ada pasien dalam antrean.</p>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Selesai -->
            <div class="bg-white rounded-xl shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4">Riwayat Selesai Hari Ini</h3>
                <div class="p-4 max-h-80 overflow-y-auto">
                    @forelse($antreanSelesai as $antrean)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <p class="font-semibold text-gray-700">{{ $antrean->queue_number }} - {{ $antrean->patient->full_name }}</p>
                                <p class="text-xs text-gray-500">Selesai pada: {{ \Carbon\Carbon::parse($antrean->finish_time)->format('H:i') }} WIB</p>
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

<!-- ========================================================= -->
<!-- == MODAL UNTUK TAMBAH RESEP OBAT == -->
<!-- ========================================================= -->
@if($pasienSedangDipanggil)
<div id="obatModal" class="fixed inset-0 flex items-center justify-center hidden">
    <div class="modal-backdrop" id="modalBackdrop"></div>
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-3xl m-4 max-h-[90vh] flex flex-col">
        <!-- Header Modal -->
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-2xl font-bold text-gray-800">Formulir Resep Obat</h3>
            <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <!-- Body Modal -->
        <div class="p-6 overflow-y-auto">
            <div id="medicine-rows-container" class="space-y-4">
                {{-- Baris input obat akan ditambahkan di sini oleh JavaScript --}}
            </div>
            <button type="button" id="add-medicine-row-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md text-sm w-full border border-gray-300">
                + Tambah Baris Obat
            </button>
        </div>
        <!-- Footer Modal -->
        <div class="flex justify-end items-center p-5 border-t bg-gray-50 rounded-b-xl">
            <button type="button" id="cancelModalBtn" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg mr-3">Batal</button>
            <button type="button" id="save-resep-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg">Simpan Resep</button>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
    {{-- CDN untuk jQuery dan Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    // =========================================================
    // == SKRIP BARU UNTUK MODAL RESEP OBAT ==
    // =========================================================
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Select2 untuk diagnosis
        $('#diagnosis_tags').select2({
            tags: true, // Memungkinkan membuat tag baru
            placeholder: "Pilih atau ketik diagnosis",
            tokenSeparators: [',']
        });

        const formPemeriksaan = document.getElementById('formPemeriksaan');
        if(!formPemeriksaan) return; // Keluar jika form tidak ada
        
        // --- DATA OBAT (diambil dari PHP) ---
        const medicinesData = {!! Illuminate\Support\Js::from($medicines ?? []) !!};
        let medicineRowCounter = 0;

        // --- ELEMEN DOM MODAL ---
        const modal = $('#obatModal');
        const showModalBtn = $('#showObatModalBtn');
        const closeModalBtn = $('#closeModalBtn');
        const cancelModalBtn = $('#cancelModalBtn');
        const modalBackdrop = $('#modalBackdrop');
        const addMedicineRowBtn = $('#add-medicine-row-btn');
        const medicineRowsContainer = $('#medicine-rows-container');
        const saveResepBtn = $('#save-resep-btn');

        // --- FUNGSI UNTUK MODAL ---
        const openModal = () => modal.removeClass('hidden');
        const closeModal = () => modal.addClass('hidden');

        // --- FUNGSI UNTUK MENAMBAH BARIS OBAT DI MODAL ---
        const addMedicineRow = () => {
            medicineRowCounter++;
            let medOptionsHtml = '<option value="">Pilih Obat</option>';
            medicinesData.forEach(med => {
                medOptionsHtml += `<option value="${med.id}" data-stock="${med.stock}">${med.name} (Stok: ${med.stock})</option>`;
            });

            // [MODIFIKASI] Opsi untuk dropdown dosis
            const dosageOptions = [
                "1x1 sehari sesudah makan", "2x1 sehari sesudah makan", "3x1 sehari sesudah makan",
                "1x1 sehari sebelum makan", "2x1 sehari sebelum makan", "3x1 sehari sebelum makan",
                "Jika perlu", "Oleskan", "Teteskan", "Lainnya..."
            ];
            let dosageOptionsHtml = dosageOptions.map(opt => `<option value="${opt}">${opt}</option>`).join('');

            const newRowHtml = `
                <div class="grid grid-cols-12 gap-3 p-3 bg-gray-50 rounded-lg border medicine-row" data-row-id="${medicineRowCounter}">
                    <div class="col-span-12 sm:col-span-4">
                        <label class="text-sm font-medium text-gray-700">Nama Obat</label>
                        <select class="w-full p-2 mt-1 border border-gray-300 rounded-md medicine-select" required>${medOptionsHtml}</select>
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" class="w-full p-2 mt-1 border border-gray-300 rounded-md quantity-input" min="1" required>
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <label class="text-sm font-medium text-gray-700">Dosis / Aturan Pakai</label>
                        <select class="w-full p-2 mt-1 border border-gray-300 rounded-md dosage-select">${dosageOptionsHtml}</select>
                        <input type="text" class="w-full p-2 mt-1 border border-gray-300 rounded-md dosage-text-input hidden" placeholder="Tulis dosis kustom..." >
                    </div>
                    <div class="col-span-12 sm:col-span-2 flex items-end">
                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded-md w-full mt-1 sm:mt-0 remove-row-btn">Hapus</button>
                    </div>
                </div>
            `;
            medicineRowsContainer.append(newRowHtml);
            
            // Inisialisasi Select2 pada select yang baru dibuat
            $(`.medicine-row[data-row-id="${medicineRowCounter}"] .medicine-select`).select2({
                 placeholder: "Pilih Obat",
                 dropdownParent: modal,
                 width: '100%'
            });
        };
        
        // --- EVENT LISTENERS ---
        showModalBtn.on('click', () => {
            if(medicineRowsContainer.is(':empty')){
                addMedicineRow();
            }
            openModal();
        });

        [closeModalBtn, cancelModalBtn, modalBackdrop].forEach(el => el.on('click', closeModal));
        addMedicineRowBtn.on('click', addMedicineRow);

        medicineRowsContainer.on('click', '.remove-row-btn', function() {
            $(this).closest('.medicine-row').remove();
        });

        medicineRowsContainer.on('change', '.medicine-select', function() {
            const selectedOption = $(this).find('option:selected');
            const stock = selectedOption.data('stock');
            const quantityInput = $(this).closest('.medicine-row').find('.quantity-input');
            if (stock) {
                quantityInput.attr('max', stock);
                if (parseInt(quantityInput.val()) > stock) {
                    quantityInput.val(stock);
                }
            } else {
                quantityInput.removeAttr('max');
            }
        });

        // [MODIFIKASI] Event listener untuk dropdown dosis hibrida
        medicineRowsContainer.on('change', '.dosage-select', function() {
            const textInput = $(this).siblings('.dosage-text-input');
            if ($(this).val() === 'Lainnya...') {
                textInput.removeClass('hidden');
                textInput.prop('required', true); // Jadikan wajib isi jika 'Lainnya' dipilih
            } else {
                textInput.addClass('hidden');
                textInput.prop('required', false);
            }
        });

        // --- LOGIKA SIMPAN RESEP DARI MODAL ---
        saveResepBtn.on('click', function() {
            let isValid = true;
            let firstError = null;

            medicineRowsContainer.find('.medicine-row').each(function() {
                const row = $(this);
                if (!row.find('.medicine-select').val()) isValid = false;
                if (!row.find('.quantity-input').val()) isValid = false;
                
                // [MODIFIKASI] Validasi dosis
                const dosageSelect = row.find('.dosage-select');
                const dosageTextInput = row.find('.dosage-text-input');
                if (dosageSelect.val() === 'Lainnya...' && !dosageTextInput.val()) {
                    isValid = false;
                    if (!firstError) firstError = 'Harap isi dosis kustom jika memilih "Lainnya...".';
                }
            });

            if (!isValid) {
                Swal.fire('Data Tidak Lengkap', firstError || 'Harap isi semua kolom untuk setiap baris obat.', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Resep',
                text: "Anda yakin ingin menyimpan resep ini? Data yang lama akan diganti.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#hidden-medicine-inputs').empty();
                    $('#resep-obat-list').empty();

                    medicineRowsContainer.find('.medicine-row').each(function(index) {
                        const row = $(this);
                        const medId = row.find('.medicine-select').val();
                        const medName = row.find('option:selected').text();
                        const quantity = row.find('.quantity-input').val();
                        
                        // [MODIFIKASI] Logika pengambilan nilai dosis
                        let dosage;
                        const dosageSelect = row.find('.dosage-select');
                        if (dosageSelect.val() === 'Lainnya...') {
                            dosage = row.find('.dosage-text-input').val();
                        } else {
                            dosage = dosageSelect.val();
                        }

                        const hiddenInputsHtml = `
                            <input type="hidden" name="medicines[${index}][id]" value="${medId}">
                            <input type="hidden" name="medicines[${index}][quantity]" value="${quantity}">
                            <input type="hidden" name="medicines[${index}][dosage]" value="${dosage}">
                        `;
                        $('#hidden-medicine-inputs').append(hiddenInputsHtml);

                        const displayListHtml = `
                            <div class="flex justify-between items-center p-3 bg-emerald-50 border border-emerald-200 rounded-md">
                                <div>
                                    <p class="font-semibold text-emerald-800">${medName.replace(/\s\(Stok: \d+\)$/, '')}</p>
                                    <p class="text-sm text-gray-600">Jumlah: ${quantity} | Dosis: ${dosage}</p>
                                </div>
                            </div>
                        `;
                        $('#resep-obat-list').append(displayListHtml);
                    });

                    closeModal();
                    Swal.fire('Berhasil!', 'Resep telah ditambahkan ke pemeriksaan.', 'success');
                }
            });
        });
        
        // --- KONFIRMASI SUBMIT FORM UTAMA ---
        formPemeriksaan.addEventListener('submit', function(e) {
            e.preventDefault();
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
    });
    </script>
@endpush

