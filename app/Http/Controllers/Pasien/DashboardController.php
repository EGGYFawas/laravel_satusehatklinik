<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pasien beserta data antrean dan list poli.
     */
    public function index()
    {
        $user = Auth::user();
        // Cari data pasien berdasarkan user_id yang sedang login.
        $patient = Patient::where('user_id', $user->id)->first();
        
        $today = Carbon::today()->toDateString();
        $antreanBerobat = null;

        // Hanya cari antrean jika data pasien ditemukan.
        if ($patient) {
            $antreanBerobat = ClinicQueue::with('poli') // Eager load relasi poli
                ->where('patient_id', $patient->id)
                ->whereDate('registration_time', $today)
                ->whereIn('status', ['MENUNGGU', 'DIPANGGIL']) // Status sesuai ERD yang masih aktif
                ->first();
        }

        // Ambil semua data poli untuk ditampilkan di dropdown form.
        $polis = Poli::orderBy('name', 'asc')->get();

        // Kirim semua data yang diperlukan ke view, termasuk data pasien
        return view('pasien.dashboard', compact('user', 'patient', 'antreanBerobat', 'polis'));
    }

    /**
     * Menyimpan antrean klinik baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form modal
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chief_complaint' => 'required|string|min:5|max:255',
            'registration_date' => 'required|date',
            // 'patient_relationship' => 'required|string', // Sesuaikan jika field ini masih digunakan
        ]);

        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        // Keamanan: Pastikan data pasien ada sebelum membuat antrean.
        if (!$patient) {
            return redirect()->back()->with('error', 'Data profil pasien tidak ditemukan. Silakan lengkapi profil Anda terlebih dahulu.');
        }
        
        $registrationDate = Carbon::parse($request->registration_date)->toDateString();
        
        // Cek apakah pasien sudah memiliki antrean aktif pada hari yang sama.
        $existingAntrean = ClinicQueue::where('patient_id', $patient->id)
            ->whereDate('registration_time', $registrationDate)
            ->whereIn('status', ['MENUNGGU', 'DIPANGGIL']) // Cek status yang masih aktif
            ->exists();
            
        if ($existingAntrean) {
             return redirect()->back()->with('error', 'Anda sudah terdaftar dalam antrean untuk hari ini.');
        }

        // Logika untuk membuat nomor antrean baru
        $poli = Poli::findOrFail($request->poli_id);
        $poliCode = $poli->code;
        $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
        $queueNumber = $poliCode . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

        // Buat record baru di tabel clinic_queues
        ClinicQueue::create([
            'patient_id' => $patient->id,
            'poli_id' => $request->poli_id,
            'doctor_id' => $request->doctor_id,
            'registered_by_user_id' => $user->id,
            'queue_number' => $queueNumber,
            'chief_complaint' => $request->chief_complaint,
            'patient_relationship' => 'Diri Sendiri', // Default atau ambil dari form jika ada
            'status' => 'MENUNGGU',
            'registration_time' => Carbon::parse($registrationDate . ' ' . now()->format('H:i:s')), // Gabungkan tanggal dari form dan waktu saat ini
        ]);

        return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil!');
    }

    /**
     * API untuk mengambil data dokter berdasarkan poli_id dan jadwal praktek hari ini.
     */
    public function getDoctorsByPoli($poli_id)
    {
        // Set locale Carbon ke Indonesia untuk mendapatkan nama hari yang benar
        Carbon::setLocale('id');
        $dayName = ucfirst(Carbon::now()->dayName); // Hasilnya: 'Senin', 'Selasa', dst.

        $doctors = Doctor::where('poli_id', $poli_id)
            ->whereHas('schedules' , function ($query) use ($dayName) {
                // Filter berdasarkan jadwal yang aktif pada hari ini
                $query->where('day_of_week', $dayName)->where('is_active', true);
            })
            // Tambahkan pengecekan schedule_overrides jika diperlukan nanti
            // ->whereDoesntHave('overrides', function ($query) {
            //     $query->where('override_date', Carbon::today()->toDateString())
            //           ->where('status', 'TIDAK_TERSEDIA');
            // })
            ->with('user')
            ->get()
            ->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)'
                ];
            });

        return response()->json($doctors);
    }
}
