<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import User model
use App\Models\Doctor; // Import Doctor model

class ScheduleController extends Controller
{
    /**
     * Menampilkan jadwal praktik dokter yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        
        // [MODIFIKASI UTAMA] Memastikan relasi doctor ada
        // Opsi 1: Menggunakan firstOrFail pada query terpisah (lebih jelas)
        $doctor = Doctor::where('user_id', $user->id)->first();

        // Jika data dokter tidak ditemukan untuk user ini, tampilkan error
        if (!$doctor) {
            // Anda bisa menampilkan pesan error yang lebih ramah pengguna
            // atau redirect ke halaman lain dengan pesan error.
            // abort(500, 'Data dokter tidak ditemukan untuk pengguna ini.'); 
             return redirect()->route('dokter.dashboard')
                          ->with('error', 'Tidak dapat memuat jadwal. Data dokter tidak ditemukan.');
        }

        // --- Mulai dari sini, kita yakin $doctor adalah objek Doctor yang valid ---

        // Ambil jadwal dokter ini saja
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
                        ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')") // Urutkan hari dengan benar
                        ->orderBy('start_time')
                        ->get();

        // Daftar hari dalam Bahasa Indonesia
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Kelompokkan jadwal berdasarkan hari
        // Pastikan $schedules tidak kosong sebelum groupBy
        $groupedSchedules = $schedules->isNotEmpty() ? 
                            $schedules->groupBy('day_of_week')->sortBy(function ($item, $key) use ($daysOrder) {
                                return array_search($key, $daysOrder);
                            }) : collect(); // Kembalikan koleksi kosong jika tidak ada jadwal

        return view('dokter.jadwal-saya', compact('groupedSchedules', 'daysOrder', 'doctor'));
    }

    // Nanti bisa ditambahkan method store, update, destroy jika dokter bisa mengatur jadwalnya sendiri
}

