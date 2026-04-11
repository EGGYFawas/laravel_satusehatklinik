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
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; 
use Illuminate\Auth\Events\PasswordReset; 
use App\Services\SatuSehatService; // [BARU] Wajib di-import untuk cek NIK Kemenkes

class AuthController extends Controller
{
    // =========================================================================
    // 1. BAGIAN REGISTRASI (DENGAN LOGIKA NIK & EVENT VERIFIKASI)
    // =========================================================================

    /**
     * Menampilkan halaman form registrasi untuk pengguna baru.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses data dari form registrasi.
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            'nik' => 'required|string|size:16',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'date_of_birth' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRules::min(6)],
            'terms' => 'accepted',
            'ihs_number' => 'nullable|string' // [BARU] Menangkap ID SatuSehat dari form hidden
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan.'
        ]);

        DB::beginTransaction();
        try {
            $existingPatient = Patient::where('nik', $validatedData['nik'])->first();

            // Cek jika NIK sudah punya akun User
            if ($existingPatient && $existingPatient->user_id) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors([
                    'nik' => 'NIK ini sudah terdaftar dan terhubung ke akun lain. Silakan login.'
                ]);
            }

            // Buat User Baru
            $user = User::create([
                'full_name' => strtoupper($validatedData['full_name']),
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Assign Role Pasien
            $user->assignRole('pasien'); 

            // Logika Linking NIK ke Patient Data
            if ($existingPatient) {
                // Validasi kecocokan data input vs data database lama
                if (
                    strtoupper($existingPatient->full_name) !== strtoupper($validatedData['full_name']) ||
                    $existingPatient->date_of_birth !== $validatedData['date_of_birth'] ||
                    $existingPatient->gender !== $validatedData['gender']
                ) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->withErrors([
                        'nik' => 'Data diri (Nama/Tgl. Lahir/Gender) tidak cocok dengan NIK yang ditemukan. Mohon refresh halaman dan coba lagi.'
                    ]);
                }
                
                // Jika data lama belum ada IHS Number tapi di form ada (hasil tembak API baru), update sekalian
                $updateData = ['user_id' => $user->id];
                if (empty($existingPatient->ihs_number) && !empty($validatedData['ihs_number'])) {
                    $updateData['ihs_number'] = $validatedData['ihs_number'];
                }

                $existingPatient->update($updateData);

                Log::info('Akun user baru (ID: ' . $user->id . ') berhasil ditautkan ke pasien lama (ID: ' . $existingPatient->id . ') via NIK.');
            } else {
                // Jika pasien benar-benar baru (Skenario dari SatuSehat atau Manual Baru)
                Patient::create([
                    'user_id' => $user->id,
                    'full_name' => strtoupper($validatedData['full_name']),
                    'nik' => $validatedData['nik'],
                    'gender' => $validatedData['gender'],
                    'date_of_birth' => $validatedData['date_of_birth'],
                    'ihs_number' => $validatedData['ihs_number'] ?? null, // [BARU] Simpan IHS Number
                ]);
            }

            DB::commit();

            // Memicu event Registered agar email verifikasi terkirim
            event(new Registered($user));

            // Redirect ke login dengan pesan cek email
            return redirect()->route('login')
                ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun sebelum login.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan Registrasi: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.']);
        }
    }

    // =========================================================================
    // 2. BAGIAN LOGIN (DENGAN REMEMBER ME & CEK VERIFIKASI)
    // =========================================================================

    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses data dari form login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Ambil value checkbox "remember" dari form login
        $remember = $request->has('remember') ? true : false;

        // Tambahkan parameter $remember ke Auth::attempt
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek apakah email sudah diverifikasi?
            if (!$user->hasVerifiedEmail()) {
                // Jika belum, jangan izinkan akses dashboard, lempar ke halaman notice
                return redirect()->route('verification.notice')
                    ->with('warning', 'Email Anda belum diverifikasi. Silakan cek inbox email Anda.');
            }

            // Logika Redirect Berdasarkan Role (Klinik Logic)
            if ($user->hasRole('admin')) {
                return redirect('/admin'); 
            } elseif ($user->hasRole('dokter')) {
                return redirect()->route('dokter.dashboard');
            } elseif ($user->hasRole('petugas loket')) {
                return redirect()->route('petugas-loket.dashboard');
            } elseif ($user->hasRole('pasien')) {
                return redirect()->route('pasien.dashboard');
            } else {
                return redirect()->intended('dashboard');
            }
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
        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    // =========================================================================
    // 3. BAGIAN CEK NIK (AJAX UNTUK FRONTEND & SATUSEHAT INJECTION)
    // =========================================================================

    /**
     * Mengecek data pasien berdasarkan NIK untuk auto-fill form registrasi publik.
     */
    public function checkPatientPublic($nik)
    {
        $validator = Validator::make(['nik' => $nik], [
            'nik' => 'required|string|digits:16'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'NIK tidak valid.'], 400);
        }

