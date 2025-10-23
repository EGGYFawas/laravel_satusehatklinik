<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use App\Models\Poli;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Pastikan DB facade di-import

class ScheduleController extends Controller
{
    /**
     * Menampilkan halaman jadwal dokter untuk pasien.
     */
    public function index()
    {
        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        // Ambil semua data jadwal aktif
        $schedules = DoctorSchedule::where('is_active', true)
                        ->with(['doctor.user', 'doctor.poli']) // Eager load relasi
                        // [MODIFIKASI] Urutkan berdasarkan hari standar, lalu jam mulai
                        ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                        ->orderBy('start_time')
                        ->get();

        // Daftar hari dalam Bahasa Indonesia
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Kelompokkan jadwal berdasarkan hari untuk tampilan mingguan
        $groupedSchedules = $schedules->groupBy('day_of_week')->sortBy(function ($item, $key) use ($daysOrder) {
            return array_search($key, $daysOrder);
        });

        // [PENAMBAHAN BARU] Logika untuk Jadwal Hari Ini
        $now = Carbon::now(config('app.timezone')); // Waktu saat ini sesuai timezone aplikasi
        $todayName = $now->translatedFormat('l'); // Nama hari ini (e.g., "Kamis")
        
        // Filter jadwal hanya untuk hari ini
        $todaySchedules = $schedules->filter(function ($schedule) use ($todayName) {
            return $schedule->day_of_week === $todayName;
        });

        // Ambil daftar poli untuk filter (jika diperlukan)
        $polis = Poli::orderBy('name')->get();

        return view('pasien.jadwal-dokter', compact(
            'groupedSchedules', 
            'polis', 
            'daysOrder',
            'now', // Kirim waktu saat ini
            'todayName', // Kirim nama hari ini
            'todaySchedules' // Kirim jadwal hari ini
        ));
    }
}

