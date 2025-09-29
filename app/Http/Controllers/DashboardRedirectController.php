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

        // Gunakan hasRole() dari Spatie untuk memeriksa peran pengguna
        if ($user->hasRole('admin')) {
            // BENAR: Menggunakan nama rute 'admin.dashboard'
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('dokter')) {
            // Anda perlu mendefinisikan rute dengan nama 'dokter.dashboard'
            return redirect()->route('dokter.dashboard');
        }

        if ($user->hasRole('petugas loket apotek')) {
             // Anda perlu mendefinisikan rute dengan nama 'apotek.dashboard'
            return redirect()->route('apotek.dashboard');
        }

        if ($user->hasRole('pasien')) {
            // BENAR: Menggunakan nama rute 'pasien.dashboard'
            return redirect()->route('pasien.dashboard');
        }

        // Fallback: Jika user login tapi tidak punya role yang dikenali, logout saja.
        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Anda tidak memiliki hak akses yang valid.']);
    }
}