        // 1. CEK KE DATABASE LOKAL DULU (Data Walk-in Loket)
        $patient = Patient::where('nik', $nik)->first();

        if ($patient) {
            $patientData = [
                'full_name' => $patient->full_name,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'ihs_number' => $patient->ihs_number ?? null // Ambil IHS jika sudah ada
            ];

            return response()->json([
                'found' => true,
                'is_satusehat' => false, // Flag bahwa ini data lokal
                'has_account' => $patient->user_id ? true : false,
                'data' => $patientData
            ]);
        }

        // 2. JIKA LOKAL TIDAK ADA, TEMBAK API SATUSEHAT KEMENKES!
        // Pastikan class SatuSehatService sudah lo buat di app/Services/SatuSehatService.php
        $satuSehat = new SatuSehatService();
        $kemenkesResponse = $satuSehat->getPatientByNIK($nik);

        if ($kemenkesResponse['success']) {
            $satusehatData = $kemenkesResponse['data'];

            // Format gender Kemenkes (male/female) ke format DB kita
            $genderLokal = '';
            if (strtolower($satusehatData['gender']) == 'male') $genderLokal = 'Laki-laki';
            if (strtolower($satusehatData['gender']) == 'female') $genderLokal = 'Perempuan';

            return response()->json([
                'found' => true,
                'is_satusehat' => true, // Flag penting buat merubah warna teks di Frontend
                'has_account' => false, // Dari Kemenkes pasti belum punya akun di web klinik kita
                'data' => [
                    'full_name' => strtoupper($satusehatData['name']),
                    'date_of_birth' => $satusehatData['birthDate'],
                    'gender' => $genderLokal,
                    'ihs_number' => $satusehatData['ihs_number'] // Wajib dikirim ke view
                ]
            ]);
        }

        // 3. JIKA LOKAL KOSONG & KEMENKES KOSONG / ERROR
        return response()->json([
            'found' => false,
            'message' => $kemenkesResponse['message'] ?? 'Data tidak ditemukan.'
        ]);
    }

    // =========================================================================
    // 4. BAGIAN BARU: LUPA PASSWORD & VERIFIKASI EMAIL
    // =========================================================================

    // --- LUPA PASSWORD (Kirim Link) ---
    
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Kirim link reset pakai SMTP Gmail yang sudah disetting
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // --- [BARU] RESET PASSWORD (Proses Update Password) ---
    
    public function resetPassword(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::min(6)->letters()->numbers()],
        ]);

        // 2. Proses Reset Password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // 3. Redirect jika sukses
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // --- VERIFIKASI EMAIL MANUAL ---

    public function showVerificationNotice()
    {
        // Tampilkan view yang meminta user cek email
        return view('auth.verify'); 
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('dashboard');
        }

        // Kirim ulang email verifikasi
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Link verifikasi baru telah dikirim ke email Anda!');
    }
}