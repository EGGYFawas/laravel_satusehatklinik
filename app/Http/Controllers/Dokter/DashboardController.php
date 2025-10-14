<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\PharmacyQueue;
use App\Models\Medicine;
use App\Models\DiagnosisTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        $today = Carbon::today();

        $allQueues = ClinicQueue::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('registration_time', $today)
            ->orderBy('check_in_time', 'asc') // Prioritaskan yang sudah check-in
            ->orderBy('queue_number', 'asc')
            ->get();

        // --- LOGIKA PEMISAHAN ANTRIAN YANG DIPERBAIKI ---
        $pasienSedangDipanggil = $allQueues->firstWhere('status', 'DIPANGGIL');
        $antreanHadir = $allQueues->where('status', 'HADIR');
        $antreanMenunggu = $allQueues->where('status', 'MENUNGGU');
        $antreanSelesai = $allQueues->whereIn('status', ['SELESAI', 'BATAL']);

        $patientHistory = null;
        $medicines = null;
        $diagnosisTags = null;

        if ($pasienSedangDipanggil) {
            $patientHistory = MedicalRecord::where('patient_id', $pasienSedangDipanggil->patient_id)
                                        ->orderBy('checkup_date', 'desc')->get();
            $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();
            $diagnosisTags = DiagnosisTag::orderBy('tag_name', 'asc')->get();
        }

        return view('dokter.dashboard', compact(
            'doctor',
            'pasienSedangDipanggil',
            'antreanHadir', // Pastikan variabel ini dikirim
            'antreanMenunggu',
            'antreanSelesai',
            'patientHistory',
            'medicines',
            'diagnosisTags'
        ));
    }

    public function panggilPasien(ClinicQueue $antrean)
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        
        if ($antrean->doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Anda tidak berhak memanggil antrean ini.');
        }
        
        // Dokter hanya bisa memanggil pasien yang sudah check-in (status 'HADIR')
        if ($antrean->status !== 'HADIR') {
            return redirect()->back()->with('error', 'Pasien belum melakukan check-in kehadiran.');
        }

        $isCallingAnother = ClinicQueue::where('doctor_id', $doctor->id)
                                        ->whereDate('registration_time', Carbon::today())
                                        ->where('status', 'DIPANGGIL')
                                        ->exists();

        if ($isCallingAnother) {
            return redirect()->back()->with('error', 'Selesaikan pemeriksaan pasien saat ini terlebih dahulu.');
        }

        $antrean->status = 'DIPANGGIL';
        $antrean->call_time = now();
        $antrean->save();

        return redirect()->route('dokter.dashboard')->with('success', "Pasien dengan nomor antrean {$antrean->queue_number} telah dipanggil.");
    }
    
    public function simpanPemeriksaan(Request $request, ClinicQueue $antrean)
    {
        $request->validate([ 'patient_id' => 'required|exists:patients,id', 'blood_type' => 'nullable|string|max:3', 'known_allergies' => 'nullable|string|max:500', 'chronic_diseases' => 'nullable|string|max:500', 'doctor_notes' => 'required|string|min:10', 'diagnosis_tags' => 'nullable|array', 'diagnosis_tags.*' => 'string|max:255', 'medicines' => 'nullable|array', 'medicines.*.id' => 'required|exists:medicines,id', 'medicines.*.quantity' => 'required|integer|min:1', 'medicines.*.dosage' => 'required|string|max:255', ]);
        DB::beginTransaction();
        try {
            $patient = Patient::findOrFail($request->patient_id);
            if ($request->filled('blood_type') || $request->filled('known_allergies') || $request->filled('chronic_diseases')) {
                $patient->update([ 'blood_type' => $patient->blood_type ?? $request->blood_type, 'known_allergies' => $patient->known_allergies ?? $request->known_allergies, 'chronic_diseases' => $patient->chronic_diseases ?? $request->chronic_diseases, ]);
            }
            $medicalRecord = MedicalRecord::create([ 'clinic_queue_id' => $antrean->id, 'patient_id' => $patient->id, 'doctor_id' => $antrean->doctor_id, 'checkup_date' => now(), 'doctor_notes' => $request->doctor_notes, ]);
            if ($request->has('diagnosis_tags')) {
                $tagIds = [];
                foreach ($request->diagnosis_tags as $tagName) {
                    $tag = DiagnosisTag::firstOrCreate(['tag_name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $medicalRecord->diagnosisTags()->sync($tagIds);
            }
            if ($request->has('medicines') && count($request->medicines) > 0) {
                $prescription = Prescription::create([ 'medical_record_id' => $medicalRecord->id, 'prescription_date' => now(), ]);
                foreach ($request->medicines as $med) {
                    PrescriptionDetail::create([ 'prescription_id' => $prescription->id, 'medicine_id' => $med['id'], 'quantity' => $med['quantity'], 'dosage' => $med['dosage'], ]);
                    $medicine = Medicine::find($med['id']);
                    $medicine->decrement('stock', $med['quantity']);
                }
                $lastPharmacyQueueCount = PharmacyQueue::whereDate('entry_time', Carbon::today())->count();
                $pharmacyQueueNumber = 'APT-' . str_pad($lastPharmacyQueueCount + 1, 3, '0', STR_PAD_LEFT);
                
                // [MODIFIKASI UTAMA] Mengubah status awal antrean apotek
                PharmacyQueue::create([ 
                    'clinic_queue_id' => $antrean->id, 
                    'prescription_id' => $prescription->id, 
                    'pharmacy_queue_number' => $pharmacyQueueNumber, 
                    'status' => 'DALAM_ANTREAN', // <-- Diubah dari 'MENUNGGU_RACIK'
                    'entry_time' => now(), 
                ]);
            }
            $antrean->status = 'SELESAI';
            
            $antrean->save();
            DB::commit();
            return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan selesai dan rekam medis berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pemeriksaan: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Terjadi kesalahan. Gagal menyimpan data pemeriksaan.');
        }
    }
}
