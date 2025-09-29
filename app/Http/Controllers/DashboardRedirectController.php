<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            // Arahkan ke dashboard admin
            // Contoh: return redirect()->route('admin.dashboard');
            return redirect('/admin/dashboard'); // Sesuaikan dengan nama route Anda
        }

        if ($user->hasRole('dokter')) {
            // Arahkan ke dashboard dokter
            // Contoh: return redirect()->route('doctor.dashboard');
            return redirect('/doctor/dashboard'); // Sesuaikan dengan nama route Anda
        }

        if ($user->hasRole('petugas loket apotek')) {
            // Arahkan ke dashboard apotek
            // Contoh: return redirect()->route('pharmacy.dashboard');
            return redirect('/pharmacy/dashboard'); // Sesuaikan dengan nama route Anda
        }

        if ($user->hasRole('pasien')) {
            // Arahkan ke dashboard pasien
            // Contoh: return redirect()->route('pasien.dashboard');
            return redirect('/pasien/dashboard'); // Sesuaikan dengan nama route Anda
        }

        // Fallback: Jika user login tapi tidak punya role yang dikenali, logout saja.
        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Anda tidak memiliki hak akses yang valid.']);
    }
}

