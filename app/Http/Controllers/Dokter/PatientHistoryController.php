<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\ClinicQueue;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;

class PatientHistoryController extends Controller
{
    /**
     * Menampilkan halaman pencarian pasien atau daftar pasien terakhir.
     */
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor; // Asumsi relasi user->doctor sudah ada
        $searchQuery = $request->input('search');
        
        $patients = Patient::query()
            // Cari berdasarkan nama atau NIK
            ->when($searchQuery, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
            })
            // Hanya tampilkan pasien yang pernah ditangani oleh dokter ini (opsional tapi bagus)
            ->whereHas('clinicQueues', function ($q) use ($doctor) {
                 $q->where('doctor_id', $doctor->id)->where('status', 'SELESAI');
            })
            ->withCount(['clinicQueues' => function ($q) use ($doctor) {
                 $q->where('doctor_id', $doctor->id)->where('status', 'SELESAI');
            }]) // Hitung jumlah kunjungan
            ->orderBy('full_name', 'asc') // Urutkan berdasarkan nama
            ->paginate(15); // Batasi hasil per halaman

        return view('dokter.riwayat-pasien-index', compact('patients', 'searchQuery'));
    }

    /**
     * Menampilkan detail riwayat kunjungan lengkap untuk satu pasien.
     */
    public function show(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        // Validasi: Pastikan dokter ini pernah menangani pasien ini (opsional tapi aman)
        $hasTreated = ClinicQueue::where('doctor_id', $doctor->id)
                                  ->where('patient_id', $patient->id)
                                  ->where('status', 'SELESAI')
                                  ->exists();

        // if (!$hasTreated) {
        //     abort(403, 'Anda tidak memiliki riwayat pemeriksaan untuk pasien ini.');
        // }

        // Ambil semua riwayat kunjungan pasien ini yang sudah selesai
        // Eager load semua relasi yang dibutuhkan, termasuk detail resep
        $riwayatKunjungan = ClinicQueue::where('patient_id', $patient->id)
            ->where('status', 'SELESAI')
            ->with([
                'poli',
                'doctor.user',
                'medicalRecord' => function ($query) {
                    $query->with([
                        'diagnosisTags',
                        'prescription.prescriptionDetails.medicine' // Eager load sampai nama obat
                    ]);
                }
            ])
            ->orderBy('finish_time', 'desc') // Urutkan dari yang terbaru
            ->get();

        return view('dokter.riwayat-pasien-detail', compact('patient', 'riwayatKunjungan'));
    }
}
