<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahPasien = \App\Models\Pasien::whereDate('created_at', Carbon::today())->count();



        // Menghitung jumlah pendaftaran dengan jnsPeserta = 1 (BPJS) untuk hari ini
        $jumlahPasienBpjs = \App\Models\Pendaftaran::whereHas('pasien', function ($query) {
            $query->where('jnsPeserta', 1);
        })->whereDate('created_at', Carbon::today())->count();

        // Menghitung jumlah pendaftaran dengan jnsPeserta = 2 (Umum) untuk hari ini
        $jumlahPasienUmum = \App\Models\Pendaftaran::whereHas('pasien', function ($query) {
            $query->where('jnsPeserta', 2);
        })->whereDate('created_at', Carbon::today())->count();


        return view('dashboard.dashboard', compact('jumlahPasien', 'jumlahPasienBpjs', 'jumlahPasienUmum'));
    }
}
