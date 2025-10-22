@extends('layouts.pasien_layout')

@section('title', 'Profil Saya')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .readonly-field {
            background-color: #f7f7f7;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 0.5rem;
            width: 100%;
            color: #4a5568;
            min-height: 44px;
            display: flex;
            align-items: center;
        }
        .readonly-field.placeholder-text {
            color: #a0aec0;
            font-style: italic;
        }
        .data-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
            display: block;
        }
        /* Custom scrollbar untuk modal */
        #editProfileModal .overflow-y-auto::-webkit-scrollbar { width: 8px; }
        #editProfileModal .overflow-y-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        #editProfileModal .overflow-y-auto::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        #editProfileModal .overflow-y-auto::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
@endpush

@section('content')
    {{-- Tampilan Utama Read-Only --}}
    <div class="w-full max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Profil Pasien</h2>
        {{-- ... Konten read-only di sini (tidak perlu diubah) ... --}}
        <div class="space-y-4">
             <div>
                <span class="data-label">Nama Lengkap (Sesuai KTP)</span>
                <p class="readonly-field">{{ $patient->full_name ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="data-label">Nomor Induk Kependudukan (NIK)</span>
                    <p class="readonly-field">{{ $patient->nik ?? '-' }}</p>
                </div>
                <div>
                    <span class="data-label">Email</span>
                    <p class="readonly-field">{{ $user->email ?? '-' }}</p>
                </div>
                <div>
                    <span class="data-label">Nomor Telepon</span>
                    <p class="readonly-field @if(!$patient->phone_number) placeholder-text @endif">
                        {{ $patient->phone_number ?: 'Belum diisi' }}
                    </p>
                </div>
                <div>
                    <span class="data-label">Tanggal Lahir</span>
                    <p class="readonly-field">{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->translatedFormat('d F Y') : '-' }}</p>
                </div>
                <div>
                    <span class="data-label">Jenis Kelamin</span>
                    <p class="readonly-field">{{ $patient->gender ?? '-' }}</p>
                </div>
            </div>
            <div>
                <span class="data-label">Alamat Lengkap</span>
                <p class="readonly-field min-h-[60px] @if(!$patient->address) placeholder-text @endif">
                    {{ $patient->address ?: 'Belum diisi' }}
                </p>
            </div>
        </div>
         @if($patient->blood_type || $patient->known_allergies || $patient->chronic_diseases)
        <div class="mt-8 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Medis (Terverifikasi)</h3>
             <div class="text-xs text-gray-500 italic mb-4">
                *Informasi medis di bawah hanya dapat diubah oleh dokter. Jika terdapat kesalahan, mohon informasikan pada kunjungan berikutnya.
            </div>
            <div class="space-y-4">
                <div>
                    <span class="data-label">Golongan Darah</span><p class="readonly-field">{{ $patient->blood_type ?? 'Belum ada data' }}</p>
                </div>
                 <div>
                    <span class="data-label">Riwayat Alergi</span><p class="readonly-field min-h-[60px]">{{ $patient->known_allergies ?? 'Belum ada data' }}</p>
                </div>
                 <div>
                    <span class="data-label">Riwayat Penyakit Kronis</span><p class="readonly-field min-h-[60px]">{{ $patient->chronic_diseases ?? 'Belum ada data' }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row justify-end items-center gap-4">
            <a href="{{ route('pasien.dashboard') }}" class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-300 text-center">Kembali</a>
            <button id="editProfileBtn" type="button" class="w-full sm:w-auto bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md">Edit Profil</button>
        </div>
    </div>

    {{-- Modal untuk Edit Profil --}}
    <div id="editProfileModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-start justify-center z-50 hidden pt-16 sm:pt-24 pb-8">
        <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="border-b pb-4 mb-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-gray-800">Edit Profil dan Akun</h3>
                    <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800 text-3xl transition">&times;</button>
                </div>
                {{-- [MODIFIKASI] Notifikasi data belum lengkap di dalam modal --}}
                @php
                    $modalIncompleteFields = [];
                    if (empty($patient->phone_number)) { $modalIncompleteFields[] = 'Nomor Telepon'; }
                    if (empty($patient->address)) { $modalIncompleteFields[] = 'Alamat'; }
                @endphp
                @if(!empty($modalIncompleteFields))
                <div class="mt-4 bg-yellow-100 border border-yellow-300 text-yellow-800 text-sm rounded-md p-3" role="alert">
                    <strong>Perhatian:</strong> Untuk meningkatkan kualitas layanan, mohon lengkapi data <strong>{{ implode(' & ', $modalIncompleteFields) }}</strong> Anda.
                </div>
                @endif
            </div>
            
            {{-- [PERBAIKAN KUNCI] Menambahkan 'novalidate' ke form --}}
            <form id="profileForm" action="{{ route('pasien.profil.update') }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                {{-- ... Konten form di sini (tidak perlu diubah) ... --}}
                <div class="space-y-4">
                     <h4 class="text-lg font-semibold text-gray-700 mb-2 border-b pb-2">Data Pribadi</h4>
                     <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Sesuai KTP)</label>
                        <input type="text" id="full_name" name="full_name" class="w-full p-2.5 border border-gray-300 rounded-md uppercase" value="{{ old('full_name', $patient->full_name) }}" required oninput="this.value = this.value.toUpperCase()">
                        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit)</label>
                        <input type="text" id="nik" name="nik" class="w-full p-2.5 border border-gray-300 rounded-md" value="{{ old('nik', $patient->nik) }}" required maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="Masukkan 16 digit NIK Anda">
                        @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" id="phone_number" name="phone_number" class="w-full p-2.5 border border-gray-300 rounded-md" value="{{ old('phone_number', $patient->phone_number) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="Contoh: 081234567890">
                         @error('phone_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea id="address" name="address" rows="3" class="w-full p-2.5 border border-gray-300 rounded-md" placeholder="Contoh: Jl. Pahlawan No. 25, RT 01/RW 02, Kel. Sukajadi, Kota Bandung">{{ old('address', $patient->address) }}</textarea>
                         @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                     <div class="border-t pt-4 mt-4">
                         <h4 class="text-lg font-semibold text-gray-700 mb-2 border-b pb-2">Data Akun</h4>
                        <div id="readOnlyAccountSection">
                             <div>
                                <span class="data-label">Email Saat Ini</span>
                                <p class="readonly-field">{{ $user->email }}</p>
                            </div>
                            <button type="button" id="toggleEditAccountBtn" class="mt-4 w-full sm:w-auto bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold py-2 px-4 rounded-lg transition duration-300 text-sm">Ubah Email atau Password</button>
                        </div>
                        <div id="editAccountSection" class="hidden mt-4 space-y-4">
                            <input type="hidden" name="change_account" id="change_account_flag" value="">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Baru</label>
                                <input type="email" id="email" name="email" class="w-full p-2.5 border border-gray-300 rounded-md" placeholder="Masukkan email baru" value="{{ old('email') }}">
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" id="password" name="password" class="w-full p-2.5 border border-gray-300 rounded-md" placeholder="Minimal 6 karakter">
                                <small class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</small>
                                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                             <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full p-2.5 border border-gray-300 rounded-md" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t flex justify-end items-center gap-4">
                    <button type="button" id="cancelBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-300">Batal</button>
                    <button type="submit" class="bg-[#24306E] hover:bg-[#1a224d] text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editProfileModal');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const profileForm = document.getElementById('profileForm');
    const toggleEditAccountBtn = document.getElementById('toggleEditAccountBtn');
    const readOnlySection = document.getElementById('readOnlyAccountSection');
    const editSection = document.getElementById('editAccountSection');
    const changeAccountFlag = document.getElementById('change_account_flag');

    const openModal = () => modal.classList.remove('hidden');
    const closeModal = () => {
        modal.classList.add('hidden');
        readOnlySection.classList.remove('hidden'); // Reset tampilan akun
        editSection.classList.add('hidden');
        changeAccountFlag.value = '';
    };

    if (editProfileBtn) editProfileBtn.addEventListener('click', openModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => (e.target === modal) && closeModal());
    
    toggleEditAccountBtn.addEventListener('click', () => {
        editSection.classList.remove('hidden');
        readOnlySection.classList.add('hidden');
        changeAccountFlag.value = 'true';
    });

    // [MODIFIKASI] Cek jika modal harus dibuka karena error validasi
    @if(session('open_modal'))
        openModal();
        // Jika errornya ada di email atau password, buka juga bagian edit akun
        @if($errors->has('email') || $errors->has('password'))
            editSection.classList.remove('hidden');
            readOnlySection.classList.add('hidden');
            changeAccountFlag.value = 'true';
        @endif
    @endif

    if(profileForm) {
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data yang Anda masukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#24306E',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    profileForm.submit();
                }
            });
        });
    }

    // Notifikasi sukses
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            icon: 'success',
            confirmButtonColor: '#24306E'
        });
    @endif
});
</script>
@endpush

