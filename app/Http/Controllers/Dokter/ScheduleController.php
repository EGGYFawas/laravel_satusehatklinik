<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model
use App\Models\Doctor; // Import Doctor model
use Carbon\Carbon; // Import Carbon

class ScheduleController extends Controller
{
    /**
     * Menampilkan jadwal praktik dokter yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Menggunakan firstOrFail untuk penanganan error yang lebih baik jika dokter tidak ditemukan
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        // Ambil jadwal dokter ini saja yang aktif
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
                        ->where('is_active', true) // Hanya ambil jadwal yang aktif
                        ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                        ->orderBy('start_time')
                        ->get();

        // Daftar hari dalam Bahasa Indonesia
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Kelompokkan jadwal berdasarkan hari
        $groupedSchedules = $schedules->isNotEmpty() ? 
                            $schedules->groupBy('day_of_week')->sortBy(function ($item, $key) use ($daysOrder) {
                                return array_search($key, $daysOrder);
                            }) : collect();

        // Variabel untuk mengecek apakah ada jadwal sama sekali
        $hasAnySchedule = $schedules->isNotEmpty();

        return view('dokter.jadwal-saya', compact('groupedSchedules', 'daysOrder', 'doctor', 'hasAnySchedule'));
    }

    // Nanti bisa ditambahkan method store, update, destroy jika dokter bisa mengatur jadwalnya sendiri
}

