<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator; // Pastikan ini ada

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form registrasi untuk pengguna baru.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses data dari form registrasi.
     * [MODIFIKASI PERMINTAAN 1: Logika Pengikatan Akun]
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            // [MODIFIKASI] Hapus 'unique:patients,nik'
            // Kita akan cek NIK secara manual
            'nik' => 'required|string|size:16',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'date_of_birth' => 'required|date',
            // [MODIFIKASI] Hapus old('email') dari view, jadi unique check di sini
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'terms' => 'accepted'
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            // 'nik.unique' dihapus
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan.'
        ]);

        DB::beginTransaction();
        try {
            // [MODIFIKASI] Cek NIK di tabel Patient SEBELUM membuat User
            $existingPatient = Patient::where('nik', $validatedData['nik'])->first();

            // Skenario 1: NIK ada DAN user_id sudah terisi (akun sudah terdaftar)
            if ($existingPatient && $existingPatient->user_id) {
                DB::rollBack();
                // Kembalikan error spesifik untuk NIK
                return redirect()->back()->withInput()->withErrors([
                    'nik' => 'NIK ini sudah terdaftar dan terhubung ke akun lain. Silakan login.'
                ]);
            }

            // Skenario 2 & 3: NIK belum ada, ATAU NIK ada tapi user_id = NULL (pasien walk-in)
            // Lanjutkan membuat User
            $user = User::create([
                'full_name' => strtoupper($validatedData['full_name']),
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Berikan role 'pasien'
            $user->assignRole('pasien');

            // [MODIFIKASI] Logika untuk Patient
            if ($existingPatient) {
                // Skenario 2: NIK ada, user_id = NULL (Pasien walk-in)
                // Update record Patient yang ada dengan user_id baru
                // Pastikan data yang di-input sesuai dengan data yang di-fetch
                if (
                    strtoupper($existingPatient->full_name) !== strtoupper($validatedData['full_name']) ||
                    $existingPatient->date_of_birth !== $validatedData['date_of_birth'] ||
                    $existingPatient->gender !== $validatedData['gender']
                ) {
                    // Jika data tidak cocok (misal user ganti value via dev tools)
                    DB::rollBack();
                    return redirect()->back()->withInput()->withErrors([
                        'nik' => 'Data diri (Nama/Tgl. Lahir/Gender) tidak cocok dengan NIK yang ditemukan. Mohon refresh halaman dan coba lagi.'
                    ]);
                }
                
                $existingPatient->update([
                    'user_id' => $user->id,
                    // Data lain sudah dipastikan cocok
                ]);
                Log::info('Akun user baru (ID: ' . $user->id . ') berhasil ditautkan ke pasien lama (ID: ' . $existingPatient->id . ') via NIK.');
            } else {
                // Skenario 3: NIK belum ada (Pasien baru murni)
                // Buat record Patient baru
                Patient::create([
                    'user_id' => $user->id,
                    'full_name' => strtoupper($validatedData['full_name']),
                    'nik' => $validatedData['nik'],
                    'gender' => $validatedData['gender'],
                    'date_of_birth' => $validatedData['date_of_birth'],
                ]);
            }

            DB::commit(); // Perbaikan: DB::commit() harus ada tanda kurung

            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan Registrasi: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.']);
        }
    }

    

    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses data dari form login.
     * * [!!! INI ADALAH BAGIAN YANG DIMODIFIKASI !!!]
     * */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // === AWAL MODIFIKASI ===
            
            // Ambil user yang baru saja login
            $user = Auth::user();

            // Cek role user
            // Saya asumsikan Anda pakai Spatie/laravel-permission 
            // karena ada 'assignRole' di fungsi register-mu
            
            if ($user->hasRole('admin')) {
                // 1. Jika role adalah 'admin', lempar ke dashboard Filament
                // URL '/admin' diubah menjadi path baru '/filament'
                return redirect('/filament'); // <-- PERUBAHAN DI SINI
            
            } elseif ($user->hasRole('dokter')) {
                // 2. Jika 'dokter', arahkan ke dashboard dokter
                // Ganti '/dashboard-dokter' jika URL-nya beda
                return redirect('/dashboard-dokter'); // Ganti URL sesuai kebutuhan

            } elseif ($user->hasRole('loket')) { // Kamu sebut 'petugas loket'
                // 3. Jika 'loket', arahkan ke dashboard loket
                // Ganti '/dashboard-loket' jika URL-nya beda
                return redirect('/dashboard-loket'); // Ganti URL sesuai kebutuhan

            } elseif ($user->hasRole('pasien')) {
                // 4. Jika 'pasien', arahkan ke dashboard pasien
                // Ganti '/dashboard-pasien' jika URL-nya beda
                return redirect('/dashboard-pasien'); // Ganti URL sesuai kebutuhan
            
            } else {
                // 5. Fallback jika user punya role lain atau tidak punya role
                // Kembali ke logika asal: redirect ke 'dashboard'
                return redirect()->intended('dashboard');
            }
            
            // === AKHIR MODIFIKASI ===
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // [MODIFIKASI] Method baru untuk cek NIK publik
    /**
     * Mengecek data pasien berdasarkan NIK untuk auto-fill form registrasi publik.
     */
    public function checkPatientPublic($nik)
    {
        // 1. Validasi NIK
        $validator = Validator::make(['nik' => $nik], [
            'nik' => 'required|string|digits:16'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'NIK tidak valid.'], 400);
        }

        // 2. Cari pasien
        $patient = Patient::where('nik', $nik)->first();

        if (!$patient) {
            // Skenario 1: NIK tidak ditemukan
            return response()->json(['found' => false]);
        }

        // Data pasien untuk dikirim
        $patientData = [
            'full_name' => $patient->full_name,
            'date_of_birth' => $patient->date_of_birth,
            'gender' => $patient->gender,
        ];

        if ($patient->user_id) {
            // Skenario 2: NIK ada DAN sudah punya akun
            // [MODIFIKASI] Tetap kirim data pasien untuk ditampilkan di modal
            return response()->json([
                'found' => true,
                'has_account' => true,
                'data' => $patientData
            ]);
        }

        // Skenario 3: NIK ada DAN belum punya akun (pasien walk-in)
        return response()->json([
            'found' => true,
            'has_account' => false,
            'data' => $patientData
        ]);
    }
}
