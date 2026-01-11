<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\ClinicQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar pasien yang terhubung dengan akun user
     * beserta tanggal kunjungan terakhir mereka.
     */
    public function index()
    {
        $user = Auth::user();
        $selfPatient = Patient::where('user_id', $user->id)->first();

        $familyPatientIds = ClinicQueue::where('registered_by_user_id', $user->id)
            ->whereNotNull('patient_id')
            ->where('patient_relationship', '!=', 'Diri Sendiri')
            ->distinct()
            ->pluck('patient_id');

        $familyPatients = Patient::whereIn('id', $familyPatientIds)
                            ->when($selfPatient, function ($query) use ($selfPatient) {
                                return $query->where('id', '!=', $selfPatient->id);
                            })
                            ->get();

        $allRelatedPatients = collect();
        if ($selfPatient) {
            $allRelatedPatients->push($selfPatient);
        }
        $allRelatedPatients = $allRelatedPatients->merge($familyPatients);

        $patientHistories = $allRelatedPatients->map(function ($patient) use ($user) {
            $latestQueue = ClinicQueue::where('patient_id', $patient->id)
                ->where('status', 'SELESAI')
                ->orderBy('finish_time', 'desc')
                ->first();

            $relationship = '-';
            if ($patient->user_id === $user->id) {
                $relationship = 'Diri Sendiri';
            } else {
                $queueRegisteredByUser = ClinicQueue::where('patient_id', $patient->id)
                                                ->where('registered_by_user_id', $user->id)
                                                ->orderBy('registration_time', 'desc')
                                                ->first();
                if ($queueRegisteredByUser) {
                    $relationship = $queueRegisteredByUser->patient_relationship === 'Lainnya'
                                            ? $queueRegisteredByUser->patient_relationship_custom
                                            : $queueRegisteredByUser->patient_relationship;
                }
            }

            return (object) [
                'patient_id' => $patient->id,
                'full_name' => $patient->full_name,
                'last_visit_date' => $latestQueue ? $latestQueue->finish_time : null,
                'relationship' => $relationship ?? 'N/A'
            ];
        })->filter(function ($item) {
            // [Optional] Hanya tampilkan pasien yang punya riwayat
            // return $item->last_visit_date !== null;
            return true; // Tampilkan semua pasien terkait
        })->sortByDesc('last_visit_date');

        return view('pasien.riwayat-daftar', compact('patientHistories'));
    }

    /**
     * Menampilkan detail riwayat kunjungan untuk satu pasien spesifik.
     */
    public function show(Patient $patient)
    {
        $user = Auth::user();

        // Validasi akses (Pastikan user ini berhak melihat riwayat pasien ini)
        $isSelf = $patient->user_id === $user->id;
        $isFamilyRegisteredByUser = ClinicQueue::where('registered_by_user_id', $user->id)
                                            ->where('patient_id', $patient->id)
                                            ->exists();

        if (!$isSelf && !$isFamilyRegisteredByUser) {
            abort(403, 'Anda tidak berhak mengakses riwayat pasien ini.');
        }

        // [MODIFIKASI UTAMA] Ambil semua riwayat kunjungan yang selesai
        // Eager load relasi yang dibutuhkan untuk detail termasuk ACTIONS
        $riwayatKunjungan = ClinicQueue::where('patient_id', $patient->id)
            ->where('status', 'SELESAI')
            ->with([
                'poli', // Data poli
                'doctor.user', // Data dokter
                'medicalRecord.diagnosisTags', // Data diagnosis tags
                'medicalRecord.actions', // [PENTING] Data Tindakan Medis
                'medicalRecord.prescription.prescriptionDetails.medicine' // [PENTING] Detail obat biar query efisien
            ])
            ->orderBy('finish_time', 'desc')
            ->get();

        // Ambil detail rekam medis dari kunjungan pertama (terbaru) untuk ditampilkan default
        // Model view akan menangani show/hide detail lainnya
        $medicalRecordDetail = $riwayatKunjungan->first()?->medicalRecord;

        return view('pasien.riwayat-detail', compact('patient', 'riwayatKunjungan', 'medicalRecordDetail'));
    }
}