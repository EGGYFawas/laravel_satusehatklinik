<?php

namespace App\Http\Controllers;

use App\Models\Doctor; // 1. Impor Model Doctor Anda
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str; // Kita akan butuh ini di view, tapi impor di sini bagus

class LandingPageController extends Controller
{
    /**
     * Menampilkan halaman landing page.
     */
    public function index(): View
    {
        // 2. Ambil semua dokter yang PUNYA JADWAL
        //    Kita juga 'eager load' relasi 'user' (untuk nama) 
        //    dan 'doctorSchedules' (untuk jadwal)
        $doctorsWithSchedules = Doctor::has('doctorSchedules')
                                    ->with('user', 'doctorSchedules')
                                    ->get();

        // 3. Kirim data dokter ke view 'landing'
        return view('landing', [
            'doctors' => $doctorsWithSchedules
        ]);
    }
}