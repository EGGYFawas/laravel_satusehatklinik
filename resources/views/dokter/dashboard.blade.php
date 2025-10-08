@extends('layouts.dokter_layout')

@section('title', 'Dashboard Dokter')

@push('styles')
    {{-- CDN untuk SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- CDN untuk Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Style kustom untuk Select2 agar sesuai tema */
        .select2-container .select2-selection--multiple {
            border-color: #d1d5db; /* border-gray-300 */
            border-radius: 0.375rem; /* rounded-md */
            padding: 0.3rem;
            min-height: 42px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #059669; /* bg-emerald-600 */
            border-color: #047857;
            color: white;
            padding: 2px 8px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.7);
            margin-right: 5px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: white;
        }
        .select2-container .select2-search--inline .select2-search__field {
            margin-top: 0.5rem;
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
                    <div x-data="{ isNewHistory: {{ $isNewHistory ? 'true' : 'false' }} }">
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
                            
                            <!-- Form Riwayat Pasien Baru (Conditional) -->
                            <div x-show="isNewHistory" x-transition class="mb-6 border border-yellow-300 bg-yellow-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Pasien Baru Terdeteksi
                                </h3>
                                <p class="text-sm text-yellow-700 mb-4">Harap lengkapi data riwayat kesehatan pasien berikut ini.</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="blood_type" class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                                        <select name="blood_type" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" :required="isNewHistory">
                                            <option value="">Pilih...</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="known_allergies" class="block text-sm font-medium text-gray-700">Alergi yang Diketahui</label>
                                        <input type="text" name="known_allergies" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Paracetamol, Udang" :required="isNewHistory">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label for="chronic_diseases" class="block text-sm font-medium text-gray-700">Penyakit Kronis</label>
                                        <input type="text" name="chronic_diseases" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Hipertensi, Diabetes" :required="isNewHistory">
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan Dokter & Diagnosis -->
                            <div class="mb-6">
                                <label for="doctor_notes" class="block text-lg font-semibold text-gray-700 mb-2">Hasil Pemeriksaan & Catatan Dokter</label>
                                <textarea name="doctor_notes" rows="5" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Tuliskan hasil pemeriksaan subjektif, objektif, dan asesmen di sini..." required></textarea>
                            </div>
                            <div class="mb-6">
                                <label for="diagnosis_tags" class="block text-lg font-semibold text-gray-700 mb-2">Diagnosis</label>
                                <select name="diagnosis_tags[]" id="diagnosis_tags" class="w-full" multiple="multiple">
                                    @if($diagnosisTags)
                                        @foreach($diagnosisTags as $tag)
                                            <option value="{{ $tag->tag_name }}">{{ $tag->tag_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih diagnosis yang ada atau ketik untuk membuat diagnosis baru.</p>
                            </div>

                            <!-- Resep Obat (Dikelola Alpine.js) -->
                            <div x-data="prescriptionHandler()">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3">Resep Obat</h3>
                                <template x-for="(med, index) in medicines" :key="index">
                                    <div class="grid grid-cols-12 gap-3 mb-3 p-3 bg-gray-50 rounded-lg border">
                                        <div class="col-span-12 sm:col-span-4">
                                            <label class="text-sm font-medium text-gray-700">Nama Obat</label>
                                            <select :name="`medicines[${index}][id]`" class="w-full p-2 mt-1 border border-gray-300 rounded-md medicine-select" @change="updateMaxStock($event, index)" required>
                                                <option value="">Pilih Obat</option>
                                                @if($medicines)
                                                    @foreach($medicines as $medicine)
                                                    <option value="{{ $medicine->id }}" data-stock="{{ $medicine->stock }}">{{ $medicine->name }} (Stok: {{ $medicine->stock }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-span-6 sm:col-span-2">
                                            <label class="text-sm font-medium text-gray-700">Jumlah</label>
                                            <input type="number" :name="`medicines[${index}][quantity]`" x-model.number="med.quantity" class="w-full p-2 mt-1 border border-gray-300 rounded-md" min="1" :max="med.maxStock" required>
                                        </div>
                                        <div class="col-span-6 sm:col-span-4">
                                            <label class="text-sm font-medium text-gray-700">Dosis / Aturan Pakai</label>
                                            <input type="text" :name="`medicines[${index}][dosage]`" class="w-full p-2 mt-1 border border-gray-300 rounded-md" placeholder="Cth: 3x1 sesudah makan" required>
                                        </div>
                                        <div class="col-span-12 sm:col-span-2 flex items-end">
                                            <button type="button" @click="removeMedicine(index)" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded-md w-full mt-1 sm:mt-0">Hapus</button>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" @click="addMedicine" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">+ Tambah Obat</button>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="mt-8 border-t pt-6 flex justify-end">
                                <button type="submit" form="formPemeriksaan" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-300">
                                    Selesaikan Pemeriksaan & Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                @else
                    <div class="text-center text-gray-500 py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <h3 class="text-xl font-semibold">Tidak ada pasien yang sedang diperiksa.</h3>
                        <p class="mt-2">Silakan panggil pasien dari daftar antrean di samping.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Antrean --}}
        <div>
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <h3 class="text-lg font-bold text-gray-800 border-b p-4">Antrean Menunggu</h3>
                <div class="p-4 max-h-64 overflow-y-auto">
                    @forelse($antreanMenunggu as $index => $antrean)
                        <div class="flex items-center justify-between p-3 rounded-md {{ $loop->first ? 'bg-blue-50 border border-blue-200' : 'border-b' }}">
                            <div>
                                <p class="font-bold text-xl text-gray-800">{{ $antrean->queue_number }}</p>
                                <p class="text-sm text-gray-600">{{ $antrean->patient->full_name }}</p>
                            </div>
                            <div>
                                @if($loop->first && !$pasienSedangDipanggil)
                                <form action="{{ route('dokter.antrean.panggil', $antrean->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Panggil</button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">Menunggu giliran</span>
                                @endif
                            </div>
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
@endsection

@push('scripts')
    {{-- CDN untuk jQuery dan Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Select2 untuk diagnosis
            $('#diagnosis_tags').select2({
                tags: true, // Memungkinkan membuat tag baru
                placeholder: "Pilih atau ketik diagnosis",
                tokenSeparators: [',']
            });

            // Inisialisasi Select2 untuk pilihan obat
            // Perlu diinisialisasi ulang setiap kali baris baru ditambahkan
            function initializeMedicineSelect() {
                $('.medicine-select:not(.select2-hidden-accessible)').select2({
                    placeholder: "Pilih Obat",
                });
            }
            initializeMedicineSelect();
            
            // Logika Alpine.js untuk resep
            window.prescriptionHandler = function() {
                return {
                    medicines: [], // Mulai dengan resep kosong
                    addMedicine() {
                        this.medicines.push({ id: '', quantity: 1, dosage: '', maxStock: 999 });
                        this.$nextTick(() => {
                            initializeMedicineSelect(); // Inisialisasi ulang Select2 untuk baris baru
                        });
                    },
                    removeMedicine(index) {
                        this.medicines.splice(index, 1);
                    },
                    updateMaxStock(event, index) {
                        const selectedOption = event.target.options[event.target.selectedIndex];
                        const stock = selectedOption.dataset.stock;
                        this.medicines[index].maxStock = stock ? parseInt(stock) : 999;
                        // Reset kuantitas jika melebihi stok
                        if (this.medicines[index].quantity > this.medicines[index].maxStock) {
                            this.medicines[index].quantity = this.medicines[index].maxStock;
                        }
                    }
                }
            }

            // Konfirmasi sebelum submit form
            const formPemeriksaan = document.getElementById('formPemeriksaan');
            if(formPemeriksaan) {
                formPemeriksaan.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Penyimpanan',
                        text: "Anda yakin ingin menyelesaikan pemeriksaan dan menyimpan data ini? Tindakan ini tidak dapat diubah.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981', // emerald-500
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

