<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Arahkan pengguna ke dashboard yang sesuai berdasarkan role mereka.
     */
    public function index()
    {
        $user = Auth::user();

        // UBAH: Dari 'admin' menjadi 'super_admin' (Sesuai DatabaseSeeder)
        if ($user->hasRole('super_admin')) {
            return redirect('/admin'); 
        }

        // AMAN: Role 'dokter' sudah sesuai seeder
        if ($user->hasRole('dokter')) {
            return redirect()->route('dokter.dashboard');
        }

        // AMAN: Role 'petugas loket' (pakai spasi) sudah sesuai seeder
        if ($user->hasRole('petugas loket')) {
            return redirect()->route('petugas-loket.dashboard');
        }

        // AMAN: Role 'pasien' sudah sesuai seeder
        if ($user->hasRole('pasien')) {
            return redirect()->route('pasien.dashboard');
        }

        // Fallback: Jika user login tapi tidak punya role yang dikenali, logout saja.
        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Anda tidak memiliki hak akses yang valid.']);
    }
}