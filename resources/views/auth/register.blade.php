@extends('layouts.guest')

@section('title', 'Buat Akun Baru')

@section('content')
{{-- Style untuk NIK loader dan Modal --}}
<style>
    .nik-status-indicator {
        position: absolute;
        right: 0.75rem; /* 12px */
        top: 50%;
        transform: translateY(-50%);
        display: none; /* Sembunyi by default */
    }
    .nik-status-indicator.loading {
        display: inline-block;
        border: 3px solid #f3f3f3; /* Light grey */
        border-top: 3px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }
    .nik-status-indicator.success {
        display: inline-block;
        color: #10B981; /* green-500 */
    }
     .nik-status-indicator.error {
        display: inline-block;
        color: #EF4444; /* red-500 */
    }
    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }

    /* Style untuk Modal */
    .modal-backdrop {
        transition: opacity 0.3s ease;
    }
    .modal-panel {
        transition: all 0.3s ease;
    }
</style>

{{-- Kontainer utama yang membungkus seluruh halaman --}}
<div class="flex items-center justify-center min-h-screen bg-brand-bg p-4">

    {{-- Kartu registrasi utama --}}
    <div class="flex flex-col md:flex-row w-full max-w-4xl bg-white shadow-2xl rounded-2xl overflow-hidden">

        <!-- Bagian Branding (Kiri) -->
        <div class="w-full md:w-[45%] bg-brand-primary/10 text-center p-8 flex flex-col justify-center items-center order-last md:order-first">
            {{-- Ganti src ini jika path gambar Anda berbeda --}}
            <img src="{{ asset('assets/img/logo_login.png') }}" alt="Ilustrasi Medis" class="max-w-[250px] mb-6">
            
            <p class="text-lg font-medium text-text-dark mb-6">“Berobat lebih mudah tanpa antri”</p>
            
            <div class="flex items-center gap-4 w-full justify-center">
                {{-- Tombol Kembali --}}
                <a href="{{ route('login') }}" 
                   class="w-1/2 bg-blue-200 text-putih font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                   Kembali
                </a>
                {{-- Tombol untuk men-submit form di sebelah kanan --}}
                <button type="submit" form="registerForm" 
                        class="w-1/2 bg-brand-primary text-brand-text font-semibold py-3 px-6 rounded-full hover:opacity-90 transition-opacity duration-300 shadow-lg">
                   Daftar Akun
                </button>
            </div>
        </div>

        <!-- Bagian Form (Kanan) -->
        <div class="w-full md:w-[55%] p-8 overflow-y-auto" style="max-height: 90vh;">
            <h1 class="text-2xl font-bold text-text-dark mb-2">Buat Akun Baru</h1>
            <p class="text-sm text-text-grey mb-6">Silakan isi data diri Anda dengan benar.</p>
            
            {{-- Menampilkan error validasi dari Laravel --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4 text-sm" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-3">
                @csrf
                
                {{-- NIK Input dengan wrapper --}}
                <div class="relative">
                    <label for="nik" class="block text-sm font-medium text-text-dark mb-1">NIK</label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required placeholder="Masukkan 16 Digit NIK"
                           pattern="\d{16}" title="NIK harus terdiri dari 16 angka" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    {{-- Indikator loading/success/error --}}
                    <div id="nik_status_indicator" class="nik-status-indicator" style="top: 65%;"></div>
                    <p id="nik_message" class="text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <label for="full_name" class="block text-sm font-medium text-text-dark mb-1">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="Masukkan Nama Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition uppercase bg-gray-50">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-text-dark mb-1">Jenis Kelamin</label>
                        <select id="gender" name="gender" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition bg-gray-50">
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-text-dark mb-1">Tanggal Lahir</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition bg-gray-50">
                    </div>
                </div>

                <hr class="my-4">
                <p class="text-sm text-text-grey -mt-2 mb-2">Lengkapi data akun Anda.</p>

                <div>
                    <label for="email" class="block text-sm font-medium text-text-dark mb-1">Email</label>
                    <!-- Tambahkan autocomplete="off" dan trik readonly + onfocus -->
                    <input type="email" id="email" name="email" value="" required placeholder="contoh: email@gmail.com"
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly');"
                           style="background-color: white;" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                        <small class="text-xs text-text-grey mt-1">Akun email Harus valid untuk verifikasi email.</small>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-text-dark mb-1">Password</label>
                    <!-- Gunakan autocomplete="new-password" agar browser tahu ini password baru -->
                    <input type="password" id="password" name="password" required placeholder="Buat Password Anda"
                           autocomplete="new-password"
                           readonly
                           onfocus="this.removeAttribute('readonly');"
                           style="background-color: white;"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    <small class="text-xs text-text-grey mt-1">Minimal 6 karakter dengan kombinasi huruf dan angka.</small>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text-dark mb-1">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi Password Anda"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary transition">
                    <small id="passwordMatchMessage" class="mt-1 text-xs"></small>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="terms" name="terms" required class="h-4 w-4 rounded border-gray-300 text-brand-primary focus:ring-brand-primary">
                    <label for="terms" class="text-sm text-text-grey">
                        Saya setuju dengan <a href="#" class="text-brand-primary hover:underline">Syarat dan Ketentuan</a>.
                    </label>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- Modal untuk Akun yang Sudah Ada --}}
