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
        // 1. Validasi input dengan nama field yang sudah konsisten
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            'nik' => 'required|string|size:16|unique:patients,nik',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'date_of_birth' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'terms' => 'accepted'
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar di sistem.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan.'
        ]);

        DB::beginTransaction();
        try {
            // 2. Buat entri di tabel 'users'
            $user = User::create([
                'full_name' => strtoupper($validatedData['full_name']),
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // 3. Berikan role 'pasien' ke user baru
            // Pastikan Anda sudah membuat role 'pasien' di database.
            $user->assignRole('pasien');

            // 4. Buat entri di tabel 'patients'
            Patient::create([
                'user_id' => $user->id,
                'nik' => $validatedData['nik'],
                'gender' => $validatedData['gender'],
                'date_of_birth' => $validatedData['date_of_birth'],
            ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk debugging internal
            Log::error('Kesalahan Registrasi: ' . $e->getMessage());
            // Beri pesan error yang ramah ke pengguna
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.']);
        }
    }

    /**
     * Menampilkan halaman form login.
     * Ini adalah method yang menyebabkan error Anda.
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
            // Arahkan ke "pintu gerbang" yang akan diatur oleh DashboardRedirectController
            return redirect()->intended('dashboard');
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
}

