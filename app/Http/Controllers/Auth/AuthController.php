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
use Illuminate\Support\Str; // [BARU] Wajib untuk generate token
use Illuminate\Auth\Events\PasswordReset; // [BARU] Event reset password

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
            'terms' => 'accepted'
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
                
                $existingPatient->update([
                    'user_id' => $user->id,
                ]);
                Log::info('Akun user baru (ID: ' . $user->id . ') berhasil ditautkan ke pasien lama (ID: ' . $existingPatient->id . ') via NIK.');
            } else {
                // Jika pasien benar-benar baru
                Patient::create([
                    'user_id' => $user->id,
                    'full_name' => strtoupper($validatedData['full_name']),
                    'nik' => $validatedData['nik'],
                    'gender' => $validatedData['gender'],
                    'date_of_birth' => $validatedData['date_of_birth'],
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
    // 3. BAGIAN CEK NIK (AJAX UNTUK FRONTEND)
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

        $patient = Patient::where('nik', $nik)->first();

        if (!$patient) {
            return response()->json(['found' => false]);
        }

        $patientData = [
            'full_name' => $patient->full_name,
            'date_of_birth' => $patient->date_of_birth,
            'gender' => $patient->gender,
        ];

        if ($patient->user_id) {
            return response()->json([
                'found' => true,
                'has_account' => true,
                'data' => $patientData
            ]);
        }

        return response()->json([
            'found' => true,
            'has_account' => false,
            'data' => $patientData
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
    // Method ini yang tadi hilang dan menyebabkan error
    
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
        // Tampilkan view yang meminta user cek email (resources/views/auth/verify.blade.php)
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