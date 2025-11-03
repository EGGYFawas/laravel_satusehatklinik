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
use Illuminate\Support\Facades\Validator;

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
            'password' => ['required', 'confirmed', Password::min(6)],
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

            if ($existingPatient && $existingPatient->user_id) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors([
                    'nik' => 'NIK ini sudah terdaftar dan terhubung ke akun lain. Silakan login.'
                ]);
            }

            $user = User::create([
                'full_name' => strtoupper($validatedData['full_name']),
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                // [MODIFIKASI] Role 'pasien' ditambahkan di controller register
                // 'role' => 'pasien' // Jika Anda tidak pakai Spatie, pastikan kolom role ada
            ]);

            // Asumsi Anda pakai Spatie karena assignRole
            $user->assignRole('pasien'); 

            if ($existingPatient) {
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
                Patient::create([
                    'user_id' => $user->id,
                    'full_name' => strtoupper($validatedData['full_name']),
                    'nik' => $validatedData['nik'],
                    'gender' => $validatedData['gender'],
                    'date_of_birth' => $validatedData['date_of_birth'],
                ]);
            }

            DB::commit();

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
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // [MODIFIKASI UTAMA] Menggunakan route() helper, bukan URL hardcode
            
            if ($user->hasRole('admin')) {
                // 1. Admin ke dashboard Filament (ini sudah benar)
                return redirect('/admin'); 
            
            } elseif ($user->hasRole('dokter')) {
                // 2. Dokter ke dashboard dokter
                return redirect()->route('dokter.dashboard'); // Bukan /dashboard-dokter

            } elseif ($user->hasRole('petugas loket')) { // [MODIFIKASI] Menyesuaikan nama role
                // 3. Petugas Loket ke dashboard petugas loket
                return redirect()->route('petugas-loket.dashboard'); // Bukan /dashboard-loket

            } elseif ($user->hasRole('pasien')) {
                // 4. Pasien ke dashboard pasien
                return redirect()->route('pasien.dashboard'); // Bukan /dashboard-pasien
            
            } else {
                // 5. Fallback
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
        return redirect('/');
    }

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
}