<div id="accountExistsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <!-- Backdrop -->
    <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop opacity-0"></div>
    
    <!-- Panel Modal -->
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md z-10 modal-panel transform scale-95 opacity-0">
        <div class="p-6 text-center">
            <!-- Ikon Error -->
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Akun Ditemukan</h3>
            
            <p class="text-gray-600 mb-6">
                NIK ini sudah terdaftar atas nama <strong id="modalPatientName" class="text-gray-900">...</strong> dan telah memiliki akun.
            </p>
            
            <p class="text-gray-600 mb-6">Silakan login untuk melanjutkan.</p>

            <div class="flex justify-center gap-4">
                <button id="closeModalBtn" type="button" class="py-2 px-6 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
                <a href="{{ route('login') }}" id="loginModalBtn" class="py-2 px-8 bg-brand-primary text-white font-semibold rounded-lg hover:opacity-90 transition-opacity">
                    Login
                </a>
            </div>
        </div>
    </div>
</div>
{{-- Akhir Modal --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Konstanta Form ---
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const messageElement = document.getElementById('passwordMatchMessage');
    const nikInput = document.getElementById('nik');
    const nameInput = document.getElementById('full_name');
    const dobInput = document.getElementById('date_of_birth');
    const genderSelect = document.getElementById('gender');
    const nikStatusIndicator = document.getElementById('nik_status_indicator');
    const nikMessage = document.getElementById('nik_message');
    const emailInput = document.getElementById('email');

    // --- Konstanta Modal ---
    const accountExistsModal = document.getElementById('accountExistsModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalPanel = document.querySelector('.modal-panel');
    const modalPatientName = document.getElementById('modalPatientName');
    const closeModalBtn = document.getElementById('closeModalBtn');
    let fetchNikTimer; // Timer untuk debounce

    // --- Fungsi Modal ---
    function openModal(patientName) {
        modalPatientName.textContent = patientName;
        accountExistsModal.classList.remove('hidden');
        // Trigger transisi
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalPanel.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal() {
        // Trigger transisi keluar
        modalBackdrop.classList.add('opacity-0');
        modalPanel.classList.add('opacity-0', 'scale-95');
        // Sembunyikan setelah transisi selesai
        setTimeout(() => {
            accountExistsModal.classList.add('hidden');
        }, 300); // 300ms = durasi transisi
    }

    // Event listener untuk menutup modal
    closeModalBtn.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);

    // --- Logika Password Matcher ---
    function validatePassword() {
        messageElement.classList.remove('text-green-600', 'text-red-600');
        if (confirmPasswordInput.value === '') { messageElement.textContent = ''; return; }
        if (passwordInput.value === confirmPasswordInput.value) {
            messageElement.textContent = 'Password cocok!';
            messageElement.classList.add('text-green-600');
        } else {
            messageElement.textContent = 'Password tidak cocok.';
            messageElement.classList.add('text-red-600');
        }
    }
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);

    // --- Logika NIK Auto-fill ---

    // Fungsi untuk mengunci/membuka form data pasien
    function setPatientFormReadOnly(isReadOnly) {
        nameInput.readOnly = isReadOnly;
        dobInput.readOnly = isReadOnly;
        genderSelect.disabled = isReadOnly;
        
        [nameInput, dobInput, genderSelect].forEach(el => {
            if (isReadOnly) {
                el.classList.add('bg-gray-200', 'text-gray-500');
                el.classList.remove('bg-gray-50');
            } else {
                el.classList.remove('bg-gray-200', 'text-gray-500');
                el.classList.add('bg-gray-50');
            }
        });
    }

    // Fungsi untuk mereset form data akun
    function resetAccountForm() {
        emailInput.value = '';
        passwordInput.value = '';
        confirmPasswordInput.value = '';
        messageElement.textContent = '';
        messageElement.classList.remove('text-green-600', 'text-red-600');
    }

    // Fungsi untuk mereset form data pasien
    function resetPatientForm() {
        nameInput.value = '';
        dobInput.value = '';
        genderSelect.value = '';
        nikMessage.classList.add('hidden');
        nikMessage.textContent = '';
        setPatientFormReadOnly(false); // Buka kunci form
    }

    // Set form ke non-readonly saat halaman dimuat
    setPatientFormReadOnly(false);
    // Jika ada input lama (dari error validasi), biarkan form terbuka dan isi email
    if (nameInput.value && '{{ old('full_name') }}') {
         setPatientFormReadOnly(false);
         emailInput.value = '{{ old('email') }}';
    } else {
        // Pastikan form akun kosong saat pertama kali memuat
        resetAccountForm();
    }


    nikInput.addEventListener('input', function() {
        clearTimeout(fetchNikTimer);
        const nik = this.value;

        nikStatusIndicator.className = 'nik-status-indicator';
        nikMessage.classList.add('hidden');
        nikMessage.textContent = ''; // Hapus pesan NIK sebelumnya

        if (nik.length !== 16) {
            if (nameInput.readOnly) {
                 resetPatientForm();
            }
            return;
        }

        nikStatusIndicator.className = 'nik-status-indicator loading';
        setPatientFormReadOnly(true); // Kunci form sementara loading
        
        // Reset form akun SETIAP kali NIK 16 digit diketik
        resetAccountForm();

        fetchNikTimer = setTimeout(() => {
            let url = '{{ route('check-patient-nik-public', ['nik' => ':nik']) }}';
            url = url.replace(':nik', nik);

            fetch(url)
                .then(response => {
                    if (!response.ok) { throw new Error('Respon server tidak baik.'); }
                    return response.json();
                })
                .then(data => {
                    if (data.found) {
                        if (data.has_account) {
                            // Skenario 1: NIK Ditemukan TAPI SUDAH PUNYA AKUN
                            nikStatusIndicator.className = 'nik-status-indicator error';
                            nikStatusIndicator.innerHTML = '&#10005;'; // X mark
                            
                            openModal(data.data.full_name); // Panggil modal
                            
                            // Isi form dengan data, lalu kunci
                            nameInput.value = data.data.full_name;
                            dobInput.value = data.data.date_of_birth;
                            genderSelect.value = data.data.gender;
                            setPatientFormReadOnly(true); // Kunci form

                        } else {
                            // Skenario 2: NIK Ditemukan & BELUM PUNYA AKUN (Pasien walk-in)
                            nikStatusIndicator.className = 'nik-status-indicator success';
                            nikStatusIndicator.innerHTML = '&#10003;'; // Checkmark
                            
                            nikMessage.textContent = 'Data pasien ditemukan. Silakan lengkapi email & password.';
                            nikMessage.classList.remove('hidden', 'text-red-600');
                            nikMessage.classList.add('text-green-600');

                            nameInput.value = data.data.full_name;
                            dobInput.value = data.data.date_of_birth;
                            genderSelect.value = data.data.gender;
                            
                            setPatientFormReadOnly(true); // Kunci form karena data ada
                        }
                    } else {
                        // Skenario 3: NIK Tidak Ditemukan
                        nikStatusIndicator.className = 'nik-status-indicator';
                        
                        nikMessage.textContent = 'NIK tidak ditemukan. Silakan lengkapi data diri Anda.';
                        nikMessage.classList.remove('hidden', 'text-red-600', 'text-green-600');
                        nikMessage.classList.add('text-blue-600');

                        resetPatientForm(); // Ini akan mengosongkan form pasien
                        setPatientFormReadOnly(false); // Buka kunci form
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    nikStatusIndicator.className = 'nik-status-indicator error';
                    nikStatusIndicator.innerHTML = '&#10005;'; // X mark
                    
                    nikMessage.textContent = 'Gagal mengambil data. Coba lagi.';
                    nikMessage.classList.remove('hidden');
                    nikMessage.classList.add('text-red-600');
                    
                    setPatientFormReadOnly(false); // Buka kunci form
                });
        }, 500);
    });
});
</script>
@endsection