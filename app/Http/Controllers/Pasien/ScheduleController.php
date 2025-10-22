<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use App\Models\Poli;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Menampilkan halaman jadwal dokter untuk pasien.
     */
    public function index()
    {
        // Ambil semua data jadwal aktif
        $schedules = DoctorSchedule::where('is_active', true)
                        ->with(['doctor.user', 'doctor.poli']) // Eager load relasi dokter dan poli
                        ->orderBy('day_of_week') // Urutkan berdasarkan hari
                        ->orderBy('start_time')  // Kemudian berdasarkan jam mulai
                        ->get();

        // Daftar hari dalam Bahasa Indonesia
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Kelompokkan jadwal berdasarkan hari
        $groupedSchedules = $schedules->groupBy('day_of_week')->sortBy(function ($item, $key) use ($daysOrder) {
            return array_search($key, $daysOrder); // Urutkan grup sesuai $daysOrder
        });

        // Ambil daftar poli untuk filter (jika diperlukan)
        $polis = Poli::orderBy('name')->get();

        return view('pasien.jadwal-dokter', compact('groupedSchedules', 'polis', 'daysOrder'));
    }
}